<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('rank')->comment('Ranking: 1, 2 ou 3');
            $table->decimal('compatibility_score', 5, 2)->comment('Score de 0 a 100');
            $table->json('llm_analysis')->nullable()->comment('Resposta completa da LLM em JSON');
            $table->text('justification');
            $table->json('strengths')->nullable()->comment('Pontos fortes do estudante para este curso');
            $table->json('challenges')->nullable()->comment('Desafios potenciais');
            $table->timestamps();

            $table->unique(['assessment_id', 'course_id']);
            $table->index(['assessment_id', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
