<?php

namespace Tests\Feature\Controllers\Portal;

use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
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

    public function test_dashboard_returns_200()
    {
        $response = $this->get(route('portal.dashboard'));
        $response->assertOk();
    }
}
