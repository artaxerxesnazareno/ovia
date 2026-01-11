<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('response_value')->nullable()->comment('Valor numérico para Likert (1-5) ou índice para múltipla escolha');
            $table->text('response_text')->nullable()->comment('Texto para respostas abertas');
            $table->timestamps();

            $table->unique(['assessment_id', 'question_id']);
            $table->index(['assessment_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_responses');
    }
};
