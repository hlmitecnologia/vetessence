<?php

namespace Tests\Feature\Controllers\Portal;

use App\Models\Invoice;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
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
        $response = $this->get(route('portal.invoices.index'));
        $response->assertOk();
    }

    public function test_show()
    {
        $invoice = Invoice::factory()->create([
            'tutor_id' => $this->tutor->id,
            'status' => 'pending',
            'pix_code' => '00020101021226890014br.gov.bcb.pix2558api.example.com/v2/',
        ]);

        $response = $this->get(route('portal.invoices.show', $invoice->id));
        $response->assertOk();
    }
}
