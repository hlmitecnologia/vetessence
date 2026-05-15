<?php

namespace Tests\Unit\Traits;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DigitalSignableTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sign_creates_hash_and_signature()
    {
        $user = User::factory()->create(['crmv' => 'SP-12345']);
        $this->actingAs($user);

        $record = MedicalRecord::factory()->create(['user_id' => $user->id]);

        $record->sign();

        $this->assertNotNull($record->content_hash);
        $this->assertNotNull($record->digital_signature);
        $this->assertNotNull($record->signed_at);
        $this->assertTrue($record->isSigned());
    }

    public function test_verify_integrity()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $record = MedicalRecord::factory()->create(['user_id' => $user->id]);
        $record->sign();

        $this->assertTrue($record->verifyIntegrity());

        $record->update(['diagnosis' => 'Tampered diagnosis']);
        $this->assertFalse($record->verifyIntegrity());
    }

    public function test_is_signed_returns_false_when_not_signed()
    {
        $record = MedicalRecord::factory()->create();
        $this->assertFalse($record->isSigned());
    }
}
