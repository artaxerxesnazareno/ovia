# 🎯 PLANO PERSONAL SCRUM - ASSISTENTE VOCACIONAL IA
## Duração: 6 semanas (1 mês e 2 semanas)

---

## 📋 VISÃO GERAL DO PROJETO

**Objetivo**: Desenvolver MVP funcional do sistema de orientação vocacional com IA para o IMETRO

**Definição de Pronto (DoD)**:
- ✅ Código commitado no repositório Git
- ✅ Testes básicos implementados e passando
- ✅ Funcionalidade testada manualmente
- ✅ Documentação básica atualizada
- ✅ Deploy realizado (se aplicável)

**Horário de Trabalho**:
- Segunda a Sexta: 4 horas/dia (após aulas)
- Sábado: 6 horas
- Domingo: 4 horas (revisão/estudo)
- **Total**: 34 horas/semana

---

## 🗓️ ESTRUTURA DE SPRINTS

### 📊 Métricas Pessoais
- **Velocity inicial estimada**: 20 story points/sprint
- **Buffer para imprevistos**: 20% do tempo
- **Review semanal**: Domingo às 18h
- **Retrospectiva**: Domingo às 19h

---

## 🚀 SPRINT 1: Setup e Fundações (Semana 1)
**Objetivo**: Ambiente configurado + Autenticação funcionando + Estrutura de BD

### 📅 Planejamento - Segunda (2h)
**Story Points Total: 21 SP**

#### User Stories

**US1.1 - Setup do Ambiente de Desenvolvimento** (5 SP)
- [x] Instalar Laravel 11.x, PostgreSQL, Redis
- [x] Configurar Docker (opcional, mas recomendado)
- [x] Setup do repositório Git (GitHub/GitLab)
- [x] Configurar .env e variáveis de ambiente
- [x] Testar conexão com banco de dados
- **Critério de Aceitação**: `php artisan serve` roda sem erros

**US1.2 - Estrutura de Banco de Dados** (5 SP)
- [x] Criar migrations para todas as tabelas principais
    - users, assessments, questions, courses, recommendations, roadmaps
- [ ] Criar seeders para dados de teste
    - 10 cursos do IMETRO
    - 40 questões (10 por categoria)
- [ ] Executar migrations e seeds
- **Critério de Aceitação**: BD populado com dados de teste

**US1.3 - Sistema de Autenticação** (8 SP)
- [ ] Implementar registro de usuário
- [ ] Implementar login/logout
- [ ] Criar middleware de autenticação
- [ ] Páginas: /register, /login, /dashboard
- [ ] Validações de formulário
- [ ] Flash messages (sucesso/erro)
- **Critério de Aceitação**: Usuário consegue se registrar, fazer login e acessar dashboard protegido

**US1.4 - Layout Base e UI Foundation** (3 SP)
- [ ] Instalar e configurar Tailwind CSS
- [ ] Criar layout master (app.blade.php)
- [ ] Componentes base: navbar, footer, alerts
- [ ] Página inicial (landing page simples)
- **Critério de Aceitação**: Layout responsivo funcionando

### 📝 Tarefas Diárias

**Segunda (4h)** - Planejamento + Setup
```
☐ Cerimônia de planejamento (2h)
☐ Instalar Laravel e dependências (1h)
☐ Configurar Git e primeiro commit (1h)
```

**Terça (4h)** - Banco de Dados
```
☐ Criar migrations de users, assessments, questions (2h)
☐ Criar migrations de courses, recommendations (1.5h)
☐ Daily pessoal (reflexão - 0.5h)
```

**Quarta (4h)** - Seeders + Autenticação Início
```
☐ Criar seeders de cursos do IMETRO (1.5h)
☐ Criar seeders de questões (1.5h)
☐ Iniciar controllers de autenticação (1h)
```

**Quinta (4h)** - Autenticação Completa
```
☐ Views de registro e login (2h)
☐ Lógica de registro (validações) (1h)
☐ Lógica de login (JWT ou session) (1h)
```

**Sexta (4h)** - Finalização Sprint 1
```
☐ Dashboard protegido (1h)
☐ Layout base com Tailwind (2h)
☐ Testes manuais de tudo (0.5h)
☐ Documentação básica README (0.5h)
```

**Sábado (6h)** - Buffer + UI Polish
```
☐ Landing page atrativa (3h)
☐ Ajustes de UI/UX (2h)
☐ Preparar demo para review (1h)
```

**Domingo (4h)** - Review + Retro + Próximo Sprint
```
☐ Sprint Review (testar tudo) (1.5h)
☐ Retrospectiva pessoal (1h)
  - O que funcionou?
  - O que melhorar?
  - Ajustar velocity?
☐ Planejamento Sprint 2 (1.5h)
```

### 🎯 Meta da Sprint 1
✅ Sistema rodando com autenticação + BD estruturado + UI base

---

## 🚀 SPRINT 2: Questionário Vocacional (Semana 2)
**Objetivo**: Questionário completo com salvamento de respostas

### 📅 Planejamento - Domingo Anterior (incluído acima)
**Story Points Total: 23 SP**

#### User Stories

**US2.1 - Modelo de Dados do Questionário** (3 SP)
- [ ] Models: Question, Assessment, AssessmentResponse
- [ ] Relationships eloquentes
- [ ] Validações no modelo
- **Critério de Aceitação**: Eloquent queries funcionando

**US2.2 - Interface do Questionário - Parte 1** (8 SP)
- [ ] Rota: /assessment/start
- [ ] Controller: AssessmentController
- [ ] View: Página de introdução
- [ ] View: Questões de múltipla escolha (Likert 1-5)
- [ ] Navegação entre seções (4 seções)
- [ ] Barra de progresso visual
- **Critério de Aceitação**: Usuário navega pelas 40 questões com UI agradável

**US2.3 - Interface do Questionário - Parte 2** (5 SP)
- [ ] Questões abertas (3 campos de texto)
- [ ] Validações inline (JavaScript)
- [ ] Salvamento automático (AJAX a cada 5 respostas)
- [ ] Botão "Pausar e continuar depois"
- **Critério de Aceitação**: Respostas salvas mesmo se usuário sair

**US2.4 - Backend de Processamento de Respostas** (5 SP)
- [ ] Route POST /assessment/submit
- [ ] Salvar todas as respostas no BD
- [ ] Criar registro de Assessment com status "pending"
- [ ] Validação server-side de todas as respostas
- **Critério de Aceitação**: Respostas persistidas corretamente no BD

**US2.5 - Página de "Processando"** (2 SP)
- [ ] View: Tela de loading após submissão
- [ ] Animação de processamento
- [ ] Mensagens encorajadoras
- **Critério de Aceitação**: UX agradável durante espera

### 📝 Tarefas Diárias

**Segunda (4h)**
```
☐ Criar Models e Relationships (2h)
☐ Seeders adicionais se necessário (1h)
☐ Testes de relacionamentos (1h)
```

**Terça (4h)**
```
☐ Controller do questionário (estrutura) (1h)
☐ View de introdução ao questionário (1h)
☐ Primeira seção de questões (Interesses) (2h)
```

**Quarta (4h)**
```
☐ Segunda seção (Habilidades) (1.5h)
☐ Terceira seção (Valores) (1.5h)
☐ Barra de progresso funcional (1h)
```

**Quinta (4h)**
```
☐ Quarta seção (Personalidade) (1.5h)
☐ Questões abertas (3 campos) (1.5h)
☐ Validações JavaScript inline (1h)
```

**Sexta (4h)**
```
☐ Salvamento automático (AJAX) (2h)
☐ Backend de processamento (2h)
```

**Sábado (6h)**
```
☐ Página de loading/processamento (2h)
☐ Testes end-to-end do fluxo completo (2h)
☐ Ajustes de UX (1h)
☐ Bug fixes (1h)
```

**Domingo (4h)**
```
☐ Review da Sprint 2 (1.5h)
☐ Retrospectiva (1h)
☐ Planejamento Sprint 3 (1.5h)
```

### 🎯 Meta da Sprint 2
✅ Questionário completo e funcional com salvamento de respostas

---

## 🚀 SPRINT 3: Integração com LLM (Semana 3)
**Objetivo**: Sistema processando com IA e gerando recomendações

### 📅 Planejamento - Domingo Anterior
**Story Points Total: 25 SP** (Sprint mais pesada - core do sistema)

#### User Stories

**US3.1 - Setup de API (OpenAI/Gemini)** (5 SP)
- [ ] Escolher provider (OpenAI GPT-4 ou Gemini)
- [ ] Criar conta e obter API key
- [ ] Instalar SDK (openai-php/google-cloud-ai)
- [ ] Configurar em config/llm.php
- [ ] Testar chamada básica
- **Critério de Aceitação**: API respondendo com sucesso

**US3.2 - LLMService - Core Logic** (8 SP)
- [ ] Criar app/Services/LLMService.php
- [ ] Método buildPrompt() - montar prompt completo
- [ ] Método callLLM() - fazer requisição
- [ ] Parsing de resposta JSON
- [ ] Tratamento de erros e retry
- [ ] Logging estruturado
- **Critério de Aceitação**: Service retorna JSON estruturado com recomendações

**US3.3 - Prompt Engineering** (5 SP)
- [ ] Escrever system prompt detalhado
- [ ] Template de user prompt
- [ ] Formatar respostas do usuário
- [ ] Incluir informações dos cursos
- [ ] Testar com 3-5 perfis diferentes
- [ ] Ajustar prompt baseado em resultados
- **Critério de Aceitação**: Recomendações fazem sentido e são relevantes

**US3.4 - Queue Job para Processamento** (4 SP)
- [ ] Configurar Redis para queues
- [ ] Criar ProcessAssessmentJob
- [ ] Dispatch job após submissão
- [ ] Atualizar status do assessment
- [ ] Notificação ao usuário (opcional: email)
- **Critério de Aceitação**: Job processa em background sem travar interface

**US3.5 - Fallback System** (3 SP)
- [ ] Análise baseada em regras (se LLM falhar)
- [ ] Pesos por categoria de questões
- [ ] Matching simples com cursos
- **Critério de Aceitação**: Sistema não quebra se API falhar

### 📝 Tarefas Diárias

**Segunda (4h)**
```
☐ Criar conta OpenAI/Gemini (0.5h)
☐ Instalar SDK e configurar (1h)
☐ Testes básicos de API (1.5h)
☐ Documentar setup (1h)
```

**Terça (4h)**
```
☐ Estrutura do LLMService (1h)
☐ Método buildPrompt() (2h)
☐ Testes unitários básicos (1h)
```

**Quarta (4h)**
```
☐ Escrever system prompt completo (2h)
☐ Formatar dados do usuário (1h)
☐ Incluir dados dos cursos (1h)
```

**Quinta (4h)**
```
☐ Método callLLM() com retry (2h)
☐ Parsing de JSON response (1h)
☐ Error handling robusto (1h)
```

**Sexta (4h)**
```
☐ Configurar Redis (0.5h)
☐ Criar ProcessAssessmentJob (2h)
☐ Integrar job com Assessment (1h)
☐ Testes de integração (0.5h)
```

**Sábado (6h)**
```
☐ Sistema de fallback (2h)
☐ Testar com 5 perfis diferentes (2h)
☐ Ajustar prompts baseado em resultados (1h)
☐ Bug fixes e otimizações (1h)
```

**Domingo (4h)**
```
☐ Review Sprint 3 (1.5h)
☐ Retrospectiva (1h)
☐ Planejamento Sprint 4 (1.5h)
```

### 🎯 Meta da Sprint 3
✅ Sistema gerando recomendações reais usando IA

---

## 🚀 SPRINT 4: Dashboard de Resultados (Semana 4)
**Objetivo**: Interface de resultados com recomendações e roadmap

### 📅 Planejamento
**Story Points Total: 22 SP**

#### User Stories

**US4.1 - Modelo de Recomendações** (3 SP)
- [ ] Model: Recommendation
- [ ] Model: Roadmap
- [ ] Relationships com Assessment e Course
- [ ] Salvar resultado da LLM no BD
- **Critério de Aceitação**: Dados estruturados salvos corretamente

**US4.2 - Dashboard de Resultados - Overview** (6 SP)
- [ ] Route: /results/{assessmentId}
- [ ] Controller: ResultsController
- [ ] View: Resumo do perfil vocacional
- [ ] Componente: Gráfico radar (Chart.js)
- [ ] Card com pontos fortes
- **Critério de Aceitação**: Usuário vê seu perfil visualizado

**US4.3 - Cards de Recomendações de Cursos** (6 SP)
- [ ] Componente: Card de curso (top 3)
- [ ] Barra de compatibilidade visual
- [ ] Justificativa da IA
- [ ] Pontos fortes e desafios
- [ ] Botão "Ver Detalhes"
- **Critério de Aceitação**: 3 cursos exibidos de forma atrativa

**US4.4 - Modal de Detalhes do Curso** (4 SP)
- [ ] Modal/página: Informações completas do curso
- [ ] Seção: Saídas profissionais (5-8 carreiras)
- [ ] Cards de carreira com salário e demanda
- [ ] Botão "Ver Roadmap"
- **Critério de Aceitação**: Informações completas e organizadas

**US4.5 - Roadmap Personalizado** (3 SP)
- [ ] View: Página de roadmap
- [ ] Timeline em 3 fases (curto/médio/longo prazo)
- [ ] Lista de recursos recomendados
- [ ] Botão "Exportar PDF" (placeholder)
- **Critério de Aceitação**: Roadmap legível e acionável

### 📝 Tarefas Diárias

**Segunda (4h)**
```
☐ Models Recommendation e Roadmap (1.5h)
☐ RecommendationService (salvar dados) (2h)
☐ Testes (0.5h)
```

**Terça (4h)**
```
☐ ResultsController (estrutura) (1h)
☐ View de resultados (layout) (1.5h)
☐ Componente de perfil (resumo) (1.5h)
```

**Quarta (4h)**
```
☐ Integrar Chart.js (1h)
☐ Gráfico radar com dados reais (2h)
☐ Estilização com Tailwind (1h)
```

**Quinta (4h)**
```
☐ Componente Card de Curso (2h)
☐ Listar top 3 cursos (1h)
☐ Barra de compatibilidade (1h)
```

**Sexta (4h)**
```
☐ Modal de detalhes do curso (2h)
☐ Saídas profissionais (cards) (2h)
```

**Sábado (6h)**
```
☐ Página de roadmap (3h)
☐ Timeline visual (2h)
☐ Recursos recomendados (1h)
```

**Domingo (4h)**
```
☐ Review Sprint 4 (1.5h)
☐ Retrospectiva (1h)
☐ Planejamento Sprint 5 (1.5h)
```

### 🎯 Meta da Sprint 4
✅ Interface completa de resultados funcionando

---

## 🚀 SPRINT 5: Admin Panel + Polimento (Semana 5)
**Objetivo**: Painel administrativo básico + refinamentos

### 📅 Planejamento
**Story Points Total: 20 SP**

#### User Stories

**US5.1 - Autenticação Admin** (3 SP)
- [ ] Middleware: isAdmin
- [ ] Seeder de usuário admin
- [ ] Route group protegida
- **Critério de Aceitação**: Apenas admin acessa painel

**US5.2 - CRUD de Cursos** (8 SP)
- [ ] Routes: /admin/courses
- [ ] Controller: Admin\CourseController
- [ ] Views: index, create, edit
- [ ] Formulários com validação
- [ ] Upload de imagem (opcional)
- **Critério de Aceitação**: Admin gerencia cursos completamente

**US5.3 - CRUD de Questões** (5 SP)
- [ ] Routes: /admin/questions
- [ ] Controller: Admin\QuestionController
- [ ] Views: index, create, edit
- [ ] Ordenação de questões (drag-drop simples)
- **Critério de Aceitação**: Admin edita questionário

**US5.4 - Dashboard Administrativo** (4 SP)
- [ ] Route: /admin/dashboard
- [ ] Métricas: total assessments, cursos mais recomendados
- [ ] Gráficos simples
- [ ] Lista de assessments recentes
- **Critério de Aceitação**: Admin visualiza estatísticas

### 📝 Tarefas Diárias

**Segunda (4h)**
```
☐ Middleware e autenticação admin (1h)
☐ Layout admin (sidebar) (1.5h)
☐ Dashboard básico (1.5h)
```

**Terça (4h)**
```
☐ CRUD cursos - Controller (1.5h)
☐ CRUD cursos - Views (index, create) (2.5h)
```

**Quarta (4h)**
```
☐ CRUD cursos - Edit/Delete (2h)
☐ Validações e flash messages (1h)
☐ Testes manuais (1h)
```

**Quinta (4h)**
```
☐ CRUD questões - Controller (1.5h)
☐ CRUD questões - Views (2.5h)
```

**Sexta (4h)**
```
☐ Finalizar CRUD questões (2h)
☐ Dashboard com métricas (2h)
```

**Sábado (6h)**
```
☐ Polimento geral de UI (2h)
☐ Ajustes responsivos mobile (2h)
☐ Performance optimization (1h)
☐ Bug fixes (1h)
```

**Domingo (4h)**
```
☐ Review Sprint 5 (1.5h)
☐ Retrospectiva (1h)
☐ Planejamento Sprint 6 (FINAL) (1.5h)
```

### 🎯 Meta da Sprint 5
✅ Admin gerencia conteúdo + Sistema refinado

---

## 🚀 SPRINT 6: Testes, Deploy e Documentação (Semana 6)
**Objetivo**: Sistema testado, documentado e em produção

### 📅 Planejamento
**Story Points Total: 18 SP** (Sprint mais leve - finalização)

#### User Stories

**US6.1 - Testes Automatizados** (5 SP)
- [ ] Tests: Feature tests principais
    - Autenticação
    - Submissão de questionário
    - Processamento (mock LLM)
- [ ] Tests: Unit tests de Services
- [ ] Cobertura mínima: 50%
- **Critério de Aceitação**: `php artisan test` verde

**US6.2 - Documentação** (4 SP)
- [ ] README.md completo (instalação, uso)
- [ ] Documentação de APIs (básica)
- [ ] Manual do admin (PDF ou Markdown)
- [ ] Comentários no código crítico
- **Critério de Aceitação**: Outra pessoa consegue rodar o projeto

**US6.3 - Deploy em Produção** (5 SP)
- [ ] Escolher hosting (Heroku, DigitalOcean, Railway)
- [ ] Configurar ambiente de produção
- [ ] Migrations em produção
- [ ] Configurar domínio (se disponível)
- [ ] SSL/HTTPS
- **Critério de Aceitação**: Sistema acessível publicamente

**US6.4 - Testes com Usuários Reais** (2 SP)
- [ ] Recrutar 5-10 colegas/amigos
- [ ] Observar uso do sistema
- [ ] Coletar feedback
- [ ] Priorizar ajustes críticos
- **Critério de Aceitação**: Feedback documentado

**US6.5 - Vídeo de Demonstração** (2 SP)
- [ ] Gravar screencast (5-10 min)
- [ ] Editar vídeo
- [ ] Upload no YouTube (unlisted)
- **Critério de Aceitação**: Vídeo compartilhável

### 📝 Tarefas Diárias

**Segunda (4h)**
```
☐ Testes de autenticação (1.5h)
☐ Testes de questionário (2h)
☐ Setup de cobertura (0.5h)
```

**Terça (4h)**
```
☐ Testes de LLMService (mock) (2h)
☐ Testes de ResultsController (1h)
☐ Correção de bugs encontrados (1h)
```

**Quarta (4h)**
```
☐ README.md detalhado (2h)
☐ Manual do admin (1.5h)
☐ Comentários no código (0.5h)
```

**Quinta (4h)**
```
☐ Escolher e configurar hosting (1.5h)
☐ Deploy inicial (2h)
☐ Troubleshooting (0.5h)
```

**Sexta (4h)**
```
☐ Configurar domínio e SSL (1h)
☐ Migrations e seeds em prod (1h)
☐ Testes em produção (1h)
☐ Ajustes finais (1h)
```

**Sábado (6h)**
```
☐ Testes com usuários reais (3h)
☐ Implementar feedback crítico (2h)
☐ Gravar vídeo demo (1h)
```

**Domingo (4h)**
```
☐ Editar vídeo (1h)
☐ Sprint Review FINAL (1.5h)
☐ Retrospectiva geral do projeto (1h)
☐ Celebrar! 🎉 (0.5h)
```

### 🎯 Meta da Sprint 6
✅ Sistema em produção, testado e documentado

---

## 📊 QUADRO KANBAN PESSOAL

Utilize Trello, Notion ou GitHub Projects com estas colunas:

```
| BACKLOG | TODO (Sprint) | IN PROGRESS | TESTING | DONE |
|---------|---------------|-------------|---------|------|
|         |               | (Max 2)     |         |      |
```

**Regras**:
- Máximo 2 tarefas em "IN PROGRESS" simultaneamente
- Mover cartões diariamente
- Adicionar checklist em cada cartão

---

## 🎯 DAILY PERSONAL STANDUP

**Todos os dias às 18h (após trabalho), responder**:

1. ✅ **O que fiz hoje?**
2. 📋 **O que farei amanhã?**
3. 🚧 **Há algum impedimento?**
4. 📈 **Estou no ritmo para a meta da sprint?**

**Registrar em**: Notion, arquivo .md ou diário

---

## 📈 BURNDOWN CHART MANUAL

Criar planilha simples:

| Dia | SP Restantes | SP Ideais |
|-----|--------------|-----------|
| D1  | 21           | 21        |
| D2  | 18           | 18        |
| ... | ...          | ...       |

**Atualizar diariamente** para visualizar progresso

---

## 🔄 RETROSPECTIVA SEMANAL (Template)

```markdown
## Sprint X Retrospective

### ✅ O que funcionou bem?
- 
- 

### ❌ O que não funcionou?
- 
- 

### 💡 Ideias de melhoria
- 
- 

### 🎯 Ações para próxima sprint
- [ ] 
- [ ] 

### 📊 Métricas
- Story Points completados: X/Y
- Velocity real: X SP
- Horas trabalhadas: X/34h
- Bugs encontrados: X
```

---

## ⚠️ GESTÃO DE RISCOS

### Riscos Identificados

| Risco | Probabilidade | Impacto | Mitigação |
|-------|--------------|---------|-----------|
| API LLM ficar cara demais | Média | Alto | Usar tier gratuito inicialmente + implementar cache agressivo |
| Ficar sem tempo | Alta | Crítico | Buffer de 20% + priorizar MVP features |
| Bug crítico perto do fim | Média | Alto | Testes contínuos desde Sprint 2 |
| Dificuldade com LLM | Média | Alto | Estudar docs no início + ter sistema de fallback |

### Plano B (Descope)

Se ficar sem tempo, remover nesta ordem:
1. ❌ Painel administrativo completo (deixar só cursos)
2. ❌ Exportação de PDF do roadmap
3. ❌ Gráficos avançados
4. ❌ Email notifications

---

## 🛠️ FERRAMENTAS RECOMENDADAS

### Gestão de Tarefas
- **Trello** (gratuito, visual)
- **Notion** (completo, para documentação também)
- **GitHub Projects** (integrado com código)

### Controle de Tempo
- **Toggl Track** (free tier)
- **Pomodoro Timer** (25 min foco, 5 min pausa)

### Comunicação (se trabalhar com orientador)
- **Discord** (chamadas + screen share)
- **WhatsApp** (rápido)

### Código
- **VS Code** + Extensions (Laravel, Tailwind)
- **Git** (commits diários!)
- **Postman** (testar APIs)

---

## 📚 ESTUDO PARALELO (Crucial!)

### Antes de Cada Sprint

**Sprint 1**: Revisar Laravel basics (Laracasts: 2h)
**Sprint 2**: Estudar Blade + Alpine.js (1h)
**Sprint 3**: LLM APIs docs (OpenAI/Gemini - 2h) ⚠️ **CRÍTICO**
**Sprint 4**: Chart.js tutorial (1h)
**Sprint 5**: Laravel admin panels (1h)
**Sprint 6**: Deploy process (1h)

---

## 🎉 CELEBRAÇÕES

Comemorar pequenas vitórias:

- ✅ Sprint 1 completa → Jantar favorito
- ✅ LLM funcionando → Compartilhar com amigos
- ✅ UI bonita → Screenshot para portfolio
- ✅ Deploy em produção → Post no LinkedIn

---

## 📞 DEFINIÇÃO DE "EMERGÊNCIA"

Parar e pedir ajuda se:
- ⚠️ Travado na mesma tarefa por 4+ horas
- ⚠️ Sprint com 50%+ de atraso
- ⚠️ Bug crítico sem solução há 2 dias

**Fontes de ajuda**:
- Stack Overflow
- Laravel Discord
- ChatGPT / Claude (para debug)
- Orientador (Professor Eduardo)

---

## ✅ CHECKLIST PRÉ-APRESENTAÇÃO

**2 dias antes da entrega**:
- [ ] Sistema rodando em produção
- [ ] Vídeo demo gravado
- [ ] README.md completo
- [ ] Slides de apresentação (15-20 slides)
- [ ] Ensaiar apresentação (3x)
- [ ] Preparar respostas para perguntas comuns
- [ ] Backup do projeto (3 lugares: GitHub, Drive, HD externo)

---

## 🚀 MOTIVAÇÃO FINAL

**Lembre-se**: Este projeto pode:
- ✨ Ajudar centenas de estudantes do IMETRO
- 💼 Ser destaque no seu portfolio
- 🎓 Garantir uma excelente nota na monografia
- 🔥 Ser seu primeiro projeto "de verdade" com IA

**Mantra Diário**:
> "Progresso, não perfeição. Um commit por dia mantém o código no lugar."

---

## 📋 TEMPLATES ÚTEIS

### Template de Commit Message

```
feat: adiciona autenticação de usuário
fix: corrige bug no salvamento de respostas
refactor: melhora estrutura do LLMService
docs: atualiza README com instruções de deploy
test: adiciona testes para AssessmentController
style: aplica formatação Tailwind nos cards
```

### Template de Issue/Bug

```markdown
## 🐛 Descrição do Bug
[Descreva o problema]

## 📍 Como Reproduzir
1. Passo 1
2. Passo 2
3. ...

## ✅ Comportamento Esperado
[O que deveria acontecer]

## ❌ Comportamento Atual
[O que está acontecendo]

## 🖥️ Ambiente
- Browser: Chrome 120
- SO: Windows 11
- Laravel: 11.x

## 📸 Screenshots
[Se aplicável]
```

### Template de Pull Request (se trabalhar em equipe)

```markdown
## 🎯 Objetivo
[Descreve o que esta PR faz]

## 🔄 Mudanças
- [ ] Feature X implementada
- [ ] Testes adicionados
- [ ] Documentação atualizada

## ✅ Checklist
- [ ] Código testado localmente
- [ ] Sem console.log() ou dd() esquecidos
- [ ] Migrations rodando
- [ ] Segue PSR-12

## 📸 Screenshots
[Se mudou UI]
```

---

## 🎓 INTEGRAÇÃO COM MONOGRAFIA

### Seções da Monografia x Sprints

| Capítulo | Sprint Relacionada | Conteúdo |
|----------|-------------------|----------|
| **Cap 1: Introdução** | - | Contexto, problema, objetivos |
| **Cap 2: Referencial Teórico** | - | Literatura sobre orientação vocacional e IA |
| **Cap 3: Metodologia** | Sprint 1-6 | Descrição do processo de desenvolvimento |
| **Cap 4: Desenvolvimento** | Sprint 1-6 | Arquitetura, tecnologias, implementação |
| **Cap 5: Testes e Resultados** | Sprint 6 | Testes realizados, feedback de usuários |
| **Cap 6: Conclusão** | Pós-Sprint 6 | Resultados alcançados, trabalhos futuros |

### Documentar Durante o Desenvolvimento

**Cada Sprint, fazer**:
```markdown
## Sprint X - Documentação para Monografia

### Decisões Técnicas
- Por que escolhi PostgreSQL? [justificativa]
- Por que Laravel? [justificativa]

### Desafios Encontrados
- Desafio 1: [descrição + como resolvi]
- Desafio 2: [descrição + como resolvi]

### Aprendizados
- Aprendi sobre [tema]
- Descobri que [insight]

### Screenshots Importantes
- [Salvar screenshots de cada funcionalidade]

### Métricas
- Linhas de código: X
- Commits: X
- Horas trabalhadas: X
```

**Isso vai facilitar MUITO a escrita da monografia!**

---

## 📊 DASHBOARD DE ACOMPANHAMENTO PESSOAL

### Planilha de Controle (Google Sheets)

**Aba 1: Visão Geral**

| Sprint | Status | SP Planejados | SP Completados | % Concluído |
|--------|--------|---------------|----------------|-------------|
| Sprint 1 | 🟢 Concluída | 21 | 21 | 100% |
| Sprint 2 | 🟡 Em andamento | 23 | 15 | 65% |
| Sprint 3 | ⚪ Não iniciada | 25 | 0 | 0% |
| Sprint 4 | ⚪ Não iniciada | 22 | 0 | 0% |
| Sprint 5 | ⚪ Não iniciada | 20 | 0 | 0% |
| Sprint 6 | ⚪ Não iniciada | 18 | 0 | 0% |
| **TOTAL** | | **129 SP** | **36 SP** | **28%** |

**Aba 2: Daily Log**

| Data | Horas Trabalhadas | Tarefas Completadas | Bloqueios | Notas |
|------|-------------------|---------------------|-----------|-------|
| 13/01 | 4h | US1.1, US1.2 (50%) | Problema com PostgreSQL | Resolvido reinstalando |
| 14/01 | 4h | US1.2 (100%), US1.3 (30%) | - | Progresso bom |
| ... | ... | ... | ... | ... |

**Aba 3: Burndown**

[Gráfico de linha mostrando SP restantes vs dias]

---

## 🎯 OKRs PESSOAIS (Objectives and Key Results)

### Objetivo 1: Entregar MVP Funcional
**Key Results**:
- ✅ Sistema com 6 funcionalidades core implementadas
- ✅ 80% das user stories completadas
- ✅ Sistema em produção até dia X

### Objetivo 2: Aprender IA Aplicada
**Key Results**:
- ✅ Integrar com sucesso uma LLM API
- ✅ Criar 3 prompts efetivos
- ✅ Entender rate limiting e custos de API

### Objetivo 3: Desenvolver Portfólio Forte
**Key Results**:
- ✅ Projeto completo no GitHub (com README atrativo)
- ✅ Vídeo demo profissional
- ✅ Post no LinkedIn com resultados

---

## 🔧 SETUP SCRIPT (Automatização)

### setup.sh (Linux/Mac)

```bash
#!/bin/bash

echo "🚀 Configurando Assistente Vocacional IA..."

# Verificar dependências
command -v php >/dev/null 2>&1 || { echo "❌ PHP não instalado"; exit 1; }
command -v composer >/dev/null 2>&1 || { echo "❌ Composer não instalado"; exit 1; }
command -v psql >/dev/null 2>&1 || { echo "❌ PostgreSQL não instalado"; exit 1; }

# Clonar repositório (se aplicável)
# git clone https://github.com/seu-usuario/assistente-vocacional-imetro.git
# cd assistente-vocacional-imetro

# Instalar dependências
echo "📦 Instalando dependências..."
composer install
npm install

# Configurar ambiente
echo "⚙️ Configurando .env..."
cp .env.example .env
php artisan key:generate

# Configurar banco de dados
echo "🗄️ Configurando banco de dados..."
read -p "Nome do banco de dados: " DB_NAME
read -p "Usuário PostgreSQL: " DB_USER
read -sp "Senha PostgreSQL: " DB_PASS
echo ""

# Atualizar .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

# Criar banco
psql -U $DB_USER -c "CREATE DATABASE $DB_NAME;"

# Rodar migrations e seeds
echo "🌱 Rodando migrations e seeds..."
php artisan migrate --seed

# Compilar assets
echo "🎨 Compilando assets..."
npm run build

echo "✅ Setup concluído!"
echo "🚀 Execute: php artisan serve"
```

### setup.bat (Windows)

```batch
@echo off
echo 🚀 Configurando Assistente Vocacional IA...

REM Verificar PHP
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ❌ PHP não instalado
    exit /b 1
)

REM Instalar dependências
echo 📦 Instalando dependências...
call composer install
call npm install

REM Configurar .env
echo ⚙️ Configurando .env...
copy .env.example .env
php artisan key:generate

echo ✅ Setup básico concluído!
echo 🔧 Configure manualmente o .env para PostgreSQL
pause
```

---

## 📱 NOTIFICAÇÕES E LEMBRETES

### Configurar Lembretes (Google Calendar / Apple Reminders)

**Diários**:
- ⏰ 18:00 - Daily Personal Standup
- ⏰ 22:00 - Commit do dia (se ainda não fez)

**Semanais**:
- 📅 Domingo 18:00 - Sprint Review
- 📅 Domingo 19:00 - Retrospectiva
- 📅 Domingo 20:00 - Planejamento próxima sprint

**Marcos**:
- 🎯 Sexta Sprint 3 - Verificar API credits OpenAI/Gemini
- 🎯 Quinta Sprint 6 - Deploy em produção
- 🎯 Sábado Sprint 6 - Testes com usuários

---

## 💰 ORÇAMENTO DO PROJETO

### Custos Estimados (6 semanas)

| Item | Custo | Observações |
|------|-------|-------------|
| **OpenAI API** | $20-30 | ~500 assessments (tier gratuito: $5) |
| **Gemini API** | $0-10 | Tier gratuito generoso |
| **Hosting (Heroku/Railway)** | $0-7 | Tier gratuito ou hobby tier |
| **Domínio .ao** | 5.000-15.000 Kz | Opcional, pode usar subdomínio gratuito |
| **PostgreSQL (Cloud)** | $0 | Supabase free tier ou ElephantSQL |
| **Redis (Cloud)** | $0 | Redis Labs free tier |
| **Total** | **$20-47 (~20.000 Kz)** | Pode ser $0 se usar tiers gratuitos |

### Redução de Custos

**Estratégia Free-First**:
1. **LLM**: Gemini (60 requests/min free) ou OpenAI $5 credit
2. **Hosting**: Railway (512MB RAM free) ou Heroku
3. **BD**: Supabase (500MB free) ou ElephantSQL
4. **Cache**: Agressivo (reduz 80% das chamadas LLM)

---

## 🎨 RECURSOS DE DESIGN

### Figma/Sketch Templates (Gratuitos)

- **UI Kits**: Tailwind UI (componentes prontos)
- **Icons**: Heroicons (oficial Tailwind)
- **Ilustrações**: unDraw, Storyset
- **Fonts**: Inter, Poppins (Google Fonts)

### Paleta de Cores Sugerida

```css
:root {
  /* Primárias */
  --primary-50: #EFF6FF;
  --primary-500: #3B82F6;  /* Azul confiável */
  --primary-700: #1D4ED8;

  /* Secundárias */
  --secondary-500: #10B981; /* Verde sucesso */
  
  /* Neutras */
  --gray-50: #F9FAFB;
  --gray-500: #6B7280;
  --gray-900: #111827;

  /* Acento */
  --accent-500: #F59E0B;    /* Laranja energia */
  
  /* Erro */
  --red-500: #EF4444;
}
```

### Componentes Reutilizáveis

Criar desde Sprint 1:

```blade
{{-- resources/views/components/button.blade.php --}}
@props(['type' => 'button', 'variant' => 'primary'])

<button 
  type="{{ $type }}"
  class="px-4 py-2 rounded-lg font-semibold transition-all
         @if($variant === 'primary') 
           bg-primary-500 hover:bg-primary-600 text-white
         @elseif($variant === 'secondary')
           bg-gray-200 hover:bg-gray-300 text-gray-900
         @endif"
  {{ $attributes }}
>
  {{ $slot }}
</button>

{{-- Uso: --}}
<x-button variant="primary" @click="submit">
  Enviar Avaliação
</x-button>
```

---

## 🧪 ESTRATÉGIA DE TESTES DETALHADA

### Pirâmide de Testes (Para 6 semanas)

```
         /\
        /E2E\     5% - 2-3 testes críticos (Dusk)
       /------\
      /  INT   \   15% - 5-8 testes de integração
     /----------\
    /   UNIT     \ 80% - 20-30 testes unitários
   /--------------\
```

### Testes Prioritários (MVP)

**Sprint 2-3** (Implementar junto):
```php
// tests/Feature/AssessmentTest.php
public function test_user_can_start_assessment()
public function test_user_cannot_start_duplicate_assessment()
public function test_assessment_saves_responses()

// tests/Unit/LLMServiceTest.php
public function test_builds_prompt_correctly()
public function test_handles_api_failure_gracefully()
public function test_caches_identical_requests()
```

**Sprint 4**:
```php
// tests/Feature/ResultsTest.php
public function test_user_sees_recommendations()
public function test_user_cannot_see_others_results()
```

### Teste Manual Checklist

**Antes de cada Sprint Review**:

```markdown
## 🧪 Checklist de Testes Manuais

### Autenticação
- [ ] Registrar novo usuário
- [ ] Login com credenciais corretas
- [ ] Login com credenciais incorretas (deve falhar)
- [ ] Logout
- [ ] Recuperar senha

### Questionário
- [ ] Iniciar nova avaliação
- [ ] Responder todas as 40 questões
- [ ] Navegar entre seções
- [ ] Sair e voltar (respostas salvas?)
- [ ] Submeter avaliação

### Processamento
- [ ] Ver tela de loading
- [ ] Aguardar resultado (max 30s)
- [ ] Verificar se recomendações fazem sentido

### Resultados
- [ ] Ver perfil vocacional
- [ ] Ver 3 cursos recomendados
- [ ] Abrir detalhes de curso
- [ ] Ver roadmap personalizado
- [ ] Navegar entre cursos

### Admin
- [ ] Acessar painel admin
- [ ] Criar novo curso
- [ ] Editar curso existente
- [ ] Deletar curso
- [ ] Ver dashboard com métricas

### Responsividade
- [ ] Testar em mobile (Chrome DevTools)
- [ ] Testar em tablet
- [ ] Testar em desktop
```

---

## 🚨 PLANO DE CONTINGÊNCIA

### Se Ficar Atrasado > 1 Sprint

**Ação Imediata**:
1. **Pare e avalie** (1h)
    - Qual a causa do atraso?
    - É problema técnico ou de planejamento?

2. **Descope agressivo**
    - Mantenha: Auth, Questionário, LLM, Resultados básicos
    - Remova: Admin panel, Roadmap, Gráficos fancy

3. **Peça ajuda**
    - Orientador
    - Colegas de curso
    - Stack Overflow / Discord

4. **Trabalhe extra** (ÚLTIMA OPÇÃO)
    - Max 2h extras/dia (não sustentável)
    - Apenas por 3-4 dias

### Se Problema Técnico Crítico

**Bug que bloqueia > 4h**:

```
1. Isole o problema (debugging)
2. Google/Stack Overflow (30min)
3. ChatGPT/Claude para ajudar (30min)
4. Pergunte em Discord Laravel Brasil (1h)
5. Se ainda não resolveu: descope temporariamente
```

---

## 📚 RECURSOS DE APRENDIZADO RÁPIDO

### Laravel (Sprints 1-2)

**Essencial (6h total)**:
- Laracasts: "Laravel From Scratch" (primeiros 10 episódios)
- Docs oficiais: Routing, Controllers, Blade, Eloquent

**Links**:
- https://laracasts.com/series/laravel-11-for-beginners
- https://laravel.com/docs/11.x

### LLM APIs (Sprint 3 - CRÍTICO)

**Essencial (4h total)**:
- OpenAI Docs: Chat Completions
  https://platform.openai.com/docs/guides/chat
- Gemini Docs: Getting Started
  https://ai.google.dev/docs
- Tutorial: Prompt Engineering
  https://www.promptingguide.ai/

**Prática**:
- Fazer 10-15 requests de teste no Playground
- Experimentar diferentes temperaturas
- Testar structured outputs (JSON mode)

### Tailwind CSS (Sprint 2)

**Essencial (2h)**:
- Docs: Utility-First Fundamentals
- Componentes prontos: Tailwind UI (componentes gratuitos)

---

## 🎤 PREPARAÇÃO PARA APRESENTAÇÃO

### Estrutura da Apresentação (15-20 min)

**Slide 1: Título** (1 min)
- Nome do projeto
- Seu nome
- IMETRO

**Slides 2-3: Problema e Contexto** (2 min)
- Estatísticas de evasão acadêmica
- Falta de orientação vocacional
- Oportunidade com IA

**Slides 4-5: Objetivos** (1 min)
- Objetivo geral
- 3-4 objetivos específicos

**Slides 6-8: Metodologia** (3 min)
- Personal Scrum adaptado
- 6 sprints em 6 semanas
- Tecnologias escolhidas (Laravel, LLM, PostgreSQL)

**Slides 9-12: Desenvolvimento** (5 min)
- Arquitetura do sistema (diagrama)
- Funcionalidades principais (screenshots)
- **DEMO AO VIVO** (2-3 min)
    - Fazer uma avaliação completa
    - Mostrar resultados

**Slides 13-14: Testes e Resultados** (2 min)
- Testes realizados
- Feedback de 10 usuários
- Métricas (precisão, satisfação)

**Slide 15: Conclusões** (1 min)
- Objetivos alcançados
- Aprendizados
- Trabalhos futuros

**Slide 16: Obrigado + Perguntas** (5 min)
- Agradecer orientador, IMETRO
- Abrir para perguntas

### Perguntas Prováveis (Preparar Respostas)

**Técnicas**:
1. "Por que escolheu Laravel e não Django/Node?"
2. "Como garantiu a qualidade das recomendações da IA?"
3. "Qual o custo operacional do sistema?"
4. "Como tratou questões de privacidade (LGPD)?"

**Metodológicas**:
5. "Por que 6 semanas? Não é muito curto?"
6. "Quais foram os maiores desafios?"
7. "Como validou com usuários reais?"

**Futuro**:
8. "Quais melhorias você faria?"
9. "O sistema está pronto para escalar?"
10. "Como integraria com sistema acadêmico?"

---

## 🏆 CRITÉRIOS DE SUCESSO FINAL

### MVP é considerado sucesso se:

✅ **Funcionalidades Core**:
- [ ] Usuário consegue se registrar e fazer login
- [ ] Usuário completa questionário de 40 questões
- [ ] Sistema processa com LLM e retorna 3 cursos
- [ ] Recomendações são relevantes (validado por 5+ pessoas)
- [ ] Interface é usável em mobile e desktop

✅ **Qualidade Técnica**:
- [ ] Código no GitHub com commits organizados
- [ ] README permite outra pessoa rodar o projeto
- [ ] Pelo menos 10 testes automatizados passando
- [ ] Sistema roda em produção (acessível por URL)

✅ **Documentação**:
- [ ] Vídeo demo de 5-10 minutos
- [ ] Apresentação com 15-20 slides
- [ ] Seções principais da monografia escritas

✅ **Validação**:
- [ ] Pelo menos 10 pessoas testaram
- [ ] Feedback geral positivo (NPS > 6)
- [ ] Professor orientador aprovou

---

## 🎓 DEPOIS DA ENTREGA

### Semana Pós-Sprint 6

**Para a monografia**:
- [ ] Escrever introdução e conclusão
- [ ] Revisar referencial teórico
- [ ] Adicionar screenshots no capítulo de desenvolvimento
- [ ] Revisar gramática e formatação ABNT
- [ ] Pedir para 2 pessoas revisarem

**Para o portfólio**:
- [ ] Post no LinkedIn com vídeo
- [ ] Adicionar no GitHub com README atrativo
- [ ] Criar case study no Notion/Medium
- [ ] Atualizar currículo

**Próximos passos**:
- [ ] Considerar publicar em evento acadêmico
- [ ] Propor ao IMETRO adotar oficialmente
- [ ] Expandir para outras instituições
- [ ] Transformar em SaaS (futuro)

---

## 💪 MENSAGEM FINAL DE MOTIVAÇÃO

### Você Consegue!

Este plano pode parecer intenso, mas é 100% realizável. Milhares de desenvolvedores ao redor do mundo constroem projetos similares em prazos parecidos.

**Lembre-se**:
- 🎯 **Foco**: Uma tarefa por vez, um dia por vez
- 📈 **Progresso**: 1% melhor todo dia = 6 semanas transformadoras
- 🤝 **Ajuda**: Não tenha vergonha de pedir ajuda
- 🎉 **Celebre**: Cada commit, cada funcionalidade, cada bug resolvido

**No final deste projeto, você terá**:
- ✅ Um sistema real, funcional, com IA
- ✅ Experiência prática com tecnologias modernas
- ✅ Portfolio impressionante para empregos
- ✅ Sua licenciatura em Ciência da Computação
- ✅ Orgulho de ter criado algo que ajuda pessoas

### Recursos de Suporte

**Comunidades Angola Tech**:
- Facebook: "Programadores Angola"
- WhatsApp: Grupos de dev Luanda
- LinkedIn: Profissionais de TI Angola

**Comunidades Globais**:
- Laravel Discord: https://discord.gg/laravel
- r/laravel (Reddit)
- Stack Overflow (português/inglês)

---

## 📞 CONTATO DE EMERGÊNCIA

**Se travou completamente**, pode enviar email para:
- Professor Eduardo Vantuva (orientador)
- Coordenação do curso

**Ou buscar ajuda online**:
- ChatGPT / Claude (para debugging)
- Stack Overflow
- Laravel Discord

---

**Boa sorte, Artaxerxes! 🚀**

*"O código não mente, mas o programador pode errar. E quando erra, aprende. E quando aprende, evolui. Você está prestes a evoluir muito nestas 6 semanas."*

**Primeiro commit**: Janeiro 2025  
**Deploy em produção**: Fevereiro 2025  
**Formatura**: 2025  
**Seu futuro**: Brilhante 🌟

---

*Documento criado em: Janeiro 2025*  
*Versão: 1.0*  
*Autor: Plano Personal Scrum para Artaxerxes Nazareno*
