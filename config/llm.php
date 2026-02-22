<?php

return [
    /*
    |--------------------------------------------------------------------------
    | LLM Provider
    |--------------------------------------------------------------------------
    |
    | Provider atual para as chamadas de IA. Nesta sprint o fluxo pronto
    | implementa o provider "gemini".
    |
    */
    'provider' => env('LLM_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | Request behavior
    |--------------------------------------------------------------------------
    */
    'timeout' => (int) env('LLM_TIMEOUT', 30),
    'retry_attempts' => (int) env('LLM_RETRY_ATTEMPTS', 3),
    'cache_ttl' => (int) env('LLM_CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Gemini
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'temperature' => (float) env('GEMINI_TEMPERATURE', 0.2),
        'max_output_tokens' => (int) env('GEMINI_MAX_OUTPUT_TOKENS', 4096),
        'json_max_output_tokens' => (int) env('GEMINI_JSON_MAX_OUTPUT_TOKENS', 8192),
        'json_thinking_budget' => (int) env('GEMINI_JSON_THINKING_BUDGET', 0),
        'use_response_schema' => filter_var(env('GEMINI_USE_RESPONSE_SCHEMA', true), FILTER_VALIDATE_BOOL),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI (placeholder para futuras sprints)
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],
];
