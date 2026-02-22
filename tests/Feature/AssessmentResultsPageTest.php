<?php

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Recommendation;
use App\Models\Roadmap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('completed assessment owner can open results page', function () {
    $user = User::factory()->create();

    $course = Course::create([
        'name' => 'Licenciatura em Ciencias da Computacao',
        'slug' => 'ciencias-da-computacao',
        'description' => 'Curso de tecnologia.',
        'duration_semesters' => 8,
        'shifts' => ['morning', 'evening'],
        'is_active' => true,
    ]);

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'completed',
        'started_at' => now()->subMinutes(2),
        'completed_at' => now(),
        'processing_time_seconds' => 22,
    ]);

    $recommendation = Recommendation::create([
        'assessment_id' => $assessment->id,
        'course_id' => $course->id,
        'rank' => 1,
        'compatibility_score' => 90,
        'llm_analysis' => [
            'profile_analysis' => [
                'summary' => 'Perfil orientado para tecnologia.',
                'personality_type' => 'Analitico-Criativo',
                'strengths' => ['Logica'],
                'areas_to_develop' => ['Comunicacao'],
                'career_values' => ['Crescimento'],
            ],
            'course_recommendation' => [
                'course_name' => 'Licenciatura em Ciencias da Computacao',
                'career_paths' => [
                    [
                        'title' => 'Desenvolvedor Full-Stack',
                        'description' => 'Atua em front-end e back-end.',
                        'average_salary_range' => '150.000 - 400.000 Kz/mes',
                        'market_demand' => 'Alta',
                        'key_skills' => ['JavaScript'],
                        'growth_potential' => 'Alta',
                    ],
                ],
            ],
            'roadmap' => [
                'short_term' => [],
                'medium_term' => [],
                'long_term' => [],
                'certifications_to_consider' => ['AWS'],
                'books_recommended' => ['Clean Code'],
                'communities_to_join' => ['Stack Overflow'],
            ],
            'additional_advice' => [
                'immediate_next_steps' => 'Pratique todos os dias.',
                'long_term_vision' => 'Mantenha consistencia.',
                'words_of_encouragement' => 'Voce consegue.',
            ],
        ],
        'justification' => 'Boa aderencia.',
        'strengths' => ['Logica'],
        'challenges' => ['Comunicacao'],
    ]);

    Roadmap::create([
        'recommendation_id' => $recommendation->id,
        'short_term_goals' => [],
        'medium_term_goals' => [],
        'long_term_goals' => [],
        'resources' => [],
        'certifications' => ['AWS'],
        'books' => ['Clean Code'],
        'communities' => ['Stack Overflow'],
        'progress' => [],
    ]);

    $response = $this->actingAs($user)
        ->get(route('results.show', ['assessmentId' => $assessment->id]));

    $response->assertOk();
    $response->assertSee('&quot;component&quot;:&quot;Results&quot;', false);
    $response->assertSee('Licenciatura em Ciencias da Computacao');
});

test('results page redirects to processing while assessment is still processing', function () {
    $user = User::factory()->create();

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'processing',
        'started_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('results.show', ['assessmentId' => $assessment->id]));

    $response->assertRedirect(route('assessment.processing', ['assessmentId' => $assessment->id]));
});

test('completed assessment owner can open recommended course details page', function () {
    $user = User::factory()->create();

    $course = Course::create([
        'name' => 'Licenciatura em Ciencias da Computacao',
        'slug' => 'ciencias-da-computacao',
        'description' => 'Curso de tecnologia.',
        'duration_semesters' => 8,
        'shifts' => ['morning', 'evening'],
        'vacancies_per_year' => 30,
        'coordinator_name' => 'Coord Teste',
        'curriculum' => ['Algoritmos', 'Estruturas de Dados'],
        'admission_requirements' => 'Ensino medio concluido',
        'is_active' => true,
    ]);

    $assessment = Assessment::create([
        'user_id' => $user->id,
        'status' => 'completed',
        'started_at' => now()->subMinutes(2),
        'completed_at' => now(),
        'processing_time_seconds' => 18,
    ]);

    $recommendation = Recommendation::create([
        'assessment_id' => $assessment->id,
        'course_id' => $course->id,
        'rank' => 1,
        'compatibility_score' => 91,
        'llm_analysis' => [
            'profile_analysis' => [
                'summary' => 'Perfil orientado para tecnologia.',
                'personality_type' => 'Analitico-Criativo',
                'strengths' => ['Logica'],
                'areas_to_develop' => ['Comunicacao'],
                'career_values' => ['Crescimento'],
            ],
            'course_recommendation' => [
                'course_name' => 'Licenciatura em Ciencias da Computacao',
                'career_paths' => [
                    [
                        'title' => 'Desenvolvedor Full-Stack',
                        'description' => 'Atua em front-end e back-end.',
                        'average_salary_range' => '150.000 - 400.000 Kz/mes',
                        'market_demand' => 'Alta',
                        'key_skills' => ['JavaScript'],
                        'growth_potential' => 'Alta',
                    ],
                ],
            ],
            'roadmap' => [
                'short_term' => [],
                'medium_term' => [],
                'long_term' => [],
                'certifications_to_consider' => [],
                'books_recommended' => [],
                'communities_to_join' => [],
            ],
            'additional_advice' => [
                'immediate_next_steps' => 'Pratique todos os dias.',
                'long_term_vision' => 'Mantenha consistencia.',
                'words_of_encouragement' => 'Voce consegue.',
            ],
        ],
        'justification' => 'Boa aderencia.',
        'strengths' => ['Logica'],
        'challenges' => ['Comunicacao'],
    ]);

    Roadmap::create([
        'recommendation_id' => $recommendation->id,
        'short_term_goals' => [],
        'medium_term_goals' => [],
        'long_term_goals' => [],
        'resources' => [],
        'certifications' => [],
        'books' => [],
        'communities' => [],
        'progress' => [],
    ]);

    $response = $this->actingAs($user)
        ->get(route('results.course', ['assessmentId' => $assessment->id, 'rank' => 1]));

    $response->assertOk();
    $response->assertSee('&quot;component&quot;:&quot;ResultCourseDetails&quot;', false);
    $response->assertSee('Licenciatura em Ciencias da Computacao');
});
