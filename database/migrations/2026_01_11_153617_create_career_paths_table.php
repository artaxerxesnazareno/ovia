<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('average_salary_min', 12, 2)->nullable();
            $table->decimal('average_salary_max', 12, 2)->nullable();
            $table->enum('market_demand', ['low', 'medium', 'high', 'very_high'])->default('medium');
            $table->json('key_skills')->nullable()->comment('Habilidades principais em JSON');
            $table->json('growth_potential')->nullable()->comment('Potencial de crescimento em JSON');
            $table->timestamps();

            $table->index(['course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_paths');
    }
};
