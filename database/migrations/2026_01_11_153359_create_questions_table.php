<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['interests', 'skills', 'values', 'personality']);
            $table->string('dimension')->nullable()->comment('Subcategoria dentro da categoria');
            $table->text('question_text');
            $table->enum('question_type', ['likert', 'multiple', 'open']);
            $table->json('options')->nullable()->comment('Opções para questões de múltipla escolha em JSON');
            $table->decimal('weight', 5, 2)->default(1.00)->comment('Peso da questão no cálculo');
            $table->integer('order')->default(0)->comment('Ordem de exibição');
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'is_active']);
            $table->index(['order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
