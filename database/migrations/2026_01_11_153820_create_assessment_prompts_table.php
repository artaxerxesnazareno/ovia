<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique()->comment('Versão do prompt, ex: v1.0.0');
            $table->text('system_prompt')->comment('Prompt do sistema para a LLM');
            $table->text('user_prompt_template')->comment('Template do prompt do usuário');
            $table->json('parameters')->nullable()->comment('Parâmetros LLM (temperature, max_tokens, etc)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['version', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_prompts');
    }
};
