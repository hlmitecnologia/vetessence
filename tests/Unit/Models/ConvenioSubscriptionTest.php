<?php

namespace Tests\Unit\Models;

use App\Models\Convenio;
use App\Models\ConvenioSubscription;
use App\Models\Tutor;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConvenioSubscriptionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $tutor = Tutor::factory()->create();
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);

        ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 15.00,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('convenio_subscriptions', [
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 15.00,
        ]);
    }

    public function test_tutor_relationship()
    {
        $tutor = Tutor::factory()->create();
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $sub = ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 10,
        ]);

        $this->assertInstanceOf(Tutor::class, $sub->tutor);
    }

    public function test_convenio_relationship()
    {
        $tutor = Tutor::factory()->create();
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $sub = ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 10,
        ]);

        $this->assertInstanceOf(Convenio::class, $sub->convenio);
    }

    public function test_covered_pets_relationship()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $sub = ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 10,
        ]);
        $sub->coveredPets()->create(['pet_id' => $pet->id]);

        $this->assertCount(1, $sub->coveredPets);
        $this->assertEquals($pet->id, $sub->coveredPets->first()->pet_id);
    }
}
