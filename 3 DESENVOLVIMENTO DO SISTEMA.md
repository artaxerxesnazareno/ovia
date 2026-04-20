# 3 DESENVOLVIMENTO DO SISTEMA

Este capítulo descreve a construção do OVIA com base na implementação existente no projeto. O sistema foi desenvolvido para apoiar a orientação vocacional de estudantes, integrando questionário estruturado, processamento assíncrono e recomendação de cursos com Inteligência Artificial.

## 3.1 ARQUITECTURA DO SISTEMA OVIA

A arquitectura do OVIA segue uma abordagem em camadas:

- Camada de apresentação: páginas React/TypeScript renderizadas por Inertia.js.
- Camada de aplicação: controladores Laravel responsáveis por validação, orquestração de fluxo e regras de navegação.
- Camada de domínio/serviços: geração de prompt (`AssessmentPromptService`), integração LLM (`LLMService`) e persistência das recomendações (`RecommendationService`).
- Camada de dados: modelos Eloquent e base de dados relacional com entidades de avaliação, respostas, cursos, recomendações e roadmap.
- Camada assíncrona: fila de jobs (`ProcessAssessmentJob`) para processar a análise por IA sem bloquear a experiência do utilizador.

O fluxo principal inicia no módulo de avaliação, passa pela submissão das respostas, aciona processamento em background e termina com a disponibilização de resultados e plano de evolução profissional.

### Diagrama de Caso de Uso

```plantuml
@startuml
left to right direction
skinparam packageStyle rectangle
title OVIA - Diagrama de Caso de Uso

actor "Visitante" as Guest
actor "Utilizador" as User
actor "Administrador" as Admin
actor "Worker de Fila" as Worker
actor "Provedor LLM\n(Gemini/DeepSeek)" as LLM

User --|> Guest
Admin --|> User

rectangle "Sistema OVIA" {
  usecase "Registar e autenticar" as UCAuth
  usecase "Gerir perfil" as UCProfile
  usecase "Iniciar/continuar avaliação" as UCStart
  usecase "Responder questionário" as UCAnswer
  usecase "Salvar respostas automaticamente" as UCAutosave
  usecase "Submeter avaliação" as UCSubmit
  usecase "Acompanhar processamento" as UCTrack
  usecase "Visualizar resultados" as UCResults
  usecase "Visualizar detalhes do curso" as UCCourse
  usecase "Aceder dashboard administrativo" as UCAdmin

  usecase "Processar avaliação em background" as UCProcess
  usecase "Construir prompt vocacional" as UCPrompt
  usecase "Gerar análise por IA" as UCAI
  usecase "Persistir recomendações e roadmap" as UCPersist
}

Guest --> UCAuth
User --> UCProfile
User --> UCStart
User --> UCAnswer
User --> UCSubmit
User --> UCTrack
User --> UCResults
User --> UCCourse
Admin --> UCAdmin

UCAnswer .> UCAutosave : <<include>>
UCSubmit .> UCProcess : <<include>>
UCProcess .> UCPrompt : <<include>>
UCProcess .> UCAI : <<include>>
UCProcess .> UCPersist : <<include>>
UCCourse .> UCResults : <<extend>>

Worker --> UCProcess
LLM --> UCAI
@enduml
```

### Diagrama de Base de Dados

```plantuml
@startuml
hide circle
skinparam linetype ortho
title OVIA - Diagrama de Base de Dados

entity "users" as users {
  *id : bigint <<PK>>
  --
  name : varchar
  email : varchar <<UK>>
  password : varchar
  birth_date : date
  gender : enum
  phone : varchar
  role : enum(admin,user)
}

entity "assessments" as assessments {
  *id : bigint <<PK>>
  *user_id : bigint <<FK>>
  --
  status : enum
  started_at : timestamp
  completed_at : timestamp
  processing_time_seconds : int
}

entity "questions" as questions {
  *id : bigint <<PK>>
  --
  category : enum
  dimension : varchar
  question_text : text
  question_type : enum
  options : json
  weight : decimal
  "order" : int
  is_required : boolean
  is_active : boolean
}

entity "assessment_responses" as responses {
  *id : bigint <<PK>>
  *assessment_id : bigint <<FK>>
  *question_id : bigint <<FK>>
  --
  response_value : int
  response_text : text
}

entity "courses" as courses {
  *id : bigint <<PK>>
  --
  name : varchar
  slug : varchar <<UK>>
  description : text
  duration_semesters : int
  shifts : json
  vacancies_per_year : int
  coordinator_name : varchar
  curriculum : text/json
  admission_requirements : text
  is_active : boolean
}

entity "career_paths" as career_paths {
  *id : bigint <<PK>>
  *course_id : bigint <<FK>>
  --
  title : varchar
  description : text
  average_salary_min : decimal
  average_salary_max : decimal
  market_demand : enum
  key_skills : json
  growth_potential : json
}

entity "recommendations" as recommendations {
  *id : bigint <<PK>>
  *assessment_id : bigint <<FK>>
  *course_id : bigint <<FK>>
  --
  rank : int
  compatibility_score : decimal
  llm_analysis : json
  justification : text
  strengths : json
  challenges : json
}

entity "roadmaps" as roadmaps {
  *id : bigint <<PK>>
  *recommendation_id : bigint <<FK>>
  --
  short_term_goals : json
  medium_term_goals : json
  long_term_goals : json
  resources : json
  certifications : json
  books : json
  communities : json
  progress : json
}

entity "assessment_prompts" as prompts {
  *id : bigint <<PK>>
  --
  version : varchar <<UK>>
  system_prompt : text
  user_prompt_template : text
  parameters : json
  is_active : boolean
}

users ||--o{ assessments : possui
assessments ||--o{ responses : contem
questions ||--o{ responses : responde
assessments ||--o{ recommendations : gera
courses ||--o{ recommendations : referencia
courses ||--o{ career_paths : possui
recommendations ||--|| roadmaps : detalha
@enduml
```

## 3.2 TECNOLOGIAS UTILIZADAS

As tecnologias identificadas no projeto e sua função no sistema são:

- Laravel 12.21.0: núcleo backend, roteamento, autenticação, ORM Eloquent, filas e validação.
- PHP: implementação dos controladores, serviços de negócio e job assíncrono.
- SQLite (ambiente local): persistência principal de dados.
- Inertia.js 2 + React 19 + TypeScript: interface dinâmica e integração SPA sem abandonar o backend Laravel.
- Tailwind CSS 3 + Radix UI + componentes utilitários: construção da interface com foco em produtividade.
- Vite 7: build e hot reload do frontend.
- HTTP Client do Laravel: comunicação com provedores LLM.
- Provedores LLM (Gemini e DeepSeek): geração da análise vocacional e recomendações.
- Queue (driver `database`): execução assíncrona do `ProcessAssessmentJob`.
- Pest/PHPUnit: cobertura de testes para fluxo de avaliação, processamento e robustez do motor de IA.

## 3.3 IMPLEMENTAÇÃO DAS FUNCIONALIDADES

As funcionalidades foram implementadas em fluxo contínuo:

1. O utilizador inicia ou retoma uma avaliação (`/assessment/start` e `/assessment/create`).
2. O questionário é carregado por categoria e as respostas são salvas automaticamente (`/assessment/{id}/save`).
3. Na submissão (`/assessment/{id}/submit`), o sistema valida respostas obrigatórias e muda o estado para `processing`.
4. O job de processamento é enviado para fila e executa o pipeline de IA.
5. A página de processamento consulta periodicamente o estado (`/assessment/{id}/status`).
6. Ao concluir, os resultados e detalhes de curso são exibidos ao utilizador.

O sistema também trata cenários de conflito e recuperação, como avaliações já concluídas, em processamento ou com falha, encaminhando o utilizador para a rota correta.

### Diagrama de Sequência

```plantuml
@startuml
title OVIA - Diagrama de Sequência (Submissão e Processamento)

actor Utilizador
participant "UI (Questions.tsx)" as UI
participant "AssessmentController" as Controller
database "Base de Dados" as DB
participant "Fila" as Queue
participant "ProcessAssessmentJob" as Job
participant "AssessmentPromptService" as PromptService
participant "LLMService" as LLMService
participant "Provedor LLM" as LLM
participant "RecommendationService" as RecService
participant "UI (Processing.tsx)" as ProcessingUI

Utilizador -> UI : Preenche respostas
UI -> Controller : POST /assessment/{id}/save
Controller -> DB : upsert assessment_responses
Controller --> UI : { success: true }

Utilizador -> UI : Enviar avaliação
UI -> Controller : POST /assessment/{id}/submit
Controller -> DB : validar obrigatórias + status=processing
Controller -> Queue : dispatch ProcessAssessmentJob
Controller --> UI : redirect /assessment/{id}/processing

loop Polling
ProcessingUI -> Controller : GET /assessment/{id}/status
Controller -> DB : consultar status
Controller --> ProcessingUI : processing/completed/failed
end

Queue -> Job : executar
Job -> DB : carregar avaliação + respostas
Job -> PromptService : buildPrompt(assessment)
PromptService --> Job : prompt estruturado
Job -> LLMService : generateAssessmentJson(prompt)
LLMService -> LLM : request JSON
LLM --> LLMService : resposta
LLMService --> Job : JSON validado
Job -> RecService : saveRecommendations(assessment, json)
RecService -> DB : salvar recommendations + roadmaps
Job -> DB : status=completed, completed_at, tempo processamento
@enduml
```

### Diagrama de Atividade

```plantuml
@startuml
title OVIA - Diagrama de Atividade (Jornada da Avaliação)

start
:Autenticar utilizador;
:Abrir página inicial da avaliação;

if (Existe avaliação pendente?) then (Sim)
  :Continuar avaliação existente;
else (Não)
  :Criar nova avaliação;
endif

:Responder questões por categoria;
:Auto-save das respostas;

if (Submeter avaliação?) then (Sim)
  :Validar questões obrigatórias;
  if (Há questões em falta?) then (Sim)
    :Mostrar erro e manter no questionário;
    :Retornar ao preenchimento das respostas;
  else (Não)
    :Atualizar estado para processing;
    :Despachar job para fila;
    :Mostrar página de processamento;
    while (status != completed) is (A processar)
      :Polling de estado;
      if (status == failed) then (Falhou)
        :Mostrar mensagem de falha e opção de retry;
        stop
      endif
    endwhile (Concluído)
    :Carregar resultados e roadmap;
    :Exibir recomendações e detalhes do curso;
  endif
else (Não)
  :Guardar progresso para retoma futura;
endif

stop
@enduml
```

## 3.4 MÓDULO DE AVALIAÇÃO VOCACIONAL

O módulo de avaliação vocacional foi estruturado em quatro categorias centrais: interesses, habilidades, valores e personalidade. As questões suportam três tipos de resposta:

- Likert (escala numérica de 1 a 5).
- Múltipla escolha.
- Resposta aberta.

Do ponto de vista funcional, o módulo apresenta:

- Cálculo de progresso em tempo real.
- Navegação por secções/categorias.
- Persistência automática (auto-save) para reduzir perda de dados.
- Validação de questões obrigatórias antes da submissão.
- Retoma de avaliação pendente para continuidade da experiência.

Essa implementação aumenta a confiabilidade do processo e melhora a qualidade dos dados enviados ao motor de recomendação.

## 3.5 MOTOR DE RECOMENDAÇÃO COM IA

O motor de recomendação foi implementado com pipeline de processamento assíncrono:

1. `AssessmentPromptService` monta um prompt contextual com dados demográficos, respostas do questionário e catálogo de cursos ativos.
2. `LLMService` envia o prompt ao provedor configurado (Gemini ou DeepSeek) e exige resposta em JSON estruturado.
3. O JSON retornado passa por validação e tratamento de erros (incluindo recuperação de formato inválido).
4. `RecommendationService` faz o mapeamento das recomendações para cursos existentes, limita resultados aos 3 melhores cursos e persiste recomendações + roadmap.
5. O `ProcessAssessmentJob` atualiza o estado final da avaliação (`completed` ou `failed`) e regista métricas de processamento.

O desenho do motor privilegia robustez operacional:

- Execução em fila para não bloquear o frontend.
- Estratégia de retry e timeout nas chamadas à IA.
- Logs detalhados para diagnóstico.
- Testes automatizados para cenários de sucesso e falha.

## 3.6 INTERFACE DO UTILIZADOR

A interface do OVIA foi construída para acompanhar toda a jornada do estudante:

- Página de início da avaliação com explicação do processo e estimativa de tempo.
- Questionário com UX orientada a continuidade (progress bar, estados de auto-save e navegação por secções).
- Tela de processamento com feedback visual, passos simulados e polling de estado.
- Página de resultados com perfil, recomendações, justificativas e visualizações de competências.
- Página de detalhes do curso com saídas profissionais, requisitos de admissão, tópicos curriculares e roadmap por horizonte temporal.
- Dashboard pessoal com histórico de avaliações, recomendações recentes e tarefas de progresso.

A combinação de Inertia.js com React permitiu uma experiência fluida, mantendo no backend a segurança, validação e controlo de regras de negócio.
