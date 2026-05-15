<?php

namespace Tests\Unit\Models;

use App\Models\ConsentLog;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConsentLogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $tutor = Tutor::factory()->create();
        $log = ConsentLog::create([
            'consentable_type' => Tutor::class,
            'consentable_id' => $tutor->id,
            'type' => 'lgpd_data_processing',
            'purpose' => 'Test purpose',
            'granted' => true,
            'ip_address' => '127.0.0.1',
            'consented_at' => now(),
        ]);

        $this->assertDatabaseHas('consent_logs', [
            'consentable_id' => $tutor->id,
            'type' => 'lgpd_data_processing',
        ]);
    }

    public function test_morph_relationship()
    {
        $tutor = Tutor::factory()->create();
        $log = $tutor->logConsent('lgpd_marketing', 'Marketing emails', true);

        $this->assertInstanceOf(ConsentLog::class, $log);
        $this->assertTrue($tutor->hasActiveConsent('lgpd_marketing'));
    }

    public function test_revoke_consent()
    {
        $tutor = Tutor::factory()->create();
        $tutor->logConsent('lgpd_data_processing', 'Data processing');

        $this->assertTrue($tutor->hasActiveConsent('lgpd_data_processing'));

        $tutor->revokeConsent('lgpd_data_processing');
        $this->assertFalse($tutor->hasActiveConsent('lgpd_data_processing'));
    }
}
