<?php

namespace Tests\Unit\Models;

use App\Models\ConsentTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConsentTemplateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        ConsentTemplate::create(['name' => 'Cirurgia Geral', 'slug' => 'cirurgia-geral', 'content' => 'Conteúdo', 'is_active' => true]);
        $this->assertDatabaseHas('consent_templates', ['name' => 'Cirurgia Geral', 'is_active' => true]);
    }

    public function test_is_active_cast()
    {
        $t = ConsentTemplate::create(['name' => 'Teste', 'content' => 'Conteúdo', 'is_active' => true]);
        $this->assertIsBool($t->is_active);
    }

    public function test_slug_auto_generated()
    {
        $t = ConsentTemplate::create(['name' => 'Meu Template', 'content' => 'Conteúdo']);
        $this->assertEquals('meu-template', $t->slug);
    }
}
