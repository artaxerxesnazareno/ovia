<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Recommendation;
use App\Models\Roadmap;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AppDashboardController extends Controller
{
    public function __invoke(): Response
    {
        $userId = auth()->id();

        $assessments = Assessment::query()
            ->with([
                'recommendations' => fn ($query) => $query
                    ->with(['course', 'roadmap'])
                    ->orderBy('rank'),
            ])
            ->where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->get();

        /** @var Assessment|null $latestCompletedAssessment */
        $latestCompletedAssessment = $assessments
            ->filter(fn (Assessment $assessment) => $assessment->status === 'completed')
            ->sortByDesc(fn (Assessment $assessment) => $assessment->completed_at?->timestamp ?? $assessment->updated_at?->timestamp ?? 0)
            ->first();

        $recommendations = $latestCompletedAssessment?->recommendations ?? collect();

        /** @var Recommendation|null $primaryRecommendation */
        $primaryRecommendation = $recommendations->sortBy('rank')->first();
        $roadmap = $primaryRecommendation?->roadmap;
        $progressStats = $this->buildProgressStats($roadmap);

        return Inertia::render('Index', [
            'initialView' => 'dashboard',
            'dashboardData' => [
                'roadmapProgress' => $this->resolveRoadmapProgress($progressStats, $assessments),
                'currentStage' => $this->resolveCurrentStage($progressStats, $assessments),
                'remainingActivities' => $this->resolveRemainingActivities($progressStats, $assessments),
                'assessments' => $this->buildAssessments($assessments),
                'recommendations' => $this->buildRecommendations($latestCompletedAssessment?->id, $recommendations),
                'tasks' => $this->buildTasks($roadmap, $progressStats['map'], $assessments),
            ],
        ]);
    }

    /**
     * @return array{map: array<string, mixed>, total: int, completed: int}
     */
    private function buildProgressStats(?Roadmap $roadmap): array
    {
        $progress = is_array($roadmap?->progress) ? $roadmap->progress : [];

        $completed = collect($progress)
            ->filter(fn ($value) => $this->isCompletedFlag($value))
            ->count();

        return [
            'map' => $progress,
            'total' => count($progress),
            'completed' => $completed,
        ];
    }

    /**
     * @param  array{map: array<string, mixed>, total: int, completed: int}  $progressStats
     */
    private function resolveRoadmapProgress(array $progressStats, Collection $assessments): int
    {
        if ($progressStats['total'] > 0) {
            return (int) round(($progressStats['completed'] / $progressStats['total']) * 100);
        }

        if ($assessments->isEmpty()) {
            return 0;
        }

        $completedAssessments = $assessments
            ->filter(fn (Assessment $assessment) => $assessment->status === 'completed')
            ->count();

        return (int) round(($completedAssessments / $assessments->count()) * 100);
    }

    /**
     * @param  array{map: array<string, mixed>, total: int, completed: int}  $progressStats
     */
    private function resolveCurrentStage(array $progressStats, Collection $assessments): string
    {
        if ($progressStats['total'] > 0) {
            $progress = (int) round(($progressStats['completed'] / $progressStats['total']) * 100);

            if ($progress < 34) {
                return 'Exploracao';
            }

            if ($progress < 67) {
                return 'Planejamento';
            }

            return 'Execucao';
        }

        if ($assessments->isEmpty()) {
            return 'Nao iniciado';
        }

        if ($assessments->contains(fn (Assessment $assessment) => $assessment->status === 'processing')) {
            return 'Analise em andamento';
        }

        if ($assessments->contains(fn (Assessment $assessment) => $assessment->status === 'pending')) {
            return 'Questionario em progresso';
        }

        if ($assessments->contains(fn (Assessment $assessment) => $assessment->status === 'completed')) {
            return 'Exploracao';
        }

        return 'Revisao';
    }

    /**
     * @param  array{map: array<string, mixed>, total: int, completed: int}  $progressStats
     */
    private function resolveRemainingActivities(array $progressStats, Collection $assessments): int
    {
        if ($progressStats['total'] > 0) {
            return max(0, $progressStats['total'] - $progressStats['completed']);
        }

        return $assessments
            ->filter(fn (Assessment $assessment) => in_array($assessment->status, ['pending', 'processing'], true))
            ->count();
    }

    private function buildAssessments(Collection $assessments): array
    {
        return $assessments
            ->take(6)
            ->map(function (Assessment $assessment): array {
                $status = $this->normalizeAssessmentStatus((string) $assessment->status);

                /** @var Recommendation|null $topRecommendation */
                $topRecommendation = $assessment->recommendations->sortBy('rank')->first();
                $score = $topRecommendation ? (int) round((float) $topRecommendation->compatibility_score) : null;
                $courseName = trim((string) ($topRecommendation?->course?->name ?? ''));

                return [
                    'id' => (int) $assessment->id,
                    'title' => 'Avaliacao Vocacional IA',
                    'completedAt' => $assessment->completed_at?->toISOString(),
                    'status' => $status,
                    'resultLabel' => $this->assessmentResultLabel($status, $score),
                    'resultHint' => $this->assessmentResultHint($status, $courseName),
                    'actionLabel' => $this->assessmentActionLabel($status),
                    'actionUrl' => $this->assessmentActionUrl($status, (int) $assessment->id),
                ];
            })
            ->values()
            ->all();
    }

    private function normalizeAssessmentStatus(string $status): string
    {
        return match ($status) {
            'completed', 'pending', 'processing', 'failed' => $status,
            default => 'pending',
        };
    }

    private function assessmentResultLabel(string $status, ?int $score): ?string
    {
        return match ($status) {
            'completed' => $score !== null ? "Score: {$score}/100" : 'Resultado disponivel',
            'processing' => 'Em processamento',
            'failed' => 'Falha no processamento',
            default => null,
        };
    }

    private function assessmentResultHint(string $status, string $courseName): ?string
    {
        return match ($status) {
            'completed' => $courseName !== '' ? Str::limit($courseName, 42) : 'Recomendacoes geradas',
            'processing' => 'IA analisando respostas',
            'failed' => 'Tente novamente',
            default => null,
        };
    }

    private function assessmentActionLabel(string $status): string
    {
        return match ($status) {
            'completed' => 'Ver resultado',
            'processing' => 'Acompanhar',
            'failed' => 'Retomar',
            default => 'Continuar',
        };
    }

    private function assessmentActionUrl(string $status, int $assessmentId): string
    {
        return match ($status) {
            'completed' => route('results.show', ['assessmentId' => $assessmentId]),
            'processing', 'failed' => route('assessment.processing', ['assessmentId' => $assessmentId]),
            default => route('assessment.questions', ['assessmentId' => $assessmentId]),
        };
    }

    private function buildRecommendations(?int $assessmentId, Collection $recommendations): array
    {
        if ($assessmentId === null) {
            return [];
        }

        return $recommendations
            ->sortBy('rank')
            ->take(3)
            ->map(function (Recommendation $recommendation) use ($assessmentId): array {
                $title = trim((string) ($recommendation->course?->name ?? ''));
                $compatibility = (int) round((float) $recommendation->compatibility_score);
                $description = trim((string) ($recommendation->justification ?? ''));

                return [
                    'id' => (int) $recommendation->id,
                    'title' => $title !== '' ? $title : 'Curso recomendado',
                    'description' => Str::limit($description !== '' ? $description : 'Recomendacao gerada com base no seu perfil.', 95),
                    'compatibility' => max(0, min($compatibility, 100)),
                    'actionUrl' => route('results.course', [
                        'assessmentId' => $assessmentId,
                        'rank' => (int) $recommendation->rank,
                    ]),
                ];
            })
            ->values()
            ->all();
    }

    private function buildTasks(?Roadmap $roadmap, array $progressMap, Collection $assessments): array
    {
        $goalTasks = $this->buildTasksFromRoadmap($roadmap, $progressMap);
        if (!empty($goalTasks)) {
            return $goalTasks;
        }

        $fallbackTasks = collect();

        $processingAssessment = $assessments->first(fn (Assessment $assessment) => $assessment->status === 'processing');
        if ($processingAssessment instanceof Assessment) {
            $fallbackTasks->push([
                'id' => 'processing-'.$processingAssessment->id,
                'label' => 'Aguardar processamento da avaliacao atual',
                'done' => false,
            ]);
        }

        $pendingAssessment = $assessments->first(fn (Assessment $assessment) => $assessment->status === 'pending');
        if ($pendingAssessment instanceof Assessment) {
            $fallbackTasks->push([
                'id' => 'pending-'.$pendingAssessment->id,
                'label' => 'Concluir o questionario em andamento',
                'done' => false,
            ]);
        }

        $completedCount = $assessments
            ->filter(fn (Assessment $assessment) => $assessment->status === 'completed')
            ->count();

        if ($completedCount > 0) {
            $fallbackTasks->push([
                'id' => 'review-recommendations',
                'label' => 'Revisar recomendacoes geradas',
                'done' => true,
            ]);
        }

        return $fallbackTasks
            ->take(3)
            ->values()
            ->all();
    }

    private function buildTasksFromRoadmap(?Roadmap $roadmap, array $progressMap): array
    {
        if (!$roadmap) {
            return [];
        }

        $goals = collect([
            ...$this->extractGoalLabels($roadmap->short_term_goals),
            ...$this->extractGoalLabels($roadmap->medium_term_goals),
            ...$this->extractGoalLabels($roadmap->long_term_goals),
        ])->take(3)->values();

        return $goals
            ->map(function (string $goal, int $index) use ($progressMap): array {
                $goalId = 'goal-'.$index;
                $done = $this->isCompletedFlag($progressMap[$goalId] ?? $progressMap[$goal] ?? false);

                return [
                    'id' => $goalId,
                    'label' => $goal,
                    'done' => $done,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  mixed  $goals
     * @return array<int, string>
     */
    private function extractGoalLabels($goals): array
    {
        if (!is_array($goals)) {
            return [];
        }

        return collect($goals)
            ->filter(fn ($goal) => is_array($goal))
            ->map(fn (array $goal) => trim((string) ($goal['goal'] ?? '')))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  mixed  $value
     */
    private function isCompletedFlag($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        if (is_string($value)) {
            return in_array(strtolower(trim($value)), ['1', 'true', 'done', 'completed', 'ok'], true);
        }

        return false;
    }
}
