<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->integer('duration_semesters');
            $table->json('shifts')->comment('Turnos disponíveis, ex: ["morning", "evening"]');
            $table->integer('vacancies_per_year')->nullable();
            $table->string('coordinator_name')->nullable();
            $table->text('curriculum')->nullable()->comment('Grade curricular em texto ou JSON');
            $table->text('admission_requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
