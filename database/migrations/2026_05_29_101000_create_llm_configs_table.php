<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('llm_configs', function (Blueprint $table) {
            $table->id();

            $table->string('provider', 50)->default('openai');
            $table->boolean('is_active')->default(false);

            // Common settings
            $table->decimal('temperature', 4, 2)->default(0.3);
            $table->integer('max_tokens')->default(500);

            // OpenAI
            $table->text('openai_api_key')->nullable();
            $table->string('openai_model', 100)->nullable()->default('gpt-4o-mini');

            // Anthropic
            $table->text('anthropic_api_key')->nullable();
            $table->string('anthropic_model', 100)->nullable()->default('claude-3-haiku-20240307');

            // Google Gemini
            $table->text('gemini_api_key')->nullable();
            $table->string('gemini_model', 100)->nullable()->default('gemini-2.0-flash');

            // Grok (xAI)
            $table->text('grok_api_key')->nullable();
            $table->string('grok_model', 100)->nullable()->default('grok-1');

            // Ollama (local)
            $table->string('ollama_base_url', 255)->nullable()->default('http://localhost:11434');
            $table->string('ollama_model', 100)->nullable()->default('llama3');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('llm_configs');
    }
};
