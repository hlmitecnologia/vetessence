<?php

namespace Tests\Feature\Integrations;

use App\Models\Branch;
use App\Models\NotificationLog;
use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use App\Models\VaccinationReminder;
use Tests\ModuleTestCase;

class VaccinationCycleTest extends ModuleTestCase
{
    protected Branch $branch;
    protected User $vet;

    protected function setUp(): void
    {
        parent::setUp();
        $user = $this->loginAs('veterinario');
        $this->branch = Branch::factory()->create();
        $this->vet = User::factory()->create(['branch_id' => $this->branch->id]);
    }

    public function test_vaccination_cycle_with_reminder_and_notification()
    {
        $pet = Pet::factory()->create();

        $vaccinationResponse = $this->post(route('vaccinations.store'), [
            'pet_id' => $pet->id,
            'vaccine' => 'V10',
            'date' => now()->format('Y-m-d'),
            'vet_id' => $this->vet->id,
            'batch' => 'BATCH001',
            'next_date' => now()->addYear()->format('Y-m-d'),
            'notes' => 'Vacina anual',
            'branch_id' => $this->branch->id,
        ]);
        $vaccinationResponse->assertSessionDoesntHaveErrors();
        $vaccinationResponse->assertRedirect();

        $this->assertDatabaseHas('vaccinations', [
            'pet_id' => $pet->id,
            'vaccine' => 'V10',
            'batch' => 'BATCH001',
        ]);
        $vaccination = Vaccination::where('pet_id', $pet->id)->first();

        $reminder = VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'scheduled_date' => now()->addDays(30)->format('Y-m-d'),
            'channel' => 'whatsapp',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('vaccination_reminders', [
            'id' => $reminder->id,
            'vaccination_id' => $vaccination->id,
            'status' => 'pending',
        ]);

        $notification = NotificationLog::create([
            'pet_id' => $pet->id,
            'type' => 'vaccination_reminder',
            'channel' => 'whatsapp',
            'destination' => '5511999999999',
            'sent_at' => now(),
            'status' => 'sent',
            'message' => 'Lembrete: Vacina V10 do ' . $pet->name . ' está próxima do vencimento.',
            'branch_id' => $this->branch->id,
        ]);

        $this->assertDatabaseHas('notification_logs', [
            'id' => $notification->id,
            'pet_id' => $pet->id,
            'type' => 'vaccination_reminder',
            'status' => 'sent',
        ]);

        $this->assertEquals('sent', $notification->fresh()->status);
    }
}
