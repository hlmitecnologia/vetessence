<?php

namespace Tests\Feature;

use App\Models\Tutor;
use Tests\ModuleTestCase;

class TutorNotificationPreferencesTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_tutor_factory_uses_defaults()
    {
        $tutor = Tutor::factory()->create();
        $fresh = Tutor::find($tutor->id);
        $this->assertTrue((bool) $fresh->notify_whatsapp);
        $this->assertTrue((bool) $fresh->notify_sms);
        $this->assertTrue((bool) $fresh->notify_email);
    }

    public function test_can_update_via_controller()
    {
        $tutor = Tutor::factory()->create();
        $response = $this->put(route('tutors.update', $tutor), [
            'name' => $tutor->name,
            'cpf' => $tutor->cpf,
            'email' => $tutor->email,
            'phone' => $tutor->phone,
            'notify_whatsapp' => '0',
            'notify_sms' => '1',
            'notify_email' => '0',
        ]);
        $response->assertRedirect();
        $tutor->refresh();
        $this->assertFalse((bool) $tutor->notify_whatsapp);
        $this->assertTrue((bool) $tutor->notify_sms);
        $this->assertFalse((bool) $tutor->notify_email);
    }
}
