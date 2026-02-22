<?php

use App\Jobs\ProcessAssessmentJob;
use App\Models\Assessment;
use App\Models\AssessmentResponse;
use App\Models\CareerPath;
use App\Models\Course;
use App\Models\Question;
use App\Models\User;
use App\Services\Assessment\AssessmentPromptService;
use App\Services\Assessment\RecommendationService;
use App\Services\LLM\LLMService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('process assessment job saves recommendations and roadmap and marks assessment as completed', function () {
    Log::spy();

    config()->set('llm.provider', 'gemini');
    config()->set('llm.gemini.api_key', 'test-api-key');
    config()->set('llm.gemini.model', 'gemini-2.5-flash');
    config()->set('llm.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    config()->set('llm.timeout', 10);
    config()->set('llm.retry_attempts', 1);

    $user = User::factory()->create([
        'birth_date' => '2008-01-01',
        'gender' => 'male',
    ]);

    $course = Course::create([
        'name' => 'Licenciatura em Ciencias da Computacao',
        'slug' => 'ciencias-da-computacao',
        'description' => 'Formacao em desenvolvimento de software e dados.',
        'duration_semesters' => 8,
        'shifts' => ['morning', 'evening'],
        'vacancies_per_year' => 40,
        'coordinator_name' => 'Coord Teste',
        'curriculum' => ['disciplinas' => ['Algoritmos', 'Banco de Dados']],
        'admission_requirements' => 'Ensino medio',
        'is_active' => true,
    ]);

    CareerPath::create([
        'course_id' => $course->id,
        'title' => 'Desenvolvedor Full-Stack',
        'description' => 'Atua em front-end e back-end.',
        'average_salary_min' => 150000,
        'average_salary_max' => 400000,
        'market_demand' => 'high',
        'key_skills' => ['JavaScript', 'Python', 'SQL'],
        'growth_potential' => ['Alta demanda', 'Setor em expansao'],
    ]);

    $questionLikert = Question::create([
        'category' => 'interests',
        'dimension' => 'areas_conhecimento',
        'question_text' => 'Tenho facilidade com logica e matematica',
        'question_type' => 'likert',
        'weight' => 1,
        'order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $questionOpen = Question::create([
        'category' => 'interests',
        'dimension' => 'visao_futuro',
        'question_text' => 'Como voce se ve daqui a 10 anos?',
        'question_type' => 'open',
        'weight' => 1,
        'order' => 2,
        'is_required' => true,
        'is_active' => true,
    ]);

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'processing',
        'started_at' => now()->subSeconds(8),
    ]);

    AssessmentResponse::create([
        'assessment_id' => $assessment->id,
        'question_id' => $questionLikert->id,
        'response_value' => 5,
        'response_text' => null,
    ]);

    AssessmentResponse::create([
        'assessment_id' => $assessment->id,
        'question_id' => $questionOpen->id,
        'response_value' => null,
        'response_text' => 'Quero trabalhar com tecnologia e criar uma startup.',
    ]);

    $llmPayload = [
        'profile_analysis' => [
            'summary' => 'Perfil analitico com foco em tecnologia.',
            'personality_type' => 'Analitico-Criativo',
            'strengths' => ['Logica', 'Aprendizado rapido', 'Inovacao'],
            'areas_to_develop' => ['Comunicacao', 'Trabalho em equipe'],
            'career_values' => ['Crescimento', 'Impacto social'],
        ],
        'course_recommendations' => [
            [
                'course_name' => 'Ciencias da Computacao',
                'rank' => 1,
                'compatibility_score' => 92,
                'justification' => 'Alta afinidade com logica, tecnologia e resolucao de problemas.',
                'student_strengths_for_course' => ['Logica', 'Tecnologia'],
                'potential_challenges' => ['Comunicacao tecnica'],
                'career_paths' => [
                    [
                        'title' => 'Desenvolvedor Full-Stack',
                        'description' => 'Atua em front-end e back-end.',
                        'average_salary_range' => '150.000 - 400.000 Kz/mes',
                        'market_demand' => 'Alta',
                        'key_skills' => ['JavaScript', 'Python', 'SQL'],
                        'growth_potential' => 'Setor em expansao',
                    ],
                ],
            ],
        ],
        'roadmap' => [
            'short_term' => [
                [
                    'goal' => 'Fundamentos de programacao',
                    'timeframe' => '0-6 meses',
                    'actions' => ['Estudar Python', 'Resolver exercicios'],
                    'resources' => ['https://www.codecademy.com/learn/learn-python-3'],
                    'estimated_hours_week' => '5-7 horas',
                ],
            ],
            'medium_term' => [],
            'long_term' => [],
            'certifications_to_consider' => ['AWS Certified Cloud Practitioner'],
            'books_recommended' => ['Clean Code - Robert C. Martin'],
            'communities_to_join' => ['Stack Overflow'],
        ],
        'additional_advice' => [
            'immediate_next_steps' => 'Comece hoje com exercicios praticos.',
            'long_term_vision' => 'Construa base forte para atuar no ecossistema tech angolano.',
            'words_of_encouragement' => 'Voce tem potencial para crescer de forma consistente.',
        ],
    ];

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => json_encode($llmPayload, JSON_UNESCAPED_UNICODE)],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $job = new ProcessAssessmentJob($assessment->id);
    $job->handle(
        app(LLMService::class),
        app(AssessmentPromptService::class),
        app(RecommendationService::class),
    );

    $assessment->refresh();
    expect($assessment->status)->toBe('completed');
    expect($assessment->processing_time_seconds)->not->toBeNull();

    $this->assertDatabaseHas('recommendations', [
        'assessment_id' => $assessment->id,
        'course_id' => $course->id,
        'rank' => 1,
    ]);

    $this->assertDatabaseCount('roadmaps', 1);

    Log::shouldHaveReceived('info')
        ->withArgs(function (string $message, array $context) use ($assessment) {
            return $message === 'assessment.job.started'
                && ($context['assessment_id'] ?? null) === $assessment->id
                && ($context['attempt'] ?? null) === 1
                && ($context['max_tries'] ?? null) === 2;
        })
        ->once();

    Log::shouldHaveReceived('info')
        ->withArgs(function ($message, $context) use ($assessment) {
            return $message === 'assessment.job.completed'
                && is_array($context)
                && ($context['assessment_id'] ?? null) === $assessment->id
                && array_key_exists('processing_time_seconds', $context);
        })
        ->atLeast()
        ->once();
});

test('process assessment job marks assessment as failed and logs failure context on invalid llm json', function () {
    Log::spy();

    config()->set('llm.provider', 'gemini');
    config()->set('llm.gemini.api_key', 'test-api-key');
    config()->set('llm.gemini.model', 'gemini-2.5-flash');
    config()->set('llm.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    config()->set('llm.timeout', 10);
    config()->set('llm.retry_attempts', 1);

    $user = User::factory()->create([
        'birth_date' => '2008-01-01',
        'gender' => 'male',
    ]);

    $questionLikert = Question::create([
        'category' => 'interests',
        'dimension' => 'areas_conhecimento',
        'question_text' => 'Tenho facilidade com logica e matematica',
        'question_type' => 'likert',
        'weight' => 1,
        'order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'processing',
        'started_at' => now(),
    ]);

    AssessmentResponse::create([
        'assessment_id' => $assessment->id,
        'question_id' => $questionLikert->id,
        'response_value' => 4,
        'response_text' => null,
    ]);

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => "texto antes {\n\"profile_analysis\":\n"],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $job = new ProcessAssessmentJob($assessment->id);

    expect(fn () => $job->handle(
        app(LLMService::class),
        app(AssessmentPromptService::class),
        app(RecommendationService::class),
    ))->toThrow(RuntimeException::class, 'Resposta do LLM nao veio em JSON valido.');

    $assessment->refresh();
    expect($assessment->status)->toBe('failed');

    Log::shouldHaveReceived('error')
        ->withArgs(function (string $message, array $context) use ($assessment) {
            return $message === 'assessment.job.failed'
                && ($context['assessment_id'] ?? null) === $assessment->id
                && ($context['attempt'] ?? null) === 1
                && ($context['max_tries'] ?? null) === 2
                && ($context['exception_class'] ?? null) === RuntimeException::class
                && ($context['exhausted_retries'] ?? null) === false;
        })
        ->once();

    Log::shouldHaveReceived('error')
        ->withArgs(function (string $message, array $context) {
            return $message === 'llm.json.decode_failed'
                && ($context['provider'] ?? null) === 'gemini'
                && ($context['model'] ?? null) === 'gemini-2.5-flash'
                && is_string($context['preview'] ?? null)
                && strlen($context['preview']) > 0;
        })
        ->once();
});

test('process assessment job failed hook logs final failure context', function () {
    Log::spy();

    $job = new ProcessAssessmentJob(1234);
    $job->failed(new RuntimeException('boom'));

    Log::shouldHaveReceived('error')
        ->withArgs(function (string $message, array $context) {
            return $message === 'assessment.job.failed'
                && ($context['assessment_id'] ?? null) === 1234
                && ($context['exception_class'] ?? null) === RuntimeException::class
                && ($context['message'] ?? null) === 'boom'
                && ($context['exhausted_retries'] ?? null) === true;
        })
        ->once();
});
