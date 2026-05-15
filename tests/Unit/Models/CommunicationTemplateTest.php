<?php

namespace Tests\Unit\Models;

use App\Models\CommunicationTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommunicationTemplateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        CommunicationTemplate::create(['name' => 'Lembrete', 'type' => 'appointment', 'channel' => 'email', 'subject' => 'Seu horário', 'content' => 'Conteúdo', 'is_active' => true]);
        $this->assertDatabaseHas('communication_templates', ['name' => 'Lembrete', 'is_active' => true]);
    }

    public function test_is_active_cast()
    {
        $t = CommunicationTemplate::create(['name' => 'Teste', 'type' => 'general', 'channel' => 'whatsapp', 'content' => 'teste', 'is_active' => true]);
        $this->assertIsBool($t->is_active);
    }
}
