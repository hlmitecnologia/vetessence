<?php

namespace Tests\Feature\Controllers;

use App\Models\ConsentForm;
use App\Models\ConsentTemplate;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class ConsentFormControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('consent-forms.index'));
        $response->assertOk();
    }

    public function test_store_creates_consent_form()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $template = ConsentTemplate::create([
            'name' => 'Cirurgia Geral',
            'content' => 'Termo de consentimento para cirurgia',
            'category' => 'surgery',
        ]);

        $response = $this->post(route('consent-forms.store'), [
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'consent_template_id' => $template->id,
            'client_name' => $tutor->name,
            'client_document' => '12345678901',
            'veterinarian_id' => $vet->id,
            'status' => 'pending',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('consent_forms', [
            'pet_id' => $pet->id,
            'client_name' => $tutor->name,
        ]);
    }

    public function test_show()
    {
        $form = ConsentForm::factory()->create();

        $response = $this->get(route('consent-forms.show', $form));
        $response->assertOk();
    }

    public function test_update()
    {
        $form = ConsentForm::factory()->create();

        $response = $this->put(route('consent-forms.update', $form), [
            'pet_id' => $form->pet_id,
            'tutor_id' => $form->tutor_id,
            'client_name' => $form->client_name,
            'status' => 'signed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('consent_forms', [
            'id' => $form->id,
            'status' => 'signed',
        ]);
    }

    public function test_destroy()
    {
        $form = ConsentForm::factory()->create();

        $response = $this->delete(route('consent-forms.destroy', $form));
        $response->assertRedirect();
        $this->assertDatabaseMissing('consent_forms', ['id' => $form->id]);
    }
}
