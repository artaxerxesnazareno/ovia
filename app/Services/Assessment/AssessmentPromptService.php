<?php

namespace App\Services\Assessment;

use App\Models\Assessment;
use App\Models\Course;
use Illuminate\Support\Collection;

class AssessmentPromptService
{
    public function buildPrompt(Assessment $assessment): string
    {
        $assessment->loadMissing(['user', 'responses.question']);

        $user = $assessment->user;
        $age = $user?->birth_date?->age ?? $user?->age;
        $gender = $this->formatGender((string) ($user?->gender ?? 'not_informed'));

        $objectiveResponses = $assessment->responses
            ->filter(fn ($response) => $response->question && $response->question->question_type !== 'open')
            ->sortBy(fn ($response) => ($response->question->category ?? '').'_'.($response->question->order ?? 0))
            ->groupBy(fn ($response) => $response->question->category ?? 'others');

        $openResponses = $assessment->responses
            ->filter(fn ($response) => $response->question && $response->question->question_type === 'open')
            ->sortBy(fn ($response) => $response->question->order ?? 0)
            ->values();

        $courses = Course::query()
            ->active()
            ->with('careerPaths')
            ->orderBy('name')
            ->get();

        return trim(<<<PROMPT
CONTEXTO:
Voce e um especialista em orientacao vocacional com 20 anos de experiencia trabalhando com jovens em instituicoes de ensino superior em Angola.
Sua expertise inclui psicologia vocacional, analise de mercado de trabalho e pedagogia.

DADOS DO ESTUDANTE:

## Perfil Demografico:
- Idade: {$this->formatAge($age)}
- Genero: {$gender}
- Localizacao: Luanda, Angola

## Respostas Objetivas (Escala 1-5):
{$this->formatObjectiveResponses($objectiveResponses)}

## Respostas Abertas:
{$this->formatOpenResponses($openResponses)}

## Cursos Disponiveis no IMETRO:
{$this->formatCourses($courses)}

TAREFA:
Analise profundamente o perfil deste estudante e forneca recomendacoes de cursos do IMETRO.
Sua analise deve ser:
1. Baseada em evidencias das respostas.
2. Culturalmente sensivel ao contexto angolano.
3. Honesta sobre pontos fortes e desafios.
4. Focada no desenvolvimento de longo prazo.
5. Alinhar os nomes de cursos EXATAMENTE com a lista de cursos disponiveis acima.

Retorne APENAS um objeto JSON valido com a seguinte estrutura:
{$this->expectedJsonStructure()}

IMPORTANTE:
- Seja especifico e detalhado.
- Cite evidencias das respostas do estudante.
- Considere o contexto do mercado de trabalho angolano.
- Seja honesto e encorajador.
- Seja conciso para caber integralmente no limite de saida: textos objetivos e sem repeticoes.
- Retorne no maximo 3 cursos em "course_recommendations" e no maximo 3 "career_paths" por curso.
- Retorne APENAS JSON valido, sem texto antes ou depois.
PROMPT);
    }

    private function formatAge(?int $age): string
    {
        return $age !== null ? "{$age} anos" : 'Nao informado';
    }

    private function formatGender(string $gender): string
    {
        return match ($gender) {
            'male', 'M', 'm' => 'Masculino',
            'female', 'F', 'f' => 'Feminino',
            'other' => 'Outro',
            'prefer_not_to_say' => 'Prefere nao informar',
            default => 'Nao informado',
        };
    }

    private function formatObjectiveResponses(Collection $groupedResponses): string
    {
        if ($groupedResponses->isEmpty()) {
            return '- Sem respostas objetivas disponiveis.';
        }

        $output = [];

        foreach ($groupedResponses as $category => $responses) {
            $output[] = "### {$this->formatCategoryTitle((string) $category)}";

            foreach ($responses->values() as $index => $response) {
                $questionText = (string) ($response->question->question_text ?? 'Pergunta');
                $value = $response->response_value ?? 'Nao informado';
                $output[] = ($index + 1).". {$questionText}: {$value}";
            }

            $output[] = '';
        }

        return trim(implode("\n", $output));
    }

    private function formatOpenResponses(Collection $openResponses): string
    {
        if ($openResponses->isEmpty()) {
            return '- Sem respostas abertas disponiveis.';
        }

        $output = [];

        foreach ($openResponses as $index => $response) {
            $questionText = (string) ($response->question->question_text ?? 'Pergunta aberta');
            $answerText = trim((string) ($response->response_text ?? 'Sem resposta'));

            $output[] = ($index + 1).". \"{$questionText}\"";
            $output[] = "   \"{$answerText}\"";
            $output[] = '';
        }

        return trim(implode("\n", $output));
    }

    private function formatCourses(Collection $courses): string
    {
        if ($courses->isEmpty()) {
            return '- Nenhum curso ativo encontrado.';
        }

        $output = [];

        foreach ($courses->values() as $index => $course) {
            $careerTitles = $course->careerPaths
                ->pluck('title')
                ->filter()
                ->values()
                ->all();

            $output[] = ($index + 1).". **{$course->name}**";
            $output[] = "   - Duracao: {$course->duration_semesters} semestres";
            $output[] = "   - Descricao: {$course->description}";
            $output[] = '   - Saidas Profissionais: '.(
                !empty($careerTitles)
                    ? implode(', ', $careerTitles)
                    : 'Nao informado'
                );

            $skills = $course->careerPaths
                ->pluck('key_skills')
                ->filter()
                ->flatten()
                ->unique()
                ->take(6)
                ->values()
                ->all();

            $output[] = '   - Perfil esperado: '.(
                !empty($skills)
                    ? implode(', ', $skills)
                    : 'Interesse e dedicacao para evolucao na area'
                );
            $output[] = '';
        }

        return trim(implode("\n", $output));
    }

    private function formatCategoryTitle(string $category): string
    {
        return match ($category) {
            'interests' => 'INTERESSES PROFISSIONAIS',
            'skills' => 'HABILIDADES E APTIDOES',
            'values' => 'VALORES E MOTIVACOES',
            'personality' => 'PERSONALIDADE',
            default => strtoupper($category),
        };
    }

    private function expectedJsonStructure(): string
    {
        return <<<JSON
{
  "profile_analysis": {
    "summary": "string",
    "personality_type": "string",
    "strengths": ["string"],
    "areas_to_develop": ["string"],
    "career_values": ["string"]
  },
  "course_recommendations": [
    {
      "course_name": "string",
      "rank": 1,
      "compatibility_score": 0,
      "justification": "string",
      "student_strengths_for_course": ["string"],
      "potential_challenges": ["string"],
      "career_paths": [
        {
          "title": "string",
          "description": "string",
          "average_salary_range": "string",
          "market_demand": "string",
          "key_skills": ["string"],
          "growth_potential": "string"
        }
      ]
    }
  ],
  "roadmap": {
    "short_term": [{"goal": "string", "timeframe": "string", "actions": ["string"], "resources": ["string"], "estimated_hours_week": "string"}],
    "medium_term": [{"goal": "string", "timeframe": "string", "actions": ["string"], "resources": ["string"], "estimated_hours_week": "string"}],
    "long_term": [{"goal": "string", "timeframe": "string", "actions": ["string"], "resources": ["string"], "estimated_hours_week": "string"}],
    "certifications_to_consider": ["string"],
    "books_recommended": ["string"],
    "communities_to_join": ["string"]
  },
  "additional_advice": {
    "immediate_next_steps": "string",
    "long_term_vision": "string",
    "words_of_encouragement": "string"
  }
}
JSON;
    }
}
