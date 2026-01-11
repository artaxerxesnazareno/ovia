<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_id')->constrained()->onDelete('cascade');
            $table->json('short_term_goals')->nullable()->comment('Metas de curto prazo (0-6 meses)');
            $table->json('medium_term_goals')->nullable()->comment('Metas de médio prazo (6-18 meses)');
            $table->json('long_term_goals')->nullable()->comment('Metas de longo prazo (18+ meses)');
            $table->json('resources')->nullable()->comment('Recursos recomendados em JSON');
            $table->json('certifications')->nullable()->comment('Certificações a considerar');
            $table->json('books')->nullable()->comment('Livros recomendados');
            $table->json('communities')->nullable()->comment('Comunidades para participar');
            $table->json('progress')->nullable()->comment('Progresso do usuário {goal_id: completed}');
            $table->timestamps();

            $table->index(['recommendation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmaps');
    }
};
