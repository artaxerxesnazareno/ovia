<?php

use App\Models\Assessment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

test('status returns dashboard redirect url when assessment is completed', function () {
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
    $response->assertJsonPath('redirect_url', route('app.dashboard'));
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

test('submit still returns redirect url for processing route', function () {
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
                    'response_value' => 4,
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

    $this->assertDatabaseHas('assessments', [
        'id' => $assessment->id,
        'status' => 'processing',
    ]);

    $this->assertDatabaseHas('assessment_responses', [
        'assessment_id' => $assessment->id,
        'question_id' => $question->id,
        'response_value' => 4,
    ]);
});
