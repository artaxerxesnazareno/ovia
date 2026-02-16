<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Question;
use App\Models\AssessmentResponse;
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
                ->where('status', 'pending')
                ->firstOrFail();

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
                'completed_at' => now(),
            ]);

            DB::commit();

            // TODO: Disparar job para processar com LLM
            // ProcessAssessmentJob::dispatch($assessment);

            Log::info('Avaliação submetida', [
                'assessment_id' => $assessment->id,
                'user_id' => auth()->id(),
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
            $response['redirect_url'] = route('app.dashboard');
        }

        if ($assessment->status === 'failed') {
            $response['message'] = 'Nao foi possivel concluir o processamento agora. Tente novamente em instantes.';
        }

        return response()->json($response);
    }
}
