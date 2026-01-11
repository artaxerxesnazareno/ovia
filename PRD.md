# PRD - Sistema de Orientação Vocacional com IA para IMETRO

## 1. VISÃO GERAL DO PRODUTO

### 1.1 Descrição
Sistema web de orientação vocacional inteligente que utiliza Large Language Models (LLMs) para analisar o perfil dos estudantes e recomendar cursos do IMETRO mais adequados às suas aptidões, interesses e valores, incluindo informações detalhadas sobre carreiras e roadmaps personalizados.

### 1.2 Problema a Resolver
- Ausência de ferramentas tecnológicas para orientação vocacional no IMETRO
- Dificuldade dos estudantes em escolher carreiras alinhadas com seus perfis
- Falta de informação estruturada sobre cursos e saídas profissionais
- Necessidade de orientação personalizada e escalável

### 1.3 Objetivos do Produto
- Fornecer avaliação vocacional baseada em IA
- Recomendar cursos do IMETRO com alta precisão
- Apresentar roadmaps de aprendizagem personalizados
- Reduzir evasão acadêmica através de escolhas mais assertivas

---

## 2. PERSONAS E STAKEHOLDERS

### 2.1 Usuários Principais
- **Estudantes do Ensino Médio**: Jovens (15-18 anos) em processo de escolha de carreira
- **Estudantes Ingressantes**: Novos alunos buscando confirmar escolha
- **Estudantes em Transição**: Alunos considerando mudança de curso

### 2.2 Stakeholders
- Coordenadores Acadêmicos do IMETRO
- Professores e Orientadores Educacionais
- Administração Institucional
- Departamento de TI

---

## 3. REQUISITOS FUNCIONAIS

### 3.1 Módulo de Cadastro e Autenticação

#### RF01 - Registro de Usuário
**Descrição**: Sistema deve permitir cadastro de novos usuários

**Campos Obrigatórios**:
- Nome completo
- Email institucional ou pessoal
- Senha (mínimo 8 caracteres)
- Data de nascimento
- Gênero (opcional)
- Telefone (opcional)

**Regras de Negócio**:
- Email único no sistema
- Validação de formato de email
- Senha com criptografia bcrypt
- Confirmação por email

#### RF02 - Login e Recuperação de Senha
- Login via email/senha
- Opção "Esqueci minha senha"
- Token de recuperação válido por 1 hora
- Logout seguro

### 3.2 Módulo de Avaliação Vocacional

#### RF03 - Questionário Multidimensional
**Descrição**: Sistema deve apresentar questionário estruturado em dimensões

**Dimensões Avaliadas**:

1. **Interesses Profissionais** (12 questões)
    - Áreas de conhecimento (Exatas, Humanas, Biológicas, Tecnologia)
    - Atividades preferidas
    - Ambientes de trabalho

2. **Habilidades e Aptidões** (10 questões)
    - Raciocínio lógico-matemático
    - Comunicação e expressão
    - Criatividade e inovação
    - Trabalho em equipe
    - Liderança

3. **Valores e Motivações** (8 questões)
    - Propósito profissional
    - Equilíbrio vida-trabalho
    - Remuneração
    - Impacto social
    - Estabilidade vs. Inovação

4. **Personalidade** (10 questões)
    - Introversão/Extroversão
    - Pensamento analítico/criativo
    - Prático/Teórico
    - Detalhista/Visionário

**Formato das Questões**:
- Escala Likert (1-5): "Discordo Totalmente" a "Concordo Totalmente"
- Questões de múltipla escolha com pesos
- Questões abertas para análise qualitativa (3 perguntas)

**Exemplo de Questões**:
```
1. Tenho facilidade em resolver problemas matemáticos complexos
   [1] [2] [3] [4] [5]

2. Prefiro trabalhar com:
   ( ) Números e dados
   ( ) Pessoas e relacionamentos
   ( ) Criação e design
   ( ) Tecnologia e sistemas

3. Descreva uma atividade que você realiza e sente-se completamente absorvido:
   [Campo de texto]
```

#### RF04 - Progresso e Salvamento
- Barra de progresso visual
- Salvamento automático a cada 3 respostas
- Possibilidade de pausar e continuar depois
- Tempo estimado de conclusão: 15-20 minutos

#### RF05 - Validação de Respostas
- Todas as questões obrigatórias
- Alerta visual para questões não respondidas
- Confirmação antes de submeter

### 3.3 Módulo de Processamento com LLM

#### RF06 - Integração com LLM (OpenAI GPT-4 ou Google Gemini)
**Descrição**: Sistema deve processar respostas usando IA para gerar análise profunda

**Fluxo de Processamento**:

1. **Coleta de Dados**
   ```php
   {
     "user_id": 123,
     "responses": {
       "interests": [...],
       "skills": [...],
       "values": [...],
       "personality": [...]
     },
     "open_responses": [...]
   }
   ```

2. **Prompt Engineering para LLM**
   ```
   Você é um especialista em orientação vocacional com 20 anos de experiência.
   
   DADOS DO ESTUDANTE:
   - Respostas objetivas: [JSON com respostas]
   - Respostas abertas: [texto do estudante]
   
   CURSOS DISPONÍVEIS NO IMETRO:
   [Lista de cursos com descrições]
   
   TAREFA:
   1. Analise o perfil psicológico e vocacional do estudante
   2. Identifique os 3 cursos mais adequados (ranking de 1-3)
   3. Para cada curso, forneça:
      - Score de compatibilidade (0-100)
      - Justificativa detalhada
      - Pontos fortes do estudante para este curso
      - Desafios potenciais
      - Saídas profissionais específicas
   4. Gere um roadmap de desenvolvimento personalizado
   
   FORMATO DE RESPOSTA: JSON estruturado
   ```

3. **Estrutura de Resposta da LLM**
   ```json
   {
     "profile_analysis": {
       "summary": "Texto descritivo",
       "strengths": ["habilidade1", "habilidade2"],
       "areas_to_develop": ["area1", "area2"],
       "personality_type": "Analítico-Criativo"
     },
     "course_recommendations": [
       {
         "course_id": 1,
         "course_name": "Ciência da Computação",
         "compatibility_score": 92,
         "rank": 1,
         "justification": "...",
         "student_strengths": ["Raciocínio lógico forte", "..."],
         "potential_challenges": ["Trabalho em equipe", "..."],
         "career_paths": [
           {
             "title": "Desenvolvedor Full-Stack",
             "description": "...",
             "average_salary": "...",
             "market_demand": "Alta"
           }
         ]
       }
     ],
     "roadmap": {
       "short_term": [...],
       "medium_term": [...],
       "long_term": [...]
     }
   }
   ```

#### RF07 - Fallback e Tratamento de Erros
- Timeout de 30 segundos para resposta da LLM
- Retry automático (até 3 tentativas)
- Fallback para análise baseada em regras se LLM falhar
- Log de erros para debugging

### 3.4 Módulo de Resultados e Recomendações

#### RF08 - Dashboard de Resultados
**Componentes da Interface**:

1. **Resumo do Perfil** (Card superior)
    - Avatar do usuário
    - Tipo de personalidade vocacional
    - Gráfico radar com dimensões avaliadas
    - Pontos fortes principais (top 5)

2. **Recomendações de Cursos** (Cards ordenados)

   Para cada curso recomendado:
   ```
   ┌─────────────────────────────────────────┐
   │ 🥇 CIÊNCIA DA COMPUTAÇÃO                │
   │ Compatibilidade: 92% [Barra visual]     │
   │                                         │
   │ "Seu perfil analítico e interesse..."  │
   │                                         │
   │ ✓ Seus pontos fortes:                  │
   │   • Raciocínio lógico                  │
   │   • Resolução de problemas             │
   │                                         │
   │ ⚠ Pontos de atenção:                   │
   │   • Desenvolver soft skills            │
   │                                         │
   │ [Ver Saídas Profissionais] [Roadmap]   │
   └─────────────────────────────────────────┘
   ```

#### RF09 - Detalhamento do Curso
**Ao clicar em um curso, modal/página com**:

- **Informações Gerais**
    - Duração (semestres)
    - Turno disponível
    - Grade curricular resumida
    - Pré-requisitos

- **Saídas Profissionais** (expandido)
    - Lista de 5-8 carreiras possíveis
    - Descrição de cada carreira
    - Faixa salarial estimada (Angola)
    - Demanda de mercado
    - Empresas/setores que contratam

- **Perfil do Profissional**
    - Habilidades necessárias
    - Ambiente de trabalho típico
    - Desafios da profissão

#### RF10 - Roadmap Personalizado
**Estrutura do Roadmap**:

```
ROADMAP DE DESENVOLVIMENTO - [Nome do Curso]

📅 CURTO PRAZO (0-6 meses - Antes de Ingressar)
┌─────────────────────────────────────┐
│ ✓ Fundamentos de Programação       │
│   • Python básico (Codecademy)     │
│   • 2-3 horas/semana               │
│                                     │
│ ✓ Matemática para Computação       │
│   • Khan Academy                    │
│   • Foco em álgebra e lógica       │
└─────────────────────────────────────┘

📅 MÉDIO PRAZO (6-18 meses - 1º/2º Semestres)
┌─────────────────────────────────────┐
│ ○ Desenvolvimento Web               │
│ ○ Estruturas de Dados               │
│ ○ Inglês Técnico                    │
└─────────────────────────────────────┘

📅 LONGO PRAZO (18+ meses - Formação)
┌─────────────────────────────────────┐
│ ○ Especialização (IA, Cloud, etc.)  │
│ ○ Projetos Portfolio                │
│ ○ Networking Profissional           │
└─────────────────────────────────────┘

🎯 RECURSOS RECOMENDADOS
• Cursos online: [links]
• Livros: [lista]
• Comunidades: [grupos]
• Certificações: [opções]
```

**Características**:
- Progresso rastreável (checkbox interativo)
- Links diretos para recursos
- Atualização semestral
- Exportável em PDF

#### RF11 - Comparação de Cursos
- Tabela comparativa lado a lado (até 3 cursos)
- Métricas: compatibilidade, duração, saídas profissionais, salário médio
- Highlighting de diferenças principais

### 3.5 Módulo Administrativo

#### RF12 - Gestão de Cursos (Admin)
**Funcionalidades**:
- CRUD de cursos do IMETRO
- Campos por curso:
    - Nome
    - Descrição completa
    - Coordenador
    - Duração (semestres)
    - Turnos disponíveis
    - Vagas/ano
    - Grade curricular (upload PDF ou texto)
    - Saídas profissionais (múltiplas entradas)
    - Status (ativo/inativo)

#### RF13 - Dashboard Administrativo
**Métricas Exibidas**:
- Total de avaliações realizadas
- Cursos mais recomendados
- Taxa de conversão (alunos que se matricularam)
- Tempo médio de avaliação
- Satisfação dos usuários (NPS)
- Gráficos de tendências

#### RF14 - Gestão de Questionário
- Editor de questões (adicionar/editar/remover)
- Reordenação de questões
- Pesos por dimensão
- Versionamento de questionários

#### RF15 - Exportação de Dados
- Relatórios em Excel/CSV
- Dados anonimizados para pesquisa
- Filtros por período, curso, perfil

---

## 4. REQUISITOS NÃO FUNCIONAIS

### 4.1 Performance
- **RNF01**: Tempo de resposta da LLM < 15 segundos
- **RNF02**: Carregamento de páginas < 2 segundos
- **RNF03**: Suporte a 100 usuários simultâneos
- **RNF04**: Cache de respostas LLM para otimização

### 4.2 Segurança
- **RNF05**: Criptografia de dados sensíveis (HTTPS)
- **RNF06**: LGPD compliance (Angola - Lei de Proteção de Dados)
- **RNF07**: Autenticação JWT com refresh tokens
- **RNF08**: Rate limiting em APIs (60 requisições/min)
- **RNF09**: Sanitização de inputs para prevenir XSS/SQL Injection

### 4.3 Usabilidade
- **RNF10**: Interface responsiva (mobile-first)
- **RNF11**: Acessibilidade WCAG 2.1 nível AA
- **RNF12**: Suporte a Português (Angola)
- **RNF13**: Feedback visual em todas as ações (loading, sucesso, erro)

### 4.4 Confiabilidade
- **RNF14**: Uptime de 99.5%
- **RNF15**: Backup diário automático
- **RNF16**: Logs estruturados de erros e auditoria

### 4.5 Manutenibilidade
- **RNF17**: Código seguindo PSR-12 (PHP Standards)
- **RNF18**: Cobertura de testes > 70%
- **RNF19**: Documentação de APIs (OpenAPI/Swagger)
- **RNF20**: Versionamento semântico (SemVer)

---

## 5. ARQUITETURA TÉCNICA

### 5.1 Stack Tecnológica

#### Backend
```
- Framework: Laravel 11.x
- PHP: 8.2+
- Banco de Dados: PostgreSQL 15 (relacional)
- Cache: Redis
- Queue: Laravel Queue (Redis driver)
- Storage: AWS S3 ou local (desenvolvimento)
```

#### Frontend
```
- Blade Templates + Alpine.js (interatividade)
- Tailwind CSS 3.x (estilização)
- Chart.js (gráficos)
- Axios (requisições HTTP)
```

#### APIs Externas
```
- OpenAI API (GPT-4 Turbo) OU
- Google Gemini API (Gemini 1.5 Pro)
- Configuração via .env para fácil troca
```

### 5.2 Arquitetura de Sistema

```
┌─────────────────────────────────────────────────┐
│              CAMADA DE APRESENTAÇÃO             │
│   (Blade Views + Alpine.js + Tailwind CSS)     │
└─────────────────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────┐
│               CAMADA DE APLICAÇÃO               │
│                                                 │
│  ┌──────────────┐  ┌──────────────┐            │
│  │ Controllers  │  │   Services   │            │
│  │              │  │              │            │
│  │ - Auth       │  │ - AssessmentService       │
│  │ - Assessment │  │ - LLMService              │
│  │ - Results    │  │ - RecommendationService   │
│  │ - Admin      │  │ - RoadmapService          │
│  └──────────────┘  └──────────────┘            │
│                                                 │
│  ┌──────────────────────────────────┐          │
│  │         Queue Jobs               │          │
│  │ - ProcessAssessmentJob           │          │
│  │ - GenerateRoadmapJob             │          │
│  └──────────────────────────────────┘          │
└─────────────────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────┐
│               CAMADA DE DOMÍNIO                 │
│                                                 │
│  Models: User, Assessment, Question, Course,    │
│          Recommendation, Roadmap                │
│                                                 │
│  Repositories: CourseRepository, etc.           │
└─────────────────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────┐
│            CAMADA DE INFRAESTRUTURA             │
│                                                 │
│  ┌──────────────┐  ┌──────────────┐            │
│  │ PostgreSQL   │  │  Redis Cache │            │
│  └──────────────┘  └──────────────┘            │
│                                                 │
│  ┌──────────────────────────────────┐          │
│  │      External Services           │          │
│  │  - OpenAI API / Gemini API       │          │
│  │  - Email (SMTP)                  │          │
│  └──────────────────────────────────┘          │
└─────────────────────────────────────────────────┘
```

### 5.3 Modelo de Dados (Principais Tabelas)

#### users
```sql
id, name, email, password, birth_date, gender, phone,
email_verified_at, remember_token, created_at, updated_at
```

#### assessments
```sql
id, user_id, status (pending|processing|completed|failed),
started_at, completed_at, processing_time_seconds,
created_at, updated_at
```

#### questions
```sql
id, category (interests|skills|values|personality),
question_text, question_type (likert|multiple|open),
weight, order, is_active, created_at, updated_at
```

#### assessment_responses
```sql
id, assessment_id, question_id, response_value,
response_text (para abertas), created_at
```

#### courses
```sql
id, name, slug, description, duration_semesters,
shifts (json: ['morning', 'evening']), vacancies_per_year,
coordinator_name, curriculum (json ou text), is_active,
created_at, updated_at
```

#### career_paths
```sql
id, course_id, title, description, average_salary_min,
average_salary_max, market_demand (low|medium|high),
created_at, updated_at
```

#### recommendations
```sql
id, assessment_id, course_id, rank (1, 2, 3),
compatibility_score (0-100), llm_analysis (json: completo),
justification (text), strengths (json), challenges (json),
created_at, updated_at
```

#### roadmaps
```sql
id, recommendation_id, short_term_goals (json),
medium_term_goals (json), long_term_goals (json),
resources (json), progress (json: {goal_id: completed}),
created_at, updated_at
```

#### assessment_prompts (versionamento)
```sql
id, version, system_prompt (text), user_prompt_template (text),
is_active, created_at
```

### 5.4 Integração com LLM - Detalhamento

#### Classe: `LLMService`

```php
<?php

namespace App\Services;

use OpenAI\Client as OpenAIClient;
use Google\Cloud\AIPlatform\V1\PredictionServiceClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LLMService
{
    private $provider; // 'openai' ou 'gemini'
    private $client;
    
    public function __construct()
    {
        $this->provider = config('llm.provider'); // openai ou gemini
        $this->initializeClient();
    }
    
    /**
     * Processa avaliação e retorna recomendações
     */
    public function processAssessment(
        array $responses, 
        array $openResponses, 
        array $availableCourses
    ): array {
        $prompt = $this->buildPrompt($responses, $openResponses, $availableCourses);
        
        // Cache key baseado em hash das respostas
        $cacheKey = 'llm_analysis_' . md5(json_encode($prompt));
        
        return Cache::remember($cacheKey, 3600, function() use ($prompt) {
            return $this->callLLM($prompt);
        });
    }
    
    /**
     * Construir prompt estruturado
     */
    private function buildPrompt(
        array $responses, 
        array $openResponses, 
        array $courses
    ): string {
        $systemPrompt = $this->getSystemPrompt();
        
        $userPrompt = "
DADOS DO ESTUDANTE:

## Respostas Objetivas (Escala 1-5):
" . $this->formatObjectiveResponses($responses) . "

## Respostas Abertas:
" . $this->formatOpenResponses($openResponses) . "

## Cursos Disponíveis:
" . $this->formatCourses($courses) . "

IMPORTANTE: Retorne APENAS um objeto JSON válido, sem nenhum texto adicional antes ou depois.
        ";
        
        return $systemPrompt . "\n\n" . $userPrompt;
    }
    
    /**
     * Chamar API da LLM
     */
    private function callLLM(string $prompt): array
    {
        try {
            if ($this->provider === 'openai') {
                return $this->callOpenAI($prompt);
            } else {
                return $this->callGemini($prompt);
            }
        } catch (\Exception $e) {
            Log::error('LLM API Error', [
                'provider' => $this->provider,
                'error' => $e->getMessage()
            ]);
            
            // Fallback para análise baseada em regras
            return $this->fallbackAnalysis($responses, $courses);
        }
    }
    
    /**
     * OpenAI Implementation
     */
    private function callOpenAI(string $prompt): array
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-4-turbo-preview',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.7,
            'max_tokens' => 4000,
        ]);
        
        $content = $response->choices[0]->message->content;
        return json_decode($content, true);
    }
    
    /**
     * Gemini Implementation
     */
    private function callGemini(string $prompt): array
    {
        // Implementação similar usando Gemini API
        $response = $this->client->generateContent([
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 4000,
            ]
        ]);
        
        $content = $response->text();
        return json_decode($content, true);
    }
    
    /**
     * Sistema de fallback se LLM falhar
     */
    private function fallbackAnalysis(array $responses, array $courses): array
    {
        // Lógica baseada em regras simples
        // Calcula scores com base em weights pré-definidos
        // Retorna estrutura JSON similar
    }
    
    // Métodos auxiliares...
    private function getSystemPrompt(): string { /* ... */ }
    private function formatObjectiveResponses(array $responses): string { /* ... */ }
    private function formatOpenResponses(array $openResponses): string { /* ... */ }
    private function formatCourses(array $courses): string { /* ... */ }
}
```

#### Configuração LLM (config/llm.php)

```php
<?php

return [
    'provider' => env('LLM_PROVIDER', 'openai'), // openai ou gemini
    
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4-turbo-preview'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 4000),
    ],
    
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-pro'),
        'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
    ],
    
    'timeout' => env('LLM_TIMEOUT', 30), // segundos
    'retry_attempts' => env('LLM_RETRY_ATTEMPTS', 3),
    'cache_ttl' => env('LLM_CACHE_TTL', 3600), // segundos
];
```

### 5.5 Jobs Assíncronos

#### ProcessAssessmentJob

```php
<?php

namespace App\Jobs;

use App\Models\Assessment;
use App\Services\LLMService;
use App\Services\RecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAssessmentJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    
    public $timeout = 60; // 1 minuto
    public $tries = 3;
    
    protected Assessment $assessment;
    
    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }
    
    public function handle(
        LLMService $llmService,
        RecommendationService $recommendationService
    ): void {
        $this->assessment->update(['status' => 'processing']);
        
        $startTime = now();
        
        try {
            // 1. Coletar respostas
            $responses = $this->assessment->responses()
                ->with('question')
                ->get()
                ->groupBy('question.category');
            
            $openResponses = $this->assessment->responses()
                ->whereHas('question', fn($q) => $q->where('question_type', 'open'))
                ->pluck('response_text')
                ->toArray();
            
            // 2. Buscar cursos ativos
            $courses = Course::active()->with('careerPaths')->get();
            
            // 3. Processar com LLM
            $llmResult = $llmService->processAssessment(
                $responses->toArray(),
                $openResponses,
                $courses->toArray()
            );
            
            // 4. Salvar recomendações
            $recommendationService->saveRecommendations(
                $this->assessment,
                $llmResult
            );
            
            // 5. Atualizar status
            $processingTime = now()->diffInSeconds($startTime);
            $this->assessment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'processing_time_seconds' => $processingTime
            ]);
            
            // 6. Enviar notificação por email (opcional)
            // Notification::send($this->assessment->user, new AssessmentCompleted());
            
        } catch (\Exception $e) {
            Log::error('Assessment Processing Failed', [
                'assessment_id' => $this->assessment->id,
                'error' => $e->getMessage()
            ]);
            
            $this->assessment->update(['status' => 'failed']);
            
            throw $e; // Para retry automático
        }
    }
}
```

---

## 6. FLUXO DE USUÁRIO DETALHADO

### 6.1 Fluxo Principal - Estudante

```
1. LANDING PAGE
   ↓
   [Entenda sua vocação] → Apresentação do sistema
   ↓
   [Começar Avaliação]

2. REGISTRO/LOGIN
   ↓
   Novo? → Formulário de cadastro → Email de confirmação
   Já tem conta? → Login direto
   ↓

3. QUESTIONÁRIO
   ↓
   Seção 1: Interesses (12 questões)
   ├─ Barra de progresso: 0-25%
   ├─ Salvamento automático
   ↓
   Seção 2: Habilidades (10 questões)
   ├─ Barra de progresso: 25-50%
   ↓
   Seção 3: Valores (8 questões)
   ├─ Barra de progresso: 50-75%
   ↓
   Seção 4: Personalidade (10 questões)
   ├─ Barra de progresso: 75-100%
   ↓
   [Confirmar e Enviar]

4. PROCESSAMENTO
   ↓
   Tela de loading (15-20 segundos)
   ├─ Animação
   ├─ Mensagens: "Analisando suas respostas..."
   │              "Comparando com cursos..."
   │              "Gerando recomendações..."
   ↓

5. RESULTADOS
   ↓
   Dashboard com:
   ├─ Perfil Vocacional (resumo + gráfico)
   ├─ Top 3 Cursos Recomendados
   │   ├─ Card 1 (Melhor match)
   │   ├─ Card 2
   │   └─ Car
Card 3
↓
[Explorar Curso] → Modal/Página detalhada
├─ Informações do curso
├─ Saídas profissionais (5-8 carreiras)
├─ [Ver Roadmap Personalizado]
↓

6. ROADMAP
   ↓
   Plano de desenvolvimento em 3 fases
   ├─ Recursos recomendados
   ├─ Progresso rastreável
   ├─ [Exportar PDF]
   └─ [Salvar no Perfil]

7. PÓS-AVALIAÇÃO
   ├─ Opção de refazer (após 30 dias)
   ├─ Feedback sobre recomendações
   └─ Compartilhar resultados (opcional)
```

### 6.2 Fluxo Administrativo

```
1. LOGIN ADMIN
   ↓
2. DASHBOARD
   ├─ Métricas gerais
   ├─ Gráficos
   ↓
3. GESTÃO DE CONTEÚDO
   ├─ Cursos
   │   ├─ [Adicionar Novo]
   │   ├─ [Editar Existente]
   │   └─ [Desativar]
   │
   ├─ Questionário
   │   ├─ [Editar Questões]
   │   ├─ [Ajustar Pesos]
   │   └─ [Versionar]
   │
   └─ Saídas Profissionais
   ├─ [Adicionar por Curso]
   └─ [Atualizar Salários]
   ↓
4. RELATÓRIOS
   ├─ Exportar dados
   ├─ Análises estatísticas
   └─ Feedback dos usuários
```

---

## 7. INTERFACE DO USUÁRIO (UI/UX)

### 7.1 Princípios de Design

- **Minimalista**: Foco no conteúdo, sem distrações
- **Progressivo**: Revelação gradual de informações
- **Responsivo**: Mobile-first, adaptável a tablets e desktops
- **Acessível**: Contraste adequado, navegação por teclado, screen readers
- **Feedback Constante**: Loading states, confirmações, validações inline

### 7.2 Paleta de Cores (Sugestão)

```
Primária:   #2563EB (Azul confiança)
Secundária: #10B981 (Verde sucesso)
Acento:     #F59E0B (Amarelo destaque)
Neutro:     #64748B (Cinza texto)
Fundo:      #F8FAFC (Branco off-white)
Erro:       #EF4444 (Vermelho)
```

### 7.3 Wireframes Principais (Descrição)

#### Página de Resultados

```
┌──────────────────────────────────────────────────┐
│  [Logo IMETRO]           Olá, João  [Menu ▼]     │
├──────────────────────────────────────────────────┤
│                                                  │
│  🎯 SEU PERFIL VOCACIONAL                        │
│  ┌────────────────────────────────────────────┐ │
│  │  [Avatar]  ANALÍTICO-CRIATIVO              │ │
│  │                                            │ │
│  │  Seus pontos fortes:                       │ │
│  │  ✓ Raciocínio lógico excepcional           │ │
│  │  ✓ Criatividade em resolução de problemas  │ │
│  │  ✓ Aprendizado autônomo                    │ │
│  │                                            │ │
│  │  [Gráfico Radar: 5 dimensões]             │ │
│  └────────────────────────────────────────────┘ │
│                                                  │
│  🏆 CURSOS RECOMENDADOS PARA VOCÊ                │
│                                                  │
│  ┌────────────────────────────────────────────┐ │
│  │ 🥇 1. CIÊNCIA DA COMPUTAÇÃO                │ │
│  │                                            │ │
│  │ Compatibilidade: 92%  [████████░░]         │ │
│  │                                            │ │
│  │ "Seu perfil analítico, combinado com...   │ │
│  │ interesse por tecnologia faz deste curso   │ │
│  │ uma escolha excepcionalmente adequada."    │ │
│  │                                            │ │
│  │ ✓ Por que você se destaca:                 │ │
│  │   • Forte raciocínio lógico-matemático     │ │
│  │   • Curiosidade por sistemas complexos     │ │
│  │                                            │ │
│  │ ⚠ Áreas a desenvolver:                     │ │
│  │   • Habilidades de comunicação em equipe   │ │
│  │   • Gestão de tempo em projetos            │ │
│  │                                            │ │
│  │ [Ver Detalhes] [Saídas Profissionais]     │ │
│  │                          [Ver Roadmap →]   │ │
│  └────────────────────────────────────────────┘ │
│                                                  │
│  ┌────────────────────────────────────────────┐ │
│  │ 🥈 2. ENGENHARIA DE SISTEMAS               │ │
│  │ Compatibilidade: 85%  [████████░░]         │ │
│  │ ...                                        │ │
│  └────────────────────────────────────────────┘ │
│                                                  │
│  ┌────────────────────────────────────────────┐ │
│  │ 🥉 3. CIÊNCIA DE DADOS                     │ │
│  │ Compatibilidade: 78%  [███████░░░]         │ │
│  │ ...                                        │ │
│  └────────────────────────────────────────────┘ │
│                                                  │
│  [Comparar Cursos] [Exportar Resultados PDF]    │
│                                                  │
└──────────────────────────────────────────────────┘
```

---

## 8. PLANO DE TESTES

### 8.1 Testes Unitários (PHPUnit)

```php
- LLMServiceTest
  ├─ testProcessAssessmentWithValidData()
  ├─ testFallbackWhenAPIFails()
  ├─ testCacheHitsForIdenticalResponses()
  └─ testTimeoutHandling()

- RecommendationServiceTest
  ├─ testSaveRecommendations()
  ├─ testRankingLogic()
  └─ testCompatibilityScoreCalculation()

- AssessmentTest (Model)
  ├─ testAssessmentCreation()
  ├─ testStatusTransitions()
  └─ testRelationships()
```

### 8.2 Testes de Integração

```
- Fluxo completo de avaliação
  ├─ Usuário se registra
  ├─ Responde questionário
  ├─ Sistema processa com LLM
  └─ Recebe recomendações

- Integração com LLM
  ├─ Mock de API OpenAI/Gemini
  ├─ Validação de estrutura JSON de resposta
  └─ Handling de erros HTTP
```

### 8.3 Testes de Aceitação (Laravel Dusk)

```
- E2E: Jornada do usuário
  ├─ Navegação completa do questionário
  ├─ Visualização de resultados
  └─ Interação com roadmap
```

### 8.4 Testes de Performance

```
- Load testing (Apache JMeter)
  ├─ 100 usuários simultâneos
  ├─ Tempo de resposta médio < 2s
  └─ Taxa de erro < 1%

- LLM stress test
  ├─ 50 requisições em 1 minuto
  └─ Validar rate limiting
```

### 8.5 Testes de Segurança

```
- OWASP Top 10
  ├─ SQL Injection
  ├─ XSS
  ├─ CSRF
  └─ Broken Authentication

- Penetration testing
  ├─ Ferramentas: OWASP ZAP, Burp Suite
  └─ Relatório de vulnerabilidades
```

---

## 9. CRONOGRAMA DE DESENVOLVIMENTO

### Fase 1: Planejamento e Setup (2 semanas)
- Semana 1:
    - Refinamento de requisitos
    - Setup do ambiente (Laravel, PostgreSQL, Redis)
    - Configuração de CI/CD
- Semana 2:
    - Design de banco de dados (migrations)
    - Estrutura de pastas e arquitetura
    - Setup de testes automatizados

### Fase 2: Desenvolvimento Core (6 semanas)
- Semanas 3-4: Autenticação e Questionário
    - Sistema de login/registro
    - CRUD de questões
    - Interface do questionário
    - Salvamento de respostas

- Semanas 5-6: Integração LLM
    - LLMService (OpenAI + Gemini)
    - Prompts engineering
    - Sistema de fallback
    - Jobs assíncronos
    - Testes de integração

- Semanas 7-8: Recomendações e Resultados
    - RecommendationService
    - Dashboard de resultados
    - Interface de saídas profissionais
    - Sistema de roadmap

### Fase 3: Features Administrativas (2 semanas)
- Semana 9:
    - CRUD de cursos
    - Dashboard administrativo
    - Métricas e relatórios

- Semana 10:
    - Exportação de dados
    - Gestão de questionários
    - Logs e auditoria

### Fase 4: Testes e Refinamento (3 semanas)
- Semana 11:
    - Testes com grupo piloto (20 usuários)
    - Coleta de feedback
    - Ajustes de UX

- Semana 12:
    - Testes de performance e segurança
    - Correção de bugs
    - Otimizações

- Semana 13:
    - Testes de aceitação finais
    - Documentação completa
    - Preparação para deploy

### Fase 5: Deploy e Monitoramento (1 semana)
- Semana 14:
    - Deploy em ambiente de produção
    - Configuração de monitoring (Laravel Telescope, Sentry)
    - Treinamento de administradores
    - Lançamento soft (beta)

**Total: 14 semanas (3,5 meses)**

---

## 10. MÉTRICAS DE SUCESSO (KPIs)

### 10.1 Métricas de Adoção
- **Usuários registrados**: Meta 200+ no primeiro semestre
- **Taxa de conclusão de avaliações**: > 80%
- **Tempo médio de avaliação**: 15-20 minutos

### 10.2 Métricas de Qualidade
- **NPS (Net Promoter Score)**: > 50
- **Satisfação com recomendações**: > 4/5 estrelas
- **Taxa de conversão** (alunos que se matricularam no curso recomendado): > 60%

### 10.3 Métricas Técnicas
- **Uptime**: > 99.5%
- **Tempo de resposta da LLM**: < 15 segundos
- **Taxa de erro**: < 1%
- **Cobertura de testes**: > 70%

### 10.4 Métricas de Impacto
- **Redução de evasão acadêmica**: Baseline vs. 6 meses após
- **Satisfação profissional** (pesquisa de follow-up após 1 ano): > 75%

---

## 11. RISCOS E MITIGAÇÕES

### 11.1 Riscos Técnicos

| Risco | Probabilidade | Impacto | Mitigação |
|-------|--------------|---------|-----------|
| API da LLM offline ou lenta | Média | Alto | Sistema de fallback com análise baseada em regras + cache agressivo |
| Custos elevados de API | Alta | Médio | Rate limiting + cache de respostas + monitoramento de uso |
| Vazamento de dados sensíveis | Baixa | Crítico | Criptografia end-to-end + auditorias de segurança + LGPD compliance |
| Performance ruim com muitos usuários | Média | Alto | Arquitetura escalável + CDN + otimização de queries |

### 11.2 Riscos de Negócio

| Risco | Probabilidade | Impacto | Mitigação |
|-------|--------------|---------|-----------|
| Baixa adoção pelos estudantes | Média | Alto | Campanhas de marketing interna + gamificação + UX impecável |
| Recomendações imprecisas | Média | Crítico | Validação com psicólogos vocacionais + feedback loop + ajustes de prompt |
| Resistência institucional | Baixa | Médio | Apresentações para stakeholders + piloto com pequeno grupo |

### 11.3 Riscos Regulatórios

| Risco | Probabilidade | Impacto | Mitigação |
|-------|--------------|---------|-----------|
| Não conformidade com LGPD (Angola) | Baixa | Alto | Consultoria jurídica + termo de consentimento claro + anonimização de dados |

---

## 12. DOCUMENTAÇÃO NECESSÁRIA

### 12.1 Para Desenvolvedores
- README.md (setup, instalação, contribuição)
- API documentation (OpenAPI/Swagger)
- Guia de arquitetura
- Convenções de código

### 12.2 Para Administradores
- Manual do administrador (PDF)
- Guia de gestão de cursos
- Interpretação de métricas

### 12.3 Para Usuários Finais
- FAQ
- Tutorial interativo (primeira vez)
- Vídeos explicativos

---

## 13. CONSIDERAÇÕES FINAIS

### 13.1 Escalabilidade Futura
- **Multi-instituição**: Adaptar para outras universidades além do IMETRO
- **Mobile App**: Versão nativa para iOS/Android
- **Multilíngue**: Suporte a Inglês, Francês (Áfricanização)
- **Gamificação**: Badges, rankings, desafios

### 13.2 Integrações Futuras
- Sistema acadêmico do IMETRO (matrícula direta)
- Plataformas de aprendizado (Coursera, Udemy) para roadmaps
- LinkedIn para networking profissional
- Empresas parceiras (estágios e empregos)

### 13.3 Pesquisa e Melhoria Contínua
- A/B testing de diferentes prompts
- Machine Learning próprio (treinar modelo customizado com dados coletados)
- Análise preditiva de sucesso acadêmico

---

## 14. ANEXOS

### Anexo A: Exemplo de Prompt Completo para LLM

```
CONTEXTO:
Você é um especialista em orientação vocacional com 20 anos de experiência trabalhando com jovens em instituições de ensino superior em Angola. Sua expertise inclui psicologia vocacional, análise de mercado de trabalho e pedagogia.

DADOS DO ESTUDANTE:

## Perfil Demográfico:
- Idade: 17 anos
- Gênero: Masculino
- Localização: Luanda, Angola

## Respostas Objetivas (Escala 1-5):

### INTERESSES PROFISSIONAIS:
1. Tenho facilidade em resolver problemas matemáticos complexos: 5
2. Gosto de trabalhar com tecnologia e computadores: 5
3. Prefiro atividades que envolvem criatividade e inovação: 4
4. Interesso-me por entender como as coisas funcionam: 5
5. Gosto de trabalhar com dados e análises: 4
... (continua)

### HABILIDADES E APTIDÕES:
1. Consigo aprender novas tecnologias rapidamente: 5
2. Tenho facilidade em comunicar ideias complexas: 3
3. Trabalho bem em equipe: 3
... (continua)

### VALORES E MOTIVAÇÕES:
1. Valorizo um salário alto: 4
2. Quero fazer diferença na sociedade: 4
3. Prefiro estabilidade a riscos: 2
... (continua)

### PERSONALIDADE:
1. Prefiro trabalhar sozinho do que em grupo: 4
2. Sou mais prático do que teórico: 3
... (continua)

## Respostas Abertas:

1. "Descreva uma atividade que você realiza e sente-se completamente absorvido":
   "Quando estou programando ou resolvendo desafios de lógica, perco a noção do tempo. Também gosto de criar jogos simples e ver eles funcionando."

2. "Qual profissional você admira e por quê?":
   "Admiro Elon Musk pela sua capacidade de inovar e pensar grande. Também admiro professores que conseguem explicar coisas difíceis de forma simples."

3. "Como você se vê daqui a 10 anos?":
   "Me vejo trabalhando numa empresa de tecnologia, talvez desenvolvendo soluções que ajudem pessoas em Angola. Quero ter minha própria startup um dia."

## Cursos Disponíveis no IMETRO:

1. **Ciência da Computação**
   - Duração: 8 semestres
   - Descrição: Formação em desenvolvimento de software, algoritmos, estruturas de dados, inteligência artificial, banco de dados e engenharia de software.
   - Saídas Profissionais: Desenvolvedor de Software, Engenheiro de Dados, Cientista de Dados, Arquiteto de Soluções, Consultor de TI
   - Perfil: Raciocínio lógico forte, interesse por tecnologia, capacidade analítica

2. **Engenharia de Sistemas**
   - Duração: 10 semestres
   - Descrição: Foco em infraestrutura, redes, segurança da informação e gestão de sistemas complexos.
   - Saídas Profissionais: Engenheiro de Redes, Administrador de Sistemas, Especialista em Segurança, Gerente de TI
   - Perfil: Visão sistêmica, interesse por infraestrutura, resolução de problemas

3. **Design Gráfico**
   - Duração: 6 semestres
   - Descrição: Criação visual, branding, UX/UI design, motion graphics.
   - Saídas Profissionais: Designer Gráfico, UX/UI Designer, Diretor de Arte, Motion Designer
   - Perfil: Criatividade, senso estético, comunicação visual

... (continua para todos os 10 cursos do IMETRO)

TAREFA:
Analise profundamente o perfil deste estudante e forneça recomendações de cursos do IMETRO. Sua análise deve ser:
1. Baseada em evidências das respostas
2. Culturalmente sensível ao contexto angolano
3. Honesta sobre pontos fortes E desafios
4. Focada no desenvolvimento de longo prazo

Retorne APENAS um objeto JSON válido com a seguinte estrutura:

{
  "profile_analysis": {
    "summary": "Texto descritivo de 3-4 frases sobre o perfil vocacional do estudante",
    "personality_type": "Etiqueta descritiva (ex: Analítico-Criativo)",
    "strengths": ["força 1", "força 2", "força 3"],
    "areas_to_develop": ["área 1", "área 2"],
    "career_values": ["valor 1", "valor 2"]
  },
  "course_recommendations": [
    {
      "course_name": "Nome exato do curso",
      "rank": 1,
      "compatibility_score": 92,
      "justification": "Parágrafo de 4-5 frases explicando POR QUE este curso se encaixa, citando respostas específicas",
      "student_strengths_for_course": [
        "Raciocínio lógico excepcional demonstrado nas respostas matemáticas",
        "Forte interesse por tecnologia e aprendizado autônomo"
      ],
      "potential_challenges": [
        "Desenvolver habilidades de trabalho em equipe (resposta baixa na questão X)",
        "Comunicação de ideias técnicas para públicos não-técnicos"
      ],
      "career_paths": [
        {
          "title": "Desenvolvedor Full-Stack",
          "description": "Profissional que trabalha tanto no front-end quanto no back-end de aplicações web",
          "average_salary_range": "150.000 - 400.000 Kz/mês (Angola)",
          "market_demand": "Alta",
          "key_skills": ["JavaScript", "Python", "SQL", "APIs"],
          "growth_potential": "Excelente - setor em expansão em Angola"
        },
        {
          "title": "Engenheiro de Inteligência Artificial",
          "description": "Desenvolvimento de sistemas inteligentes, machine learning e automação",
          "average_salary_range": "300.000 - 600.000 Kz/mês",
          "market_demand": "Crescente",
          "key_skills": ["Python", "TensorFlow", "Estatística", "Algoritmos"],
          "growth_potential": "Muito alta - área emergente"
        }
        // ... até 5-8 carreiras
      ]
    },
    // Curso 2 (rank 2)
    // Curso 3 (rank 3)
  ],
  "roadmap": {
    "short_term": [
      {
        "goal": "Fundamentos de Programação",
        "timeframe": "0-6 meses (antes de ingressar)",
        "actions": [
          "Completar curso 'Python para Iniciantes' na Codecademy",
          "Resolver 50 problemas no HackerRank (fácil/médio)",
          "Criar 3 projetos pessoais simples"
        ],
        "resources": [
          "https://www.codecademy.com/learn/learn-python-3",
          "https://www.hackerrank.com/domains/python"
        ],
        "estimated_hours_week": "5-7 horas"
      },
      {
        "goal": "Matemática para Computação",
        "timeframe": "0-6 meses",
        "actions": [
          "Revisar álgebra linear (Khan Academy)",
          "Estudar lógica proposicional"
        ],
        "resources": ["https://www.khanacademy.org/math/linear-algebra"],
        "estimated_hours_week": "3-4 horas"
      }
    ],
    "medium_term": [
      {
        "goal": "Desenvolvimento Web",
        "timeframe": "6-18 meses (1º e 2º semestres)",
        "actions": [
          "Dominar HTML, CSS, JavaScript",
          "Aprender React ou Vue.js",
          "Construir portfolio online com 5 projetos"
        ],
        "resources": ["MDN Web Docs", "freeCodeCamp"],
        "estimated_hours_week": "10-12 horas (inclui aulas)"
      },
      {
        "goal": "Estruturas de Dados e Algoritmos",
        "timeframe": "6-18 meses",
        "actions": [
          "Estudar livro 'Introduction to Algorithms'",
          "Participar de competições de programação"
        ],
        "resources": ["Livro: Cormen et al."],
        "estimated_hours_week": "6-8 horas"
      },
      {
        "goal": "Soft Skills",
        "timeframe": "6-18 meses",
        "actions": [
          "Participar de grupos de estudo",
          "Apresentar projetos em seminários",
          "Contribuir em projetos open-source"
        ],
        "estimated_hours_week": "2-3 horas"
      }
    ],
    "long_term": [
      {
        "goal": "Especialização",
        "timeframe": "18+ meses (3º semestre em diante)",
        "actions": [
          "Escolher área de especialização (IA, Cloud, Mobile, etc.)",
          "Realizar estágio em empresa de tecnologia",
          "Iniciar TCC em área de interesse"
        ],
        "resources": ["Coursera Specializations", "AWS Academy"],
        "estimated_hours_week": "15+ horas"
      },
      {
        "goal": "Networking e Carreira",
        "timeframe": "18+ meses",
        "actions": [
          "Participar de eventos de tecnologia em Luanda",
          "Conectar-se com profissionais no LinkedIn",
          "Considerar freelancing ou startup própria"
        ],
        "resources": ["Meetup.com", "LinkedIn Learning"],
        "estimated_hours_week": "2-4 horas"
      }
    ],
    "certifications_to_consider": [
      "AWS Certified Cloud Practitioner",
      "Google IT Support Professional Certificate",
      "Microsoft Certified: Azure Fundamentals"
    ],
    "books_recommended": [
      "Clean Code - Robert C. Martin",
      "The Pragmatic Programmer - Hunt & Thomas",
      "Cracking the Coding Interview - Gayle McDowell"
    ],
    "communities_to_join": [
      "Stack Overflow",
      "GitHub (contribuir em open-source)",
      "Dev.to",
      "Comunidades de desenvolvedores em Angola (Facebook groups)"
    ]
  },
  "additional_advice": {
    "immediate_next_steps": "Texto com 3-4 ações concretas que o estudante pode fazer HOJE para começar sua jornada",
    "long_term_vision": "Texto inspiracional sobre como este caminho pode levá-lo aos seus objetivos de 10 anos",
    "words_of_encouragement": "Mensagem personalizada reconhecendo seus pontos fortes e potencial"
  }
}

IMPORTANTE:
- Seja específico e detalhado
- Cite evidências das respostas do estudante
- Considere o contexto do mercado de trabalho angolano
- Seja honesto mas encorajador
- Retorne APENAS JSON válido, sem texto antes ou depois
```

---

## 15. GLOSSÁRIO TÉCNICO

- **LLM (Large Language Model)**: Modelo de IA treinado em grandes volumes de texto
- **Prompt Engineering**: Técnica de otimização de instruções para LLMs
- **Fallback**: Sistema alternativo quando o principal falha
- **Rate Limiting**: Limitação de número de requisições por tempo
- **JWT (JSON Web Token)**: Padrão de autenticação baseado em tokens
- **CRUD**: Create, Read, Update, Delete (operações básicas de dados)
- **API**: Interface de Programação de Aplicações
- **Webhook**: Notificação HTTP automática entre sistemas
- **CI/CD**: Integração Contínua / Entrega Contínua
- **LGPD**: Lei Geral de Proteção de Dados (Angola/Brasil)

---

**Documento preparado para**: Artaxerxes Nazareno de Maria Lídia  
**Instituição**: Instituto Superior Politécnico Metropolitano de Angola (IMETRO)  
**Orientador**: Professor Eduardo Vantuva  
**Data**: Janeiro 2025  
**Versão**: 1.0

---

Este PRD fornece uma base sólida e completa para o desenvolvimento da monografia e implementação do sistema. Recomendo que seja revisado com o orientador e ajustado conforme feedback. Boa sorte com o projeto! 🚀
