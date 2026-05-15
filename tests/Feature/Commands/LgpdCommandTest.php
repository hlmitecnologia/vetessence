<?php

namespace Tests\Feature\Commands;

use App\Models\Tutor;
use App\Models\ConsentLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LgpdCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_export_command()
    {
        $tutor = Tutor::factory()->create();
        $tutor->logConsent('lgpd_data_processing', 'Test purpose');

        $this->artisan('lgpd:export', ['tutor_id' => $tutor->id])
            ->assertExitCode(0);
    }

    public function test_export_nonexistent_tutor()
    {
        $this->artisan('lgpd:export', ['tutor_id' => 99999])
            ->assertExitCode(1);
    }

    public function test_anonymize_command()
    {
        $tutor = Tutor::factory()->create([
            'name' => 'João Silva',
            'cpf' => '123.456.789-00',
            'email' => 'joao@example.com',
        ]);
        $tutor->logConsent('lgpd_data_processing', 'Test');

        $this->artisan('lgpd:anonymize', ['tutor_id' => $tutor->id])
            ->assertExitCode(0);

        $tutor->refresh();
        $this->assertDatabaseHas('tutors', ['id' => $tutor->id, 'name' => '[ANONYMIZED]']);
        $this->assertNull($tutor->cpf);
        $this->assertFalse($tutor->hasActiveConsent('lgpd_data_processing'));
    }

    public function test_anonymize_nonexistent_tutor()
    {
        $this->artisan('lgpd:anonymize', ['tutor_id' => 99999])
            ->assertExitCode(1);
    }
}
