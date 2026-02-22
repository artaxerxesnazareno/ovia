<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAssessmentJob;
use App\Models\Assessment;
use App\Models\AssessmentResponse;
use App\Models\Question;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    /**
     * Página de introdução à avaliação
     */
    public function start(): Response
    {
        // Verificar se usuário já tem avaliação em andamento
        $existingAssessment = Assessment::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        // Contagem de questões por categoria para mostrar ao usuário
        $questionCounts = Question::where('is_active', true)
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $totalQuestions = array_sum($questionCounts);
        $estimatedTime = ceil($totalQuestions * 0.5); // ~30 segundos por questão

        return Inertia::render('Start', [
            'hasExistingAssessment' => !is_null($existingAssessment),
            'existingAssessmentId' => $existingAssessment?->id,
            'questionCounts' => $questionCounts,
            'totalQuestions' => $totalQuestions,
            'estimatedTime' => $estimatedTime,
        ]);
    }

    /**
     * Iniciar nova avaliação ou continuar existente
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            // Verificar se há avaliação em andamento
            $assessment = Assessment::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->first();

            if (!$assessment) {
                // Criar nova avaliação
                $assessment = Assessment::create([
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                    'started_at' => now(),
                ]);

                Log::info('Nova avaliação criada', [
                    'assessment_id' => $assessment->id,
                    'user_id' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('assessment.questions', [
                'assessmentId' => $assessment->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar avaliação', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', 'Erro ao iniciar avaliação. Tente novamente.');
        }
    }

    /**
     * Mostrar questionário com todas as seções
     */
    public function questions(Request $request, $assessmentId): Response
    {
        $assessment = Assessment::where('id', $assessmentId)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        // Buscar questões agrupadas por categoria, ordenadas
        $questions = Question::where('is_active', true)
            ->orderBy('category')
            ->orderBy('order')
            ->get()
            ->groupBy('category')
            ->map(function ($categoryQuestions) {
                return $categoryQuestions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'category' => $question->category,
                        'dimension' => $question->dimension,
                        'text' => $question->question_text,
                        'type' => $question->question_type,
                        'options' => $question->options ? json_decode($question->options) : null,
                        'weight' => $question->weight,
                        'order' => $question->order,
                        'is_required' => $question->is_required,
                    ];
                });
            });

        // Buscar respostas já salvas (se houver)
        $savedResponses = AssessmentResponse::where('assessment_id', $assessmentId)
            ->pluck('response_value', 'question_id')
            ->toArray();

        $savedTextResponses = AssessmentResponse::where('assessment_id', $assessmentId)
            ->whereNotNull('response_text')
            ->pluck('response_text', 'question_id')
            ->toArray();

        // Metadata das categorias
        $categoryMetadata = [
            'interests' => [
                'title' => 'Interesses Profissionais',
                'description' => 'Nesta seção, vamos conhecer suas áreas de interesse e atividades que mais te atraem.',
                'icon' => 'compass',
            ],
            'skills' => [
                'title' => 'Habilidades e Aptidões',
                'description' => 'Vamos avaliar suas principais habilidades e competências.',
                'icon' => 'star',
            ],
            'values' => [
                'title' => 'Valores e Motivações',
                'description' => 'Entenda o que é mais importante para você em uma carreira.',
                'icon' => 'heart',
            ],
            'personality' => [
                'title' => 'Personalidade',
                'description' => 'Conheça melhor seu perfil de personalidade profissional.',
                'icon' => 'user',
            ],
        ];

        return Inertia::render('Questions', [
            'assessment' => [
                'id' => $assessment->id,
                'started_at' => $assessment->started_at,
            ],
            'questions' => $questions,
            'savedResponses' => $savedResponses,
            'savedTextResponses' => $savedTextResponses,
            'categoryMetadata' => $categoryMetadata,
        ]);
    }

    /**
     * Salvar respostas (auto-save)
     */
    public function saveResponses(Request $request, $assessmentId)
    {
        $validated = $request->validate([
            'responses' => 'required|array',
            'responses.*.question_id' => 'required|exists:questions,id',
            'responses.*.response_value' => 'nullable|integer|min:1|max:5',
            'responses.*.response_text' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $assessment = Assessment::where('id', $assessmentId)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->firstOrFail();

            foreach ($validated['responses'] as $response) {
                AssessmentResponse::updateOrCreate(
                    [
                        'assessment_id' => $assessmentId,
                        'question_id' => $response['question_id'],
                    ],
                    [
                        'response_value' => $response['response_value'] ?? null,
                        'response_text' => $response['response_text'] ?? null,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Respostas salvas automaticamente',
                'saved_at' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar respostas', [
                'error' => $e->getMessage(),
                'assessment_id' => $assessmentId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar respostas',
            ], 500);
        }
    }

    /**
     * Submeter avaliação completa
     */
    public function submit(Request $request, $assessmentId)
    {
        $validated = $request->validate([
            'responses' => 'required|array',
            'responses.*.question_id' => 'required|exists:questions,id',
            'responses.*.response_value' => 'nullable|integer|min:1|max:5',
            'responses.*.response_text' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $assessment = Assessment::where('id', $assessmentId)
                ->where('user_id', auth()->id())
                ->first();

            if (!$assessment) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Avaliacao nao encontrada. Inicie uma nova avaliacao.',
                    'redirect_url' => route('assessment.start'),
                ], 404);
            }

            if ($assessment->status !== 'pending') {
                DB::rollBack();
                [$message, $redirectUrl] = $this->resolveSubmitConflictResponse($assessment);

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'redirect_url' => $redirectUrl,
                    'current_status' => $assessment->status,
                ], 409);
            }

            // Salvar todas as respostas
            foreach ($validated['responses'] as $response) {
                AssessmentResponse::updateOrCreate(
                    [
                        'assessment_id' => $assessmentId,
                        'question_id' => $response['question_id'],
                    ],
                    [
                        'response_value' => $response['response_value'] ?? null,
                        'response_text' => $response['response_text'] ?? null,
                    ]
                );
            }

            // Verificar se todas as questões obrigatórias foram respondidas
            $requiredQuestions = Question::where('is_active', true)
                ->where('is_required', true)
                ->pluck('id');

            $answeredQuestions = AssessmentResponse::where('assessment_id', $assessmentId)
                ->pluck('question_id');

            $missingQuestions = $requiredQuestions->diff($answeredQuestions);

            if ($missingQuestions->isNotEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor, responda todas as questões obrigatórias',
                    'missing_questions' => $missingQuestions->values(),
                ], 422);
            }

            // Atualizar status para processamento
            $assessment->update([
                'status' => 'processing',
                'completed_at' => null,
            ]);

            DB::commit();

            $queueConnection = (string) config('queue.default', 'sync');
            // Em local, afterResponse evita depender de worker para perceber o fluxo.
            // Em conexao sync, dispatch normal bloqueia o response e atrasa a tela de processamento.
            $dispatchAfterResponse = app()->environment('local') || $queueConnection === 'sync';

            if ($dispatchAfterResponse) {
                ProcessAssessmentJob::dispatchAfterResponse($assessment->id);
            } else {
                ProcessAssessmentJob::dispatch($assessment->id);
            }

            Log::info('assessment.submit.dispatched', [
                'assessment_id' => $assessment->id,
                'user_id' => auth()->id(),
                'queue_connection' => $queueConnection,
                'dispatch_mode' => $dispatchAfterResponse ? 'after_response' : 'queue',
                'dispatched_at' => now()->toISOString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avaliação enviada com sucesso!',
                'redirect_url' => route('assessment.processing', ['assessmentId' => $assessmentId]),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao submeter avaliação', [
                'error' => $e->getMessage(),
                'assessment_id' => $assessmentId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar avaliação. Tente novamente.',
            ], 500);
        }
    }

    private function resolveSubmitConflictResponse(Assessment $assessment): array
    {
        if ($assessment->status === 'completed') {
            return [
                'Esta avaliacao ja foi concluida.',
                route('results.show', ['assessmentId' => $assessment->id]),
            ];
        }

        if ($assessment->status === 'processing') {
            return [
                'Esta avaliacao ja foi enviada e esta em processamento.',
                route('assessment.processing', ['assessmentId' => $assessment->id]),
            ];
        }

        if ($assessment->status === 'failed') {
            $pendingAssessment = Assessment::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->latest('id')
                ->first();

            if ($pendingAssessment) {
                return [
                    'Encontramos uma avaliacao pendente mais recente. Vamos continuar por ela.',
                    route('assessment.questions', ['assessmentId' => $pendingAssessment->id]),
                ];
            }

            return [
                'A ultima tentativa falhou. Inicie uma nova avaliacao para tentar novamente.',
                route('assessment.start'),
            ];
        }

        return [
            'Esta avaliacao nao pode ser enviada neste momento.',
            route('assessment.start'),
        ];
    }

    /**
     * Página de processamento (loading)
     */
    public function processing($assessmentId)
    {
        $assessment = Assessment::where('id', $assessmentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($assessment->status === 'pending') {
            return redirect()->route('assessment.questions', [
                'assessmentId' => $assessment->id,
            ]);
        }

        $totalQuestions = Question::where('is_active', true)->count();

        return Inertia::render('Processing', [
            'assessment' => [
                'id' => $assessment->id,
                'status' => $assessment->status,
                'completed_at' => $assessment->completed_at,
                'total_questions' => $totalQuestions,
            ],
            'ux' => [
                'poll_interval_ms' => 2000,
                'timeout_seconds' => 30,
            ],
        ]);
    }

    /**
     * Verificar status do processamento (polling)
     */
    public function checkStatus($assessmentId)
    {
        $assessment = Assessment::where('id', $assessmentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $response = [
            'status' => $assessment->status,
            'completed_at' => $assessment->completed_at,
        ];

        if ($assessment->status === 'completed') {
            $response['redirect_url'] = route('results.show', ['assessmentId' => $assessmentId]);
        }

        if ($assessment->status === 'failed') {
            $response['message'] = 'Nao foi possivel concluir o processamento agora. Tente novamente em instantes.';
        }

        return response()->json($response);
    }

    /**
     * Página de resultados da avaliação
     */
    public function results($assessmentId)
    {
        $assessment = Assessment::query()
            ->with([
                'recommendations' => fn ($query) => $query->orderBy('rank'),
                'recommendations.course.careerPaths',
                'recommendations.roadmap',
            ])
            ->where('id', $assessmentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($assessment->status === 'pending') {
            return redirect()->route('assessment.questions', ['assessmentId' => $assessment->id]);
        }

        if ($assessment->status === 'processing') {
            return redirect()->route('assessment.processing', ['assessmentId' => $assessment->id]);
        }

        if ($assessment->status === 'failed') {
            return redirect()->route('assessment.processing', ['assessmentId' => $assessment->id]);
        }

        $recommendations = $assessment->recommendations;
        if ($recommendations->isEmpty()) {
            return redirect()->route('assessment.processing', ['assessmentId' => $assessment->id]);
        }

        /** @var Recommendation $primaryRecommendation */
        $primaryRecommendation = $recommendations->first();
        $primaryAnalysis = is_array($primaryRecommendation->llm_analysis)
            ? $primaryRecommendation->llm_analysis
            : [];

        $profileAnalysis = $this->extractProfileAnalysis($primaryAnalysis);
        $additionalAdvice = $this->extractAdditionalAdvice($primaryAnalysis);
        $roadmap = $this->extractRoadmap($primaryRecommendation, $primaryAnalysis);

        $formattedRecommendations = $recommendations
            ->map(fn (Recommendation $recommendation) => $this->formatRecommendation($recommendation))
            ->values();

        return Inertia::render('Results', [
            'assessment' => [
                'id' => $assessment->id,
                'status' => $assessment->status,
                'completed_at' => $assessment->completed_at,
                'processing_time_seconds' => $assessment->processing_time_seconds,
            ],
            'profileAnalysis' => $profileAnalysis,
            'recommendations' => $formattedRecommendations,
            'roadmap' => $roadmap,
            'additionalAdvice' => $additionalAdvice,
        ]);
    }

    /**
     * Página de detalhes de um curso recomendado
     */
    public function courseDetails($assessmentId, $rank)
    {
        $assessment = Assessment::query()
            ->with([
                'recommendations' => fn ($query) => $query->orderBy('rank'),
                'recommendations.course.careerPaths',
                'recommendations.roadmap',
            ])
            ->where('id', $assessmentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($assessment->status === 'pending') {
            return redirect()->route('assessment.questions', ['assessmentId' => $assessment->id]);
        }

        if ($assessment->status === 'processing' || $assessment->status === 'failed') {
            return redirect()->route('assessment.processing', ['assessmentId' => $assessment->id]);
        }

        $recommendations = $assessment->recommendations;
        if ($recommendations->isEmpty()) {
            return redirect()->route('assessment.processing', ['assessmentId' => $assessment->id]);
        }

        /** @var Recommendation $primaryRecommendation */
        $primaryRecommendation = $recommendations->first();
        $selectedRecommendation = $recommendations->firstWhere('rank', (int) $rank);

        if (!$selectedRecommendation instanceof Recommendation) {
            return redirect()->route('results.show', ['assessmentId' => $assessment->id]);
        }

        $primaryAnalysis = is_array($primaryRecommendation->llm_analysis)
            ? $primaryRecommendation->llm_analysis
            : [];

        $selectedAnalysis = is_array($selectedRecommendation->llm_analysis)
            ? $selectedRecommendation->llm_analysis
            : [];

        $analysisForProfile = !empty(data_get($selectedAnalysis, 'profile_analysis'))
            ? $selectedAnalysis
            : $primaryAnalysis;

        $profileAnalysis = $this->extractProfileAnalysis($analysisForProfile);
        $additionalAdvice = $this->extractAdditionalAdvice($analysisForProfile);
        $roadmap = $this->extractRoadmap($selectedRecommendation, $selectedAnalysis);

        $formattedRecommendations = $recommendations
            ->map(fn (Recommendation $recommendation) => $this->formatRecommendation($recommendation))
            ->values();

        return Inertia::render('ResultCourseDetails', [
            'assessment' => [
                'id' => $assessment->id,
                'status' => $assessment->status,
                'completed_at' => $assessment->completed_at,
                'processing_time_seconds' => $assessment->processing_time_seconds,
            ],
            'profileAnalysis' => $profileAnalysis,
            'selectedRecommendation' => $this->formatRecommendation($selectedRecommendation),
            'recommendations' => $formattedRecommendations,
            'roadmap' => $roadmap,
            'additionalAdvice' => $additionalAdvice,
        ]);
    }

    private function formatRecommendation(Recommendation $recommendation): array
    {
        $analysis = is_array($recommendation->llm_analysis) ? $recommendation->llm_analysis : [];
        $courseRecommendation = data_get($analysis, 'course_recommendation', []);
        $llmCareerPaths = data_get($courseRecommendation, 'career_paths', []);
        $course = $recommendation->course;

        $courseName = (string) ($course->name ?? 'Curso');
        $durationSemesters = $course?->duration_semesters ? (int) $course->duration_semesters : null;
        $durationYears = $durationSemesters ? round($durationSemesters / 2, 1) : null;
        $durationYearsLabel = $durationYears === null
            ? 'Duracao nao informada'
            : ((float) $durationYears === floor((float) $durationYears)
                ? (string) ((int) $durationYears).' anos'
                : str_replace('.', ',', (string) $durationYears).' anos');

        return [
            'course_name' => $courseName,
            'course_slug' => (string) ($course->slug ?? ''),
            'rank' => (int) $recommendation->rank,
            'compatibility_score' => (float) $recommendation->compatibility_score,
            'justification' => (string) $recommendation->justification,
            'student_strengths_for_course' => $recommendation->strengths ?? [],
            'potential_challenges' => $recommendation->challenges ?? [],
            'career_paths' => $this->extractCareerPaths($recommendation, $llmCareerPaths),
            'course' => [
                'description' => (string) ($course->description ?? ''),
                'duration_semesters' => $durationSemesters,
                'duration_years_label' => $durationYearsLabel,
                'course_type' => $this->deriveCourseType($courseName, $durationSemesters),
                'shifts' => collect($course->shifts ?? [])
                    ->filter(fn ($shift) => is_string($shift))
                    ->map(fn (string $shift) => $this->formatShiftLabel($shift))
                    ->values()
                    ->all(),
                'vacancies_per_year' => is_numeric($course?->vacancies_per_year)
                    ? (int) $course->vacancies_per_year
                    : null,
                'coordinator_name' => (string) ($course->coordinator_name ?? ''),
                'admission_requirements' => (string) ($course->admission_requirements ?? ''),
                'curriculum_topics' => $this->extractCurriculumTopics($course->curriculum ?? []),
            ],
        ];
    }

    private function deriveCourseType(string $courseName, ?int $durationSemesters): string
    {
        $normalized = strtolower($courseName);

        if (str_contains($normalized, 'tecnolog')) {
            return 'Tecnologo';
        }

        if (str_contains($normalized, 'licenciatura')) {
            return 'Licenciatura';
        }

        if ($durationSemesters !== null && $durationSemesters <= 4) {
            return 'Tecnologo';
        }

        if ($durationSemesters !== null && $durationSemesters >= 9) {
            return 'Licenciatura';
        }

        return 'Bacharelado';
    }

    private function formatShiftLabel(string $shift): string
    {
        return match (strtolower(trim($shift))) {
            'morning' => 'Manha',
            'afternoon' => 'Tarde',
            'evening' => 'Noite',
            'full_time' => 'Integral',
            default => ucfirst(trim($shift)),
        };
    }

    private function extractCurriculumTopics(mixed $curriculum): array
    {
        $topics = [];

        $collectTopics = function (mixed $value) use (&$topics, &$collectTopics): void {
            if (is_string($value)) {
                $topic = trim($value);
                if ($topic !== '') {
                    $topics[] = $topic;
                }
                return;
            }

            if (is_array($value)) {
                foreach ($value as $nestedValue) {
                    $collectTopics($nestedValue);
                }
            }
        };

        $collectTopics($curriculum);

        return collect($topics)->unique()->values()->all();
    }

    private function extractProfileAnalysis(array $analysis): array
    {
        $profile = data_get($analysis, 'profile_analysis', []);
        if (!is_array($profile)) {
            $profile = [];
        }

        return [
            'summary' => (string) ($profile['summary'] ?? 'Perfil em processamento.'),
            'personality_type' => (string) ($profile['personality_type'] ?? 'Perfil em definicao'),
            'strengths' => $this->normalizeStringArray($profile['strengths'] ?? []),
            'areas_to_develop' => $this->normalizeStringArray($profile['areas_to_develop'] ?? []),
            'career_values' => $this->normalizeStringArray($profile['career_values'] ?? []),
        ];
    }

    private function extractAdditionalAdvice(array $analysis): array
    {
        $advice = data_get($analysis, 'additional_advice', []);
        if (!is_array($advice)) {
            $advice = [];
        }

        return [
            'immediate_next_steps' => (string) ($advice['immediate_next_steps'] ?? ''),
            'long_term_vision' => (string) ($advice['long_term_vision'] ?? ''),
            'words_of_encouragement' => (string) ($advice['words_of_encouragement'] ?? ''),
        ];
    }

    private function extractRoadmap(Recommendation $recommendation, array $analysis): array
    {
        $roadmap = $recommendation->roadmap;

        if ($roadmap) {
            return [
                'short_term' => $roadmap->short_term_goals ?? [],
                'medium_term' => $roadmap->medium_term_goals ?? [],
                'long_term' => $roadmap->long_term_goals ?? [],
                'certifications_to_consider' => $roadmap->certifications ?? [],
                'books_recommended' => $roadmap->books ?? [],
                'communities_to_join' => $roadmap->communities ?? [],
            ];
        }

        $fallbackRoadmap = data_get($analysis, 'roadmap', []);
        if (!is_array($fallbackRoadmap)) {
            $fallbackRoadmap = [];
        }

        return [
            'short_term' => $fallbackRoadmap['short_term'] ?? [],
            'medium_term' => $fallbackRoadmap['medium_term'] ?? [],
            'long_term' => $fallbackRoadmap['long_term'] ?? [],
            'certifications_to_consider' => $fallbackRoadmap['certifications_to_consider'] ?? [],
            'books_recommended' => $fallbackRoadmap['books_recommended'] ?? [],
            'communities_to_join' => $fallbackRoadmap['communities_to_join'] ?? [],
        ];
    }

    private function extractCareerPaths(Recommendation $recommendation, mixed $llmCareerPaths): array
    {
        if (is_array($llmCareerPaths) && !empty($llmCareerPaths)) {
            return collect($llmCareerPaths)
                ->filter(fn ($careerPath) => is_array($careerPath))
                ->map(function ($careerPath) {
                    return [
                        'title' => (string) ($careerPath['title'] ?? ''),
                        'description' => (string) ($careerPath['description'] ?? ''),
                        'average_salary_range' => (string) ($careerPath['average_salary_range'] ?? ''),
                        'market_demand' => (string) ($careerPath['market_demand'] ?? ''),
                        'key_skills' => $this->normalizeStringArray($careerPath['key_skills'] ?? []),
                        'growth_potential' => (string) ($careerPath['growth_potential'] ?? ''),
                    ];
                })
                ->filter(fn ($careerPath) => $careerPath['title'] !== '')
                ->values()
                ->all();
        }

        if (!$recommendation->course) {
            return [];
        }

        return $recommendation->course->careerPaths
            ->map(function ($careerPath) {
                $salaryMin = $careerPath->average_salary_min ? number_format((float) $careerPath->average_salary_min, 0, ',', '.') : null;
                $salaryMax = $careerPath->average_salary_max ? number_format((float) $careerPath->average_salary_max, 0, ',', '.') : null;
                $salaryRange = ($salaryMin && $salaryMax) ? "{$salaryMin} - {$salaryMax} Kz/mes" : 'Nao informado';

                $marketDemand = match ($careerPath->market_demand) {
                    'very_high' => 'Muito alta',
                    'high' => 'Alta',
                    'medium' => 'Media',
                    'low' => 'Baixa',
                    default => 'Nao informado',
                };

                return [
                    'title' => (string) $careerPath->title,
                    'description' => (string) $careerPath->description,
                    'average_salary_range' => $salaryRange,
                    'market_demand' => $marketDemand,
                    'key_skills' => $careerPath->key_skills ?? [],
                    'growth_potential' => is_array($careerPath->growth_potential)
                        ? implode(', ', $careerPath->growth_potential)
                        : (string) ($careerPath->growth_potential ?? ''),
                ];
            })
            ->values()
            ->all();
    }

    private function normalizeStringArray(mixed $value): array
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
}
