<?php

use App\Jobs\ProcessAssessmentJob;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('processing renders for owner with processing status and includes ux props', function () {
    $user = User::factory()->create();

    Question::create([
        'category' => 'interests',
        'question_text' => 'Pergunta ativa',
        'question_type' => 'likert',
        'weight' => 1,
        'order' => 1,
        'is_active' => true,
    ]);

    Question::create([
        'category' => 'skills',
        'question_text' => 'Pergunta inativa',
        'question_type' => 'likert',
        'weight' => 1,
        'order' => 2,
        'is_active' => false,
    ]);

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'processing',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('assessment.processing', ['assessmentId' => $assessment->id]));

    $response->assertOk();
    $response->assertSee('&quot;component&quot;:&quot;Processing&quot;', false);
    $response->assertSee('&quot;id&quot;:'.$assessment->id, false);
    $response->assertSee('&quot;status&quot;:&quot;processing&quot;', false);
    $response->assertSee('&quot;total_questions&quot;:1', false);
    $response->assertSee('&quot;poll_interval_ms&quot;:2000', false);
    $response->assertSee('&quot;timeout_seconds&quot;:30', false);
});

test('processing redirects to questions when assessment is pending', function () {
    $user = User::factory()->create();

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'pending',
        'started_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('assessment.processing', ['assessmentId' => $assessment->id]));

    $response->assertRedirect(route('assessment.questions', ['assessmentId' => $assessment->id]));
});

test('status returns results redirect url when assessment is completed', function () {
    $user = User::factory()->create();

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'completed',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->getJson(route('assessment.status', ['assessmentId' => $assessment->id]));

    $response->assertOk();
    $response->assertJsonPath('status', 'completed');
    $response->assertJsonPath(
        'redirect_url',
        route('results.show', ['assessmentId' => $assessment->id]),
    );
});

test('status returns failed message when assessment has failed', function () {
    $user = User::factory()->create();

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'failed',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->getJson(route('assessment.status', ['assessmentId' => $assessment->id]));

    $response->assertOk();
    $response->assertJsonPath('status', 'failed');
    $response->assertJsonPath(
        'message',
        'Nao foi possivel concluir o processamento agora. Tente novamente em instantes.',
    );
});

test('status returns 404 for an assessment that belongs to another user', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $assessment = Assessment::create([
        'user_id' => $owner->id,
        'status' => 'processing',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($otherUser)
        ->getJson(route('assessment.status', ['assessmentId' => $assessment->id]));

    $response->assertNotFound();
});

test('submit returns conflict with redirect when assessment already failed and newer pending exists', function () {
    Queue::fake();

    $user = User::factory()->create();

    $question = Question::create([
        'category' => 'interests',
        'question_text' => 'Questao obrigatoria',
        'question_type' => 'likert',
        'weight' => 1,
        'order' => 1,
        'is_active' => true,
    ]);

    $failedAssessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'failed',
        'started_at' => now()->subMinutes(10),
        'completed_at' => now()->subMinutes(9),
    ]);

    $pendingAssessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'pending',
        'started_at' => now(),
    ]);

    $response = $this->actingAs($user)->postJson(
        route('assessment.submit', ['assessmentId' => $failedAssessment->id]),
        [
            'responses' => [
                [
                    'question_id' => $question->id,
                    'response_value' => 4,
                    'response_text' => null,
                ],
            ],
        ],
    );

    $response->assertStatus(409);
    $response->assertJsonPath('success', false);
    $response->assertJsonPath('current_status', 'failed');
    $response->assertJsonPath(
        'redirect_url',
        route('assessment.questions', ['assessmentId' => $pendingAssessment->id]),
    );

    Queue::assertNothingPushed();
});

test('submit returns conflict with processing redirect when assessment is already processing', function () {
    Queue::fake();

    $user = User::factory()->create();

    $question = Question::create([
        'category' => 'interests',
        'question_text' => 'Questao obrigatoria',
        'question_type' => 'likert',
        'weight' => 1,
        'order' => 1,
        'is_active' => true,
    ]);

    $processingAssessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'processing',
        'started_at' => now(),
    ]);

    $response = $this->actingAs($user)->postJson(
        route('assessment.submit', ['assessmentId' => $processingAssessment->id]),
        [
            'responses' => [
                [
                    'question_id' => $question->id,
                    'response_value' => 5,
                    'response_text' => null,
                ],
            ],
        ],
    );

    $response->assertStatus(409);
    $response->assertJsonPath('success', false);
    $response->assertJsonPath('current_status', 'processing');
    $response->assertJsonPath(
        'redirect_url',
        route('assessment.processing', ['assessmentId' => $processingAssessment->id]),
    );

    Queue::assertNothingPushed();
});

test('submit still returns redirect url for processing route and dispatches queued job with async connection', function () {
    Queue::fake();
    Log::spy();
    config()->set('queue.default', 'database');

    $user = User::factory()->create();

    $question = Question::create([
        'category' => 'interests',
        'question_text' => 'Questao obrigatoria',
        'question_type' => 'likert',
        'weight' => 1,
        'order' => 1,
        'is_active' => true,
    ]);

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'pending',
        'started_at' => now(),
    ]);

    $originalEnvironment = app()->environment();
    app()->detectEnvironment(fn () => 'testing');

    try {
        $response = $this->actingAs($user)->postJson(
            route('assessment.submit', ['assessmentId' => $assessment->id]),
            [
                'responses' => [
                    [
                        'question_id' => $question->id,
                        'response_value' => 4,
                        'response_text' => null,
                    ],
                ],
            ],
        );
    } finally {
        app()->detectEnvironment(fn () => $originalEnvironment);
    }

    $response->assertOk();
    $response->assertJsonPath('success', true);
    $response->assertJsonPath(
        'redirect_url',
        route('assessment.processing', ['assessmentId' => $assessment->id]),
    );

    $this->assertDatabaseHas('assessments', [
        'id' => $assessment->id,
        'status' => 'processing',
    ]);

    $this->assertDatabaseHas('assessment_responses', [
        'assessment_id' => $assessment->id,
        'question_id' => $question->id,
        'response_value' => 4,
    ]);

    Queue::assertPushed(ProcessAssessmentJob::class, function (ProcessAssessmentJob $job) use ($assessment) {
        return $job->assessmentId === $assessment->id;
    });

    Log::shouldHaveReceived('info')
        ->withArgs(function (string $message, array $context) use ($assessment, $user) {
            return $message === 'assessment.submit.dispatched'
                && ($context['assessment_id'] ?? null) === $assessment->id
                && ($context['user_id'] ?? null) === $user->id
                && ($context['dispatch_mode'] ?? null) === 'queue'
                && ($context['queue_connection'] ?? null) === 'database';
        })
        ->once();
});

test('submit dispatches processing after response in local environment with async queue connection', function () {
    Bus::fake();
    Log::spy();
    config()->set('queue.default', 'database');

    $user = User::factory()->create();

    $question = Question::create([
        'category' => 'interests',
        'question_text' => 'Questao obrigatoria',
        'question_type' => 'likert',
        'weight' => 1,
        'order' => 1,
        'is_active' => true,
    ]);

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'pending',
        'started_at' => now(),
    ]);

    $originalEnvironment = app()->environment();
    app()->detectEnvironment(fn () => 'local');
    $csrfToken = 'local-csrf-token';

    try {
        $response = $this->actingAs($user)
            ->withSession(['_token' => $csrfToken])
            ->postJson(
                route('assessment.submit', ['assessmentId' => $assessment->id]),
                [
                    'responses' => [
                        [
                            'question_id' => $question->id,
                            'response_value' => 5,
                            'response_text' => null,
                        ],
                    ],
                ],
                ['X-CSRF-TOKEN' => $csrfToken],
            );
    } finally {
        app()->detectEnvironment(fn () => $originalEnvironment);
    }

    $response->assertOk();
    $response->assertJsonPath('success', true);
    $response->assertJsonPath(
        'redirect_url',
        route('assessment.processing', ['assessmentId' => $assessment->id]),
    );

    Bus::assertDispatchedAfterResponse(ProcessAssessmentJob::class, function (ProcessAssessmentJob $job) use ($assessment) {
        return $job->assessmentId === $assessment->id;
    });

    Log::shouldHaveReceived('info')
        ->withArgs(function (string $message, array $context) use ($assessment, $user) {
            return $message === 'assessment.submit.dispatched'
                && ($context['assessment_id'] ?? null) === $assessment->id
                && ($context['user_id'] ?? null) === $user->id
                && ($context['dispatch_mode'] ?? null) === 'after_response'
                && ($context['queue_connection'] ?? null) === 'database';
        })
        ->once();
});

test('submit dispatches processing after response when queue connection is sync', function () {
    Bus::fake();
    Log::spy();
    config()->set('queue.default', 'sync');

    $user = User::factory()->create();

    $question = Question::create([
        'category' => 'interests',
        'question_text' => 'Questao obrigatoria',
        'question_type' => 'likert',
        'weight' => 1,
        'order' => 1,
        'is_active' => true,
    ]);

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'pending',
        'started_at' => now(),
    ]);

    $response = $this->actingAs($user)->postJson(
        route('assessment.submit', ['assessmentId' => $assessment->id]),
        [
            'responses' => [
                [
                    'question_id' => $question->id,
                    'response_value' => 5,
                    'response_text' => null,
                ],
            ],
        ],
    );

    $response->assertOk();
    $response->assertJsonPath('success', true);
    $response->assertJsonPath(
        'redirect_url',
        route('assessment.processing', ['assessmentId' => $assessment->id]),
    );

    Bus::assertDispatchedAfterResponse(ProcessAssessmentJob::class, function (ProcessAssessmentJob $job) use ($assessment) {
        return $job->assessmentId === $assessment->id;
    });

    Log::shouldHaveReceived('info')
        ->withArgs(function (string $message, array $context) use ($assessment, $user) {
            return $message === 'assessment.submit.dispatched'
                && ($context['assessment_id'] ?? null) === $assessment->id
                && ($context['user_id'] ?? null) === $user->id
                && ($context['dispatch_mode'] ?? null) === 'after_response'
                && ($context['queue_connection'] ?? null) === 'sync';
        })
        ->once();
});
