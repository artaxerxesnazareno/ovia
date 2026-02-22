<?php

use App\Services\LLM\LLMService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

test('llm service decodes gemini json response', function () {
    config()->set('llm.provider', 'gemini');
    config()->set('llm.gemini.api_key', 'test-api-key');
    config()->set('llm.gemini.model', 'gemini-2.5-flash');
    config()->set('llm.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    config()->set('llm.timeout', 10);
    config()->set('llm.retry_attempts', 1);

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => '{"status":"ok","provider":"gemini"}'],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $service = app(LLMService::class);
    $result = $service->generateJson('retorne JSON');

    expect($result)->toBe([
        'status' => 'ok',
        'provider' => 'gemini',
    ]);

    Http::assertSent(function ($request) {
        $payload = json_decode($request->body(), true);

        return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=test-api-key'
            && ($payload['generationConfig']['responseMimeType'] ?? null) === 'application/json';
    });
});

test('llm service sends gemini response schema for assessment json generation', function () {
    config()->set('llm.provider', 'gemini');
    config()->set('llm.gemini.api_key', 'test-api-key');
    config()->set('llm.gemini.model', 'gemini-2.5-flash');
    config()->set('llm.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    config()->set('llm.gemini.use_response_schema', true);
    config()->set('llm.timeout', 10);
    config()->set('llm.retry_attempts', 1);

    $llmPayload = [
        'profile_analysis' => [
            'summary' => 'Resumo',
            'personality_type' => 'Analitico',
            'strengths' => ['Logica'],
            'areas_to_develop' => ['Comunicacao'],
            'career_values' => ['Crescimento'],
        ],
        'course_recommendations' => [
            [
                'course_name' => 'Ciencias da Computacao',
                'rank' => 1,
                'compatibility_score' => 90,
                'justification' => 'Boa aderencia',
                'student_strengths_for_course' => ['Raciocinio'],
                'potential_challenges' => ['Equipe'],
                'career_paths' => [
                    [
                        'title' => 'Desenvolvedor',
                        'description' => 'Descricao',
                        'average_salary_range' => '150.000 - 250.000 Kz/mes',
                        'market_demand' => 'Alta',
                        'key_skills' => ['PHP'],
                        'growth_potential' => 'Alta',
                    ],
                ],
            ],
        ],
        'roadmap' => [
            'short_term' => [
                [
                    'goal' => 'Base',
                    'timeframe' => '0-6 meses',
                    'actions' => ['Acao 1'],
                    'resources' => ['Recurso 1'],
                    'estimated_hours_week' => '4-6 horas',
                ],
            ],
            'medium_term' => [],
            'long_term' => [],
            'certifications_to_consider' => ['Cert 1'],
            'books_recommended' => ['Livro 1'],
            'communities_to_join' => ['Comunidade 1'],
        ],
        'additional_advice' => [
            'immediate_next_steps' => 'Passo 1',
            'long_term_vision' => 'Visao',
            'words_of_encouragement' => 'Mensagem',
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

    $service = app(LLMService::class);
    $result = $service->generateAssessmentJson('retorne JSON estruturado');

    expect($result)->toBeArray()
        ->and($result['profile_analysis']['summary'])->toBe('Resumo')
        ->and($result['course_recommendations'][0]['rank'])->toBe(1);

    Http::assertSent(function ($request) {
        $payload = json_decode($request->body(), true);
        $generationConfig = $payload['generationConfig'] ?? [];
        $responseSchema = $generationConfig['responseSchema'] ?? [];

        return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=test-api-key'
            && ($generationConfig['responseMimeType'] ?? null) === 'application/json'
            && ($responseSchema['type'] ?? null) === 'OBJECT'
            && ($responseSchema['properties']['profile_analysis']['type'] ?? null) === 'OBJECT'
            && ($responseSchema['properties']['course_recommendations']['type'] ?? null) === 'ARRAY';
    });
});

test('llm smoke test command succeeds', function () {
    config()->set('llm.provider', 'gemini');
    config()->set('llm.gemini.api_key', 'test-api-key');
    config()->set('llm.gemini.model', 'gemini-2.5-flash');
    config()->set('llm.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    config()->set('llm.timeout', 10);
    config()->set('llm.retry_attempts', 1);

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => '{"status":"ok","provider":"gemini","message":"smoke"}'],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->artisan('llm:smoke-test')
        ->expectsOutputToContain('LLM smoke test concluido com sucesso.')
        ->assertExitCode(0);
});

test('llm service decodes json after removing invalid control characters', function () {
    config()->set('llm.provider', 'gemini');
    config()->set('llm.gemini.api_key', 'test-api-key');
    config()->set('llm.gemini.model', 'gemini-2.5-flash');
    config()->set('llm.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    config()->set('llm.timeout', 10);
    config()->set('llm.retry_attempts', 1);

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => "{\n\"status\":\"ok\",\x07\"provider\":\"gemini\"}"],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $service = app(LLMService::class);
    $result = $service->generateJson('retorne JSON');

    expect($result)->toBe([
        'status' => 'ok',
        'provider' => 'gemini',
    ]);
});

test('llm service logs safe decode failure metadata when json cannot be recovered', function () {
    Log::spy();

    config()->set('llm.provider', 'gemini');
    config()->set('llm.gemini.api_key', 'test-api-key');
    config()->set('llm.gemini.model', 'gemini-2.5-flash');
    config()->set('llm.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    config()->set('llm.timeout', 10);
    config()->set('llm.retry_attempts', 1);

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => "texto fora de json: {\"foo\": "],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $service = app(LLMService::class);

    expect(fn () => $service->generateJson('retorne JSON'))
        ->toThrow(RuntimeException::class, 'Resposta do LLM nao veio em JSON valido.');

    Log::shouldHaveReceived('error')
        ->withArgs(function (string $message, array $context) {
            return $message === 'llm.json.decode_failed'
                && ($context['provider'] ?? null) === 'gemini'
                && ($context['model'] ?? null) === 'gemini-2.5-flash'
                && is_int($context['response_length'] ?? null)
                && is_string($context['response_sha1'] ?? null)
                && strlen($context['response_sha1']) === 12
                && is_string($context['preview'] ?? null)
                && strlen($context['preview']) > 0
                && is_string($context['decode_error'] ?? null)
                && strlen($context['decode_error']) > 0
                && is_string($context['full_response_text'] ?? null)
                && strlen($context['full_response_text']) > 0
                && is_string($context['full_response_text_escaped'] ?? null)
                && strlen($context['full_response_text_escaped']) > 0
                && is_string($context['full_response_text_base64'] ?? null)
                && strlen($context['full_response_text_base64']) > 0
                && is_string($context['gemini_http_body'] ?? null)
                && strlen($context['gemini_http_body']) > 0
                && is_string($context['gemini_http_body_escaped'] ?? null)
                && strlen($context['gemini_http_body_escaped']) > 0;
        })
        ->once();
});

test('llm service logs retry warning when gemini request fails before succeeding', function () {
    Log::spy();

    config()->set('llm.provider', 'gemini');
    config()->set('llm.gemini.api_key', 'test-api-key');
    config()->set('llm.gemini.model', 'gemini-2.5-flash');
    config()->set('llm.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    config()->set('llm.timeout', 10);
    config()->set('llm.retry_attempts', 2);

    Http::fakeSequence()
        ->pushStatus(500)
        ->push([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => '{"status":"ok","provider":"gemini"}'],
                        ],
                    ],
                ],
            ],
        ], 200);

    $service = app(LLMService::class);
    $result = $service->generateJson('retorne JSON');

    expect($result)->toBe([
        'status' => 'ok',
        'provider' => 'gemini',
    ]);

    Log::shouldHaveReceived('warning')
        ->withArgs(function (string $message, array $context) {
            return $message === 'llm.gemini.request.retry'
                && ($context['provider'] ?? null) === 'gemini'
                && ($context['model'] ?? null) === 'gemini-2.5-flash'
                && ($context['attempt'] ?? null) === 1
                && ($context['attempts'] ?? null) === 2
                && ($context['will_retry'] ?? null) === true
                && is_string($context['error'] ?? null);
        })
        ->once();
});

test('llm service retries when gemini response is truncated by max tokens', function () {
    Log::spy();

    config()->set('llm.provider', 'gemini');
    config()->set('llm.gemini.api_key', 'test-api-key');
    config()->set('llm.gemini.model', 'gemini-2.5-flash');
    config()->set('llm.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    config()->set('llm.retry_attempts', 2);
    config()->set('llm.gemini.json_max_output_tokens', 1200);
    config()->set('llm.gemini.json_thinking_budget', 0);

    Http::fakeSequence()
        ->push([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => '{"status":"ok"'],
                        ],
                    ],
                    'finishReason' => 'MAX_TOKENS',
                ],
            ],
            'usageMetadata' => [
                'promptTokenCount' => 100,
                'candidatesTokenCount' => 1200,
                'thoughtsTokenCount' => 0,
            ],
        ], 200)
        ->push([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => '{"status":"ok","provider":"gemini"}'],
                        ],
                    ],
                    'finishReason' => 'STOP',
                ],
            ],
        ], 200);

    $service = app(LLMService::class);
    $result = $service->generateJson('retorne JSON');

    expect($result)->toBe([
        'status' => 'ok',
        'provider' => 'gemini',
    ]);

    Log::shouldHaveReceived('warning')
        ->withArgs(function (string $message, array $context) {
            return $message === 'llm.gemini.response.truncated'
                && ($context['finish_reason'] ?? null) === 'MAX_TOKENS'
                && ($context['configured_max_output_tokens'] ?? null) === 1200;
        })
        ->once();
});
