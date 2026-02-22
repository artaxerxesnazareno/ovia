<?php

namespace App\Services\Assessment;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Recommendation;
use App\Models\Roadmap;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class RecommendationService
{
    /**
     * @param  array<string, mixed>  $llmResult
     */
    public function saveRecommendations(Assessment $assessment, array $llmResult): void
    {
        $courseRecommendations = data_get($llmResult, 'course_recommendations', []);
        if (!is_array($courseRecommendations) || empty($courseRecommendations)) {
            throw new RuntimeException('A resposta do LLM nao trouxe recomendacoes de cursos.');
        }

        $courses = Course::query()
            ->active()
            ->with('careerPaths')
            ->get();

        DB::transaction(function () use ($assessment, $llmResult, $courseRecommendations, $courses): void {
            $assessment->recommendations()->delete();

            $saved = 0;
            foreach ($courseRecommendations as $index => $courseRecommendation) {
                if ($saved >= 3) {
                    break;
                }

                if (!is_array($courseRecommendation)) {
                    continue;
                }

                $courseName = trim((string) ($courseRecommendation['course_name'] ?? ''));
                $course = $this->findCourseByName($courseName, $courses);

                if (!$course) {
                    continue;
                }

                $rank = $this->normalizeRank($courseRecommendation['rank'] ?? null, $index + 1);
                $compatibility = $this->normalizeScore($courseRecommendation['compatibility_score'] ?? null);
                $justification = trim((string) ($courseRecommendation['justification'] ?? 'Analise gerada automaticamente.'));
                $strengths = $this->normalizeStringArray($courseRecommendation['student_strengths_for_course'] ?? []);
                $challenges = $this->normalizeStringArray($courseRecommendation['potential_challenges'] ?? []);

                $recommendation = Recommendation::create([
                    'assessment_id' => $assessment->id,
                    'course_id' => $course->id,
                    'rank' => $rank,
                    'compatibility_score' => $compatibility,
                    'llm_analysis' => [
                        'profile_analysis' => data_get($llmResult, 'profile_analysis', []),
                        'course_recommendation' => $courseRecommendation,
                        'roadmap' => data_get($llmResult, 'roadmap', []),
                        'additional_advice' => data_get($llmResult, 'additional_advice', []),
                    ],
                    'justification' => $justification,
                    'strengths' => $strengths,
                    'challenges' => $challenges,
                ]);

                $this->saveRoadmap($recommendation, data_get($llmResult, 'roadmap', []));
                $saved++;
            }

            if ($saved === 0) {
                throw new RuntimeException('Nenhuma recomendacao foi persistida. Verifique nomes de cursos retornados pela IA.');
            }
        });
    }

    /**
     * @param  mixed  $roadmapData
     */
    private function saveRoadmap(Recommendation $recommendation, $roadmapData): void
    {
        $roadmap = is_array($roadmapData) ? $roadmapData : [];

        $short = $this->normalizeGoalArray($roadmap['short_term'] ?? []);
        $medium = $this->normalizeGoalArray($roadmap['medium_term'] ?? []);
        $long = $this->normalizeGoalArray($roadmap['long_term'] ?? []);

        Roadmap::create([
            'recommendation_id' => $recommendation->id,
            'short_term_goals' => $short,
            'medium_term_goals' => $medium,
            'long_term_goals' => $long,
            'resources' => $this->collectResources($short, $medium, $long),
            'certifications' => $this->normalizeStringArray($roadmap['certifications_to_consider'] ?? []),
            'books' => $this->normalizeStringArray($roadmap['books_recommended'] ?? []),
            'communities' => $this->normalizeStringArray($roadmap['communities_to_join'] ?? []),
            'progress' => [],
        ]);
    }

    private function normalizeRank(mixed $rank, int $fallback): int
    {
        if (!is_numeric($rank)) {
            return $fallback;
        }

        return max(1, (int) $rank);
    }

    private function normalizeScore(mixed $score): float
    {
        if (!is_numeric($score)) {
            return 0.0;
        }

        $normalized = (float) $score;
        $bounded = max(0, min($normalized, 100));

        return round($bounded, 2);
    }

    /**
     * @param  mixed  $value
     * @return array<int, string>
     */
    private function normalizeStringArray($value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  mixed  $value
     * @return array<int, array<string, mixed>>
     */
    private function normalizeGoalArray($value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(fn ($goal) => is_array($goal))
            ->map(function ($goal): array {
                return [
                    'goal' => trim((string) ($goal['goal'] ?? '')),
                    'timeframe' => trim((string) ($goal['timeframe'] ?? '')),
                    'actions' => $this->normalizeStringArray($goal['actions'] ?? []),
                    'resources' => $this->normalizeStringArray($goal['resources'] ?? []),
                    'estimated_hours_week' => trim((string) ($goal['estimated_hours_week'] ?? '')),
                ];
            })
            ->filter(fn ($goal) => $goal['goal'] !== '')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $short
     * @param  array<int, array<string, mixed>>  $medium
     * @param  array<int, array<string, mixed>>  $long
     * @return array<int, string>
     */
    private function collectResources(array $short, array $medium, array $long): array
    {
        return collect([$short, $medium, $long])
            ->flatten(1)
            ->pluck('resources')
            ->flatten(1)
            ->map(fn ($resource) => trim((string) $resource))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function findCourseByName(string $courseName, Collection $courses): ?Course
    {
        if ($courseName === '') {
            return null;
        }

        $normalizedInput = $this->normalizeCourseName($courseName);

        $exactMatch = $courses->first(function (Course $course) use ($normalizedInput) {
            return $this->normalizeCourseName((string) $course->name) === $normalizedInput;
        });

        if ($exactMatch instanceof Course) {
            return $exactMatch;
        }

        $containsMatch = $courses->first(function (Course $course) use ($normalizedInput) {
            $normalizedCourse = $this->normalizeCourseName((string) $course->name);

            return str_contains($normalizedCourse, $normalizedInput)
                || str_contains($normalizedInput, $normalizedCourse);
        });

        if ($containsMatch instanceof Course) {
            return $containsMatch;
        }

        $bestScore = 0.0;
        $bestCourse = null;

        $inputTokens = collect(explode(' ', $normalizedInput))->filter()->values();
        foreach ($courses as $course) {
            $normalizedCourse = $this->normalizeCourseName((string) $course->name);
            $courseTokens = collect(explode(' ', $normalizedCourse))->filter()->values();

            if ($inputTokens->isEmpty() || $courseTokens->isEmpty()) {
                continue;
            }

            $overlap = $inputTokens->intersect($courseTokens)->count();
            $score = $overlap / max($inputTokens->count(), $courseTokens->count());

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestCourse = $course;
            }
        }

        return $bestScore >= 0.45 ? $bestCourse : null;
    }

    private function normalizeCourseName(string $value): string
    {
        $normalized = Str::of(Str::ascii($value))
            ->lower()
            ->replace('licenciatura em ', '')
            ->replace('bacharelado em ', '')
            ->replace('curso de ', '')
            ->replaceMatches('/[^a-z0-9 ]+/', ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim();

        return (string) $normalized;
    }
}
