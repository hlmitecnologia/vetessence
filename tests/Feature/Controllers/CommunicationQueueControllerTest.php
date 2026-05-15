<?php

namespace Tests\Feature\Controllers;

use App\Models\CommunicationQueue;
use App\Models\CommunicationTemplate;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class CommunicationQueueControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    private function createTemplate(): CommunicationTemplate
    {
        return CommunicationTemplate::create([
            'name' => 'Template Teste',
            'type' => 'appointment_reminder',
            'channel' => 'email',
            'subject' => 'Assunto',
            'content' => 'Conteúdo do template',
            'is_active' => true,
        ]);
    }

    private function createQueue(): CommunicationQueue
    {
        $template = $this->createTemplate();
        $tutor = Tutor::factory()->create();

        return CommunicationQueue::create([
            'tutor_id' => $tutor->id,
            'template_id' => $template->id,
            'channel' => 'email',
            'destination' => 'teste@example.com',
            'message_content' => 'Mensagem de teste',
            'scheduled_at' => now(),
            'status' => 'pending',
        ]);
    }

    public function test_index()
    {
        $response = $this->get(route('communication-queues.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('communication-queues.create'));
        $response->assertOk();
    }

    public function test_store()
    {
        $template = $this->createTemplate();
        $tutor = Tutor::factory()->create();

        $response = $this->post(route('communication-queues.store'), [
            'tutor_id' => $tutor->id,
            'template_id' => $template->id,
            'channel' => 'email',
            'destination' => $tutor->email ?? 'teste@example.com',
            'message_content' => 'Mensagem de teste',
            'scheduled_at' => now()->addHour()->format('Y-m-d H:i:s'),
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('communication-queues.index'));
        $this->assertDatabaseHas('communication_queue', [
            'tutor_id' => $tutor->id,
            'status' => 'pending',
        ]);
    }

    public function test_show()
    {
        $queue = $this->createQueue();

        $response = $this->get(route('communication-queues.show', $queue));
        $response->assertOk();
    }

    public function test_destroy()
    {
        $queue = $this->createQueue();

        $response = $this->delete(route('communication-queues.destroy', $queue));
        $response->assertRedirect(route('communication-queues.index'));
        $this->assertDatabaseMissing('communication_queue', ['id' => $queue->id]);
    }
}
