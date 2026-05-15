<?php

namespace Tests\Unit\Models;

use App\Models\CommunicationQueue;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\CommunicationTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommunicationQueueTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $template = CommunicationTemplate::create(['name' => 'Lembrete', 'type' => 'appointment', 'channel' => 'email', 'content' => 'teste']);
        CommunicationQueue::create([
            'tutor_id' => $tutor->id, 'pet_id' => $pet->id, 'template_id' => $template->id,
            'channel' => 'email', 'destination' => 'test@test.com', 'message_content' => 'Oi', 'status' => 'pending',
        ]);
        $this->assertDatabaseHas('communication_queue', ['tutor_id' => $tutor->id, 'status' => 'pending']);
    }

    public function test_tutor_relationship()
    {
        $tutor = Tutor::factory()->create();
        $q = CommunicationQueue::create(['tutor_id' => $tutor->id, 'channel' => 'email', 'status' => 'pending']);
        $this->assertInstanceOf(Tutor::class, $q->tutor);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $q = CommunicationQueue::create(['pet_id' => $pet->id, 'channel' => 'email', 'status' => 'pending']);
        $this->assertInstanceOf(Pet::class, $q->pet);
    }
}
