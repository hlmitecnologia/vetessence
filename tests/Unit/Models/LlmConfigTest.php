<?php

namespace Tests\Unit\Models;

use App\Models\LlmConfig;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LlmConfigTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $config = LlmConfig::factory()->create([
            'provider' => 'anthropic',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('llm_configs', [
            'id' => $config->id,
            'provider' => 'anthropic',
        ]);
    }

    public function test_is_active_cast()
    {
        $config = LlmConfig::factory()->create(['is_active' => true]);
        $this->assertTrue($config->is_active);
    }

    public function test_temperature_cast()
    {
        $config = LlmConfig::factory()->create(['temperature' => 0.5]);
        $this->assertEquals(0.5, $config->temperature);
    }

    public function test_max_tokens_cast()
    {
        $config = LlmConfig::factory()->create(['max_tokens' => 1000]);
        $this->assertIsInt($config->max_tokens);
        $this->assertEquals(1000, $config->max_tokens);
    }

    public function test_active_scope()
    {
        LlmConfig::factory()->create(['is_active' => false]);
        $active = LlmConfig::factory()->create(['is_active' => true]);

        $this->assertTrue($active->fresh()->is_active);
        $this->assertEquals(1, LlmConfig::where('id', $active->id)->where('is_active', true)->count());
    }

    public function test_no_relationships()
    {
        $methods = get_class_methods(LlmConfig::class);
        $relationshipMethods = ['branch', 'pet', 'user', 'medicalRecord'];
        foreach ($relationshipMethods as $method) {
            $this->assertNotContains($method, $methods, "LlmConfig should not have a '$method' relationship");
        }
    }

    public function test_no_custom_scopes()
    {
        $methods = get_class_methods(LlmConfig::class);
        $scopeMethods = array_filter($methods, fn ($m) => str_starts_with($m, 'scope'));
        $this->assertEmpty($scopeMethods, 'LlmConfig should not define any custom scopes');
    }

    public function test_provider_fields_in_fillable()
    {
        $fillable = (new LlmConfig)->getFillable();
        $expected = ['provider', 'is_active', 'temperature', 'max_tokens',
            'openai_api_key', 'openai_model', 'anthropic_api_key', 'anthropic_model',
            'gemini_api_key', 'gemini_model', 'grok_api_key', 'grok_model',
            'ollama_base_url', 'ollama_model',
        ];
        foreach ($expected as $field) {
            $this->assertContains($field, $fillable, "Field '$field' should be in LlmConfig fillable");
        }
    }
}
