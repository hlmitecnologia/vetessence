<?php

namespace Tests\Feature\Controllers;

use App\Models\LlmConfig;
use Tests\ModuleTestCase;

class LlmConfigControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('super-admin');
    }

    public function test_edit_returns_ok()
    {
        $response = $this->get(route('llm.config'));
        $response->assertOk();
    }

    public function test_update_creates_config()
    {
        $response = $this->put(route('llm.config.update'), [
            'provider' => 'openai',
            'is_active' => true,
            'temperature' => 0.3,
            'max_tokens' => 500,
            'openai_api_key' => 'sk-test-key',
            'openai_model' => 'gpt-4o-mini',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('llm_configs', [
            'provider' => 'openai',
            'is_active' => true,
            'temperature' => 0.3,
            'max_tokens' => 500,
        ]);
    }

    public function test_update_validates_provider_required()
    {
        $response = $this->put(route('llm.config.update'), []);
        $response->assertSessionHasErrors(['provider']);
    }

    public function test_update_validates_anthropic_requires_api_key()
    {
        $response = $this->put(route('llm.config.update'), [
            'provider' => 'anthropic',
            'is_active' => true,
            'temperature' => 0.3,
            'max_tokens' => 500,
        ]);

        $response->assertSessionHasErrors(['anthropic_api_key']);
    }

    public function test_update_validates_gemini_requires_api_key()
    {
        $response = $this->put(route('llm.config.update'), [
            'provider' => 'gemini',
            'is_active' => true,
            'temperature' => 0.3,
            'max_tokens' => 500,
        ]);

        $response->assertSessionHasErrors(['gemini_api_key']);
    }

    public function test_update_validates_ollama_requires_base_url()
    {
        $response = $this->put(route('llm.config.update'), [
            'provider' => 'ollama',
            'is_active' => true,
            'temperature' => 0.3,
            'max_tokens' => 500,
        ]);

        $response->assertSessionHasErrors(['ollama_base_url']);
    }

    public function test_edit_displays_existing_config()
    {
        $config = LlmConfig::factory()->create([
            'provider' => 'gemini',
            'temperature' => 0.5,
            'max_tokens' => 1000,
            'gemini_api_key' => 'ai-test-key',
            'gemini_model' => 'gemini-2.0-flash',
        ]);

        $response = $this->get(route('llm.config'));
        $response->assertOk();
        $response->assertSee('gemini-2.0-flash');
    }

}
