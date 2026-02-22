<?php

namespace App\Jobs;

use App\Models\Assessment;
use App\Services\Assessment\AssessmentPromptService;
use App\Services\Assessment\RecommendationService;
use App\Services\LLM\LLMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessAssessmentJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 120;

    public int $tries = 2;

    public function __construct(public int $assessmentId)
    {
    }

    public function handle(
        LLMService $llmService,
        AssessmentPromptService $promptService,
        RecommendationService $recommendationService,
    ): void {
        $assessment = Assessment::query()
            ->with(['user', 'responses.question'])
            ->find($this->assessmentId);

        if (!$assessment) {
            return;
        }

        if ($assessment->status === 'completed') {
            return;
        }

        Log::info('assessment.job.started', [
            'assessment_id' => $assessment->id,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
        ]);

        $assessment->update([
            'status' => 'processing',
            'completed_at' => null,
        ]);

        $startedAt = now();

        try {
            $prompt = $promptService->buildPrompt($assessment);
            $llmResult = $llmService->generateAssessmentJson($prompt);

            $recommendationService->saveRecommendations($assessment, $llmResult);

            $processingTimeSeconds = now()->diffInSeconds($startedAt);

            $assessment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'processing_time_seconds' => $processingTimeSeconds,
            ]);

            Log::info('assessment.job.completed', [
                'assessment_id' => $assessment->id,
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
                'processing_time_seconds' => $processingTimeSeconds,
            ]);
        } catch (Throwable $exception) {
            Log::error('assessment.job.failed', [
                'assessment_id' => $assessment->id,
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
                'exception_class' => $exception::class,
                'message' => $exception->getMessage(),
                'exhausted_retries' => false,
            ]);

            $assessment->update([
                'status' => 'failed',
            ]);

            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('assessment.job.failed', [
            'assessment_id' => $this->assessmentId,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
            'exception_class' => $exception::class,
            'message' => $exception->getMessage(),
            'exhausted_retries' => true,
        ]);
    }
}
