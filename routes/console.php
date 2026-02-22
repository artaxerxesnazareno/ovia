<?php

use App\Services\LLM\LLMService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('llm:smoke-test {--prompt=} {--prompt-file=}', function () {
    $promptOption = (string) $this->option('prompt');
    $promptFile = (string) $this->option('prompt-file');

    $prompt = $promptOption;
    if ($prompt === '' && $promptFile !== '') {
        if (!is_file($promptFile)) {
            $this->error("Arquivo de prompt nao encontrado: {$promptFile}");
            return self::FAILURE;
        }

        $prompt = (string) file_get_contents($promptFile);
    }

    if ($prompt === '') {
        $prompt = <<<PROMPT
Retorne apenas um JSON valido com a estrutura:
{
  "status": "ok",
  "provider": "configured_provider",
  "message": "smoke test"
}
PROMPT;
    }

    try {
        /** @var LLMService $llm */
        $llm = app(LLMService::class);
        $result = $llm->generateJson($prompt);

        $this->info('LLM smoke test concluido com sucesso.');
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return self::SUCCESS;
    } catch (\Throwable $exception) {
        $this->error('Falha no smoke test de LLM: '.$exception->getMessage());
        return self::FAILURE;
    }
})->purpose('Testa chamada basica no provider LLM configurado');
