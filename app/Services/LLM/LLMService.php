<?php

namespace App\Services\LLM;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class LLMService
{
    private ?string $lastGeminiHttpBody = null;

    /**
     * Gera uma resposta em JSON a partir do prompt.
     *
     * @return array<string, mixed>
     */
    public function generateJson(string $prompt, ?array $responseSchema = null): array
    {
        $rawText = $this->generateText($prompt, true, $responseSchema);

        return $this->decodeJsonResponse($rawText);
    }

    /**
     * Gera JSON com schema estruturado para o fluxo de avaliacao.
     *
     * @return array<string, mixed>
     */
    public function generateAssessmentJson(string $prompt): array
    {
        return $this->generateJson($prompt, $this->assessmentResponseSchema());
    }

    /**
     * Gera texto bruto com o provider configurado.
     */
    public function generateText(string $prompt, bool $forceJson = false, ?array $responseSchema = null): string
    {
        $provider = (string) config('llm.provider', 'gemini');

        return match ($provider) {
            'gemini' => $this->callGemini($prompt, $forceJson, $responseSchema),
            default => throw new RuntimeException(
                "Provider '{$provider}' ainda nao esta implementado. Defina LLM_PROVIDER=gemini."
            ),
        };
    }

    private function callGemini(string $prompt, bool $forceJson, ?array $responseSchema = null): string
    {
        $this->lastGeminiHttpBody = null;

        $apiKey = (string) config('llm.gemini.api_key');
        $baseUrl = rtrim((string) config('llm.gemini.base_url', ''), '/');
        $model = (string) config('llm.gemini.model', 'gemini-2.5-flash');
        $defaultMaxOutputTokens = max((int) config('llm.gemini.max_output_tokens', 4096), 1);

        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY nao configurada.');
        }

        if ($baseUrl === '') {
            throw new RuntimeException('GEMINI_BASE_URL nao configurada.');
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => (float) config('llm.gemini.temperature', 0.2),
                'maxOutputTokens' => $defaultMaxOutputTokens,
            ],
        ];

        if ($forceJson) {
            $payload['generationConfig']['maxOutputTokens'] = max(
                (int) config('llm.gemini.json_max_output_tokens', $defaultMaxOutputTokens),
                1
            );
            $payload['generationConfig']['responseMimeType'] = 'application/json';

            if (
                $responseSchema !== null
                && (bool) config('llm.gemini.use_response_schema', true)
            ) {
                $payload['generationConfig']['responseSchema'] = $responseSchema;
            }

            $payload['generationConfig']['thinkingConfig'] = [
                'thinkingBudget' => (int) config('llm.gemini.json_thinking_budget', 0),
            ];
        }

        $attempts = max((int) config('llm.retry_attempts', 1), 1);
        $timeout = max((int) config('llm.timeout', 30), 1);

        $lastException = null;
        $url = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $response = Http::timeout($timeout)
                    ->acceptJson()
                    ->asJson()
                    ->post($url, $payload)
                    ->throw();

                $this->lastGeminiHttpBody = (string) $response->body();
                $json = $response->json();
                $text = data_get($json, 'candidates.0.content.parts.0.text');
                $finishReason = (string) data_get($json, 'candidates.0.finishReason', '');

                if ($finishReason === 'MAX_TOKENS') {
                    Log::warning('llm.gemini.response.truncated', [
                        'provider' => 'gemini',
                        'model' => $model,
                        'attempt' => $attempt,
                        'attempts' => $attempts,
                        'finish_reason' => $finishReason,
                        'prompt_token_count' => data_get($json, 'usageMetadata.promptTokenCount'),
                        'candidate_token_count' => data_get($json, 'usageMetadata.candidatesTokenCount'),
                        'thoughts_token_count' => data_get($json, 'usageMetadata.thoughtsTokenCount'),
                        'configured_max_output_tokens' => $payload['generationConfig']['maxOutputTokens'] ?? null,
                    ]);

                    throw new RuntimeException(
                        'Resposta do Gemini foi truncada por limite de tokens (finishReason=MAX_TOKENS).'
                    );
                }

                if (!is_string($text) || trim($text) === '') {
                    throw new RuntimeException('Resposta vazia ou invalida retornada pelo Gemini.');
                }

                return trim($text);
            } catch (Throwable $exception) {
                $lastException = $exception;

                Log::warning('llm.gemini.request.retry', [
                    'provider' => 'gemini',
                    'model' => $model,
                    'attempt' => $attempt,
                    'attempts' => $attempts,
                    'error' => $exception->getMessage(),
                    'will_retry' => $attempt < $attempts,
                ]);

                if ($attempt < $attempts) {
                    // Backoff curto para erro transiente (rede/limite momentaneo).
                    usleep($attempt * 200000);
                }
            }
        }

        if ($lastException instanceof RuntimeException) {
            throw $lastException;
        }

        throw new RuntimeException(
            'Falha ao consultar Gemini apos tentativas configuradas.',
            previous: $lastException
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function assessmentResponseSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'required' => [
                'profile_analysis',
                'course_recommendations',
                'roadmap',
                'additional_advice',
            ],
            'properties' => [
                'profile_analysis' => $this->profileAnalysisSchema(),
                'course_recommendations' => [
                    'type' => 'ARRAY',
                    'items' => $this->courseRecommendationSchema(),
                ],
                'roadmap' => $this->roadmapSchema(),
                'additional_advice' => $this->additionalAdviceSchema(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function profileAnalysisSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'required' => [
                'summary',
                'personality_type',
                'strengths',
                'areas_to_develop',
                'career_values',
            ],
            'properties' => [
                'summary' => ['type' => 'STRING'],
                'personality_type' => ['type' => 'STRING'],
                'strengths' => $this->stringArraySchema(),
                'areas_to_develop' => $this->stringArraySchema(),
                'career_values' => $this->stringArraySchema(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function courseRecommendationSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'required' => [
                'course_name',
                'rank',
                'compatibility_score',
                'justification',
                'student_strengths_for_course',
                'potential_challenges',
                'career_paths',
            ],
            'properties' => [
                'course_name' => ['type' => 'STRING'],
                'rank' => ['type' => 'INTEGER'],
                'compatibility_score' => ['type' => 'NUMBER'],
                'justification' => ['type' => 'STRING'],
                'student_strengths_for_course' => $this->stringArraySchema(),
                'potential_challenges' => $this->stringArraySchema(),
                'career_paths' => [
                    'type' => 'ARRAY',
                    'items' => $this->careerPathSchema(),
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function careerPathSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'required' => [
                'title',
                'description',
                'average_salary_range',
                'market_demand',
                'key_skills',
                'growth_potential',
            ],
            'properties' => [
                'title' => ['type' => 'STRING'],
                'description' => ['type' => 'STRING'],
                'average_salary_range' => ['type' => 'STRING'],
                'market_demand' => ['type' => 'STRING'],
                'key_skills' => $this->stringArraySchema(),
                'growth_potential' => ['type' => 'STRING'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function roadmapSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'required' => [
                'short_term',
                'medium_term',
                'long_term',
                'certifications_to_consider',
                'books_recommended',
                'communities_to_join',
            ],
            'properties' => [
                'short_term' => [
                    'type' => 'ARRAY',
                    'items' => $this->roadmapGoalSchema(),
                ],
                'medium_term' => [
                    'type' => 'ARRAY',
                    'items' => $this->roadmapGoalSchema(),
                ],
                'long_term' => [
                    'type' => 'ARRAY',
                    'items' => $this->roadmapGoalSchema(),
                ],
                'certifications_to_consider' => $this->stringArraySchema(),
                'books_recommended' => $this->stringArraySchema(),
                'communities_to_join' => $this->stringArraySchema(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function roadmapGoalSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'required' => [
                'goal',
                'timeframe',
                'actions',
                'resources',
                'estimated_hours_week',
            ],
            'properties' => [
                'goal' => ['type' => 'STRING'],
                'timeframe' => ['type' => 'STRING'],
                'actions' => $this->stringArraySchema(),
                'resources' => $this->stringArraySchema(),
                'estimated_hours_week' => ['type' => 'STRING'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function additionalAdviceSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'required' => [
                'immediate_next_steps',
                'long_term_vision',
                'words_of_encouragement',
            ],
            'properties' => [
                'immediate_next_steps' => ['type' => 'STRING'],
                'long_term_vision' => ['type' => 'STRING'],
                'words_of_encouragement' => ['type' => 'STRING'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function stringArraySchema(): array
    {
        return [
            'type' => 'ARRAY',
            'items' => [
                'type' => 'STRING',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(string $rawText): array
    {
        $provider = (string) config('llm.provider', 'gemini');
        $model = (string) config('llm.gemini.model', 'gemini-2.5-flash');
        $normalized = $this->stripMarkdownCodeFence(trim($rawText));

        $decodeException = null;
        $decoded = $this->tryDecodeJsonObject($normalized, $decodeException);
        if (is_array($decoded)) {
            return $decoded;
        }

        $sanitized = $this->removeInvalidControlCharacters($normalized);
        if ($sanitized !== $normalized) {
            $decoded = $this->tryDecodeJsonObject($sanitized, $decodeException);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $extracted = $this->extractJsonObjectBlock($sanitized);
        if ($extracted !== null) {
            $decoded = $this->tryDecodeJsonObject($extracted, $decodeException);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $this->logJsonDecodeFailure($rawText, $provider, $model, $decodeException);

        throw new RuntimeException(
            'Resposta do LLM nao veio em JSON valido.',
            previous: $decodeException
        );
    }

    private function stripMarkdownCodeFence(string $text): string
    {
        if (!str_starts_with($text, '```')) {
            return $text;
        }

        $text = preg_replace('/^```[a-zA-Z]*\s*/', '', $text) ?? $text;
        $text = preg_replace('/\s*```$/', '', $text) ?? $text;

        return trim($text);
    }

    private function removeInvalidControlCharacters(string $text): string
    {
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? $text;
    }

    private function tryDecodeJsonObject(string $json, ?Throwable &$exception): ?array
    {
        try {
            /** @var mixed $decoded */
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $decodeError) {
            $exception = $decodeError;

            return null;
        }

        if (!is_array($decoded) || array_is_list($decoded)) {
            $exception = new RuntimeException('Resposta do LLM precisa ser um objeto JSON.');

            return null;
        }

        return $decoded;
    }

    private function extractJsonObjectBlock(string $text): ?string
    {
        $start = strpos($text, '{');
        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escaped = false;
        $length = strlen($text);

        for ($i = $start; $i < $length; $i++) {
            $char = $text[$i];

            if ($inString) {
                if ($escaped) {
                    $escaped = false;
                    continue;
                }

                if ($char === '\\') {
                    $escaped = true;
                    continue;
                }

                if ($char === '"') {
                    $inString = false;
                }

                continue;
            }

            if ($char === '"') {
                $inString = true;
                continue;
            }

            if ($char === '{') {
                $depth++;
                continue;
            }

            if ($char === '}') {
                $depth--;

                if ($depth === 0) {
                    return substr($text, $start, $i - $start + 1);
                }
            }
        }

        return null;
    }

    private function logJsonDecodeFailure(
        string $rawText,
        string $provider,
        string $model,
        ?Throwable $exception,
    ): void {
        $fullTextEscaped = $this->escapeControlCharactersForLog($rawText);
        $httpBody = $this->lastGeminiHttpBody;
        $httpBodyEscaped = $httpBody !== null ? $this->escapeControlCharactersForLog($httpBody) : null;

        Log::error('llm.json.decode_failed', [
            'provider' => $provider,
            'model' => $model,
            'response_length' => strlen($rawText),
            'response_sha1' => substr(sha1($rawText), 0, 12),
            'preview' => $this->safePreview($rawText, 600),
            'decode_error' => $exception?->getMessage(),
            'full_response_text' => $rawText,
            'full_response_text_escaped' => $fullTextEscaped,
            'full_response_text_base64' => base64_encode($rawText),
            'gemini_http_body' => $httpBody,
            'gemini_http_body_escaped' => $httpBodyEscaped,
        ]);
    }

    private function escapeControlCharactersForLog(string $text): string
    {
        $escaped = '';
        $length = strlen($text);

        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];
            $ord = ord($char);

            if ($ord >= 32 && $ord !== 127) {
                $escaped .= $char;
                continue;
            }

            $escaped .= match ($char) {
                "\n" => '\n',
                "\r" => '\r',
                "\t" => '\t',
                default => sprintf('\x%02X', $ord),
            };
        }

        return $escaped;
    }

    private function safePreview(string $text, int $limit): string
    {
        $sanitized = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $text) ?? $text;
        $sanitized = preg_replace('/\s+/u', ' ', trim($sanitized)) ?? trim($sanitized);

        if (strlen($sanitized) <= $limit) {
            return $sanitized;
        }

        return substr($sanitized, 0, $limit).'...';
    }
}
