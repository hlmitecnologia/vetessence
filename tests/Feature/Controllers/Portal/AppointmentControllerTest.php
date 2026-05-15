<?php

namespace Tests\Feature\Controllers\Portal;

use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AppointmentControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tutor = Tutor::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($this->tutor, 'tutor');
    }

    public function test_index()
    {
        $response = $this->get(route('portal.appointments.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('portal.appointments.create'));
        $response->assertOk();
    }
}
