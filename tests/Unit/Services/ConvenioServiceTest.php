<?php

namespace Tests\Unit\Services;

use App\Models\Convenio;
use App\Models\ConvenioCoverageRule;
use App\Models\ConvenioSubscription;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Pet;
use App\Models\Tutor;
use App\Services\ConvenioService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConvenioServiceTest extends TestCase
{
    use DatabaseTransactions;

    private ConvenioService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ConvenioService::class);
    }

    private function makePet(Tutor $tutor): Pet
    {
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        return $pet;
    }

    public function test_find_active_subscription_returns_null_when_none()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->makePet($tutor);

        $result = $this->service->findActiveSubscription($tutor, $pet);

        $this->assertNull($result);
    }

    public function test_find_active_subscription_returns_subscription()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->makePet($tutor);
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $sub = ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 10,
            'is_active' => true,
        ]);
        $sub->coveredPets()->create(['pet_id' => $pet->id]);

        $result = $this->service->findActiveSubscription($tutor, $pet);

        $this->assertNotNull($result);
        $this->assertEquals($sub->id, $result->id);
    }

    public function test_find_active_subscription_ignores_inactive()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->makePet($tutor);
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $sub = ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 10,
            'is_active' => false,
        ]);
        $sub->coveredPets()->create(['pet_id' => $pet->id]);

        $result = $this->service->findActiveSubscription($tutor, $pet);

        $this->assertNull($result);
    }

    public function test_apply_discount_adds_invoice_item_with_negative_value()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->makePet($tutor);
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $sub = ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 10,
            'is_active' => true,
        ]);
        $sub->coveredPets()->create(['pet_id' => $pet->id]);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'status' => 'pending',
            'total' => 200,
            'subtotal' => 200,
            'due_date' => now(),
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Consulta',
            'quantity' => 1,
            'unit_price' => 200,
            'total' => 200,
            'item_type' => 'service',
        ]);

        $this->service->applyDiscount($invoice, $sub);

        $invoice->refresh();

        $this->assertEquals($sub->id, $invoice->convenio_subscription_id);
        $this->assertEquals(20, $invoice->convenio_discount);
        $this->assertEquals(20, $invoice->discount);
        $this->assertEquals(180, $invoice->total);

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'total' => -20,
        ]);
    }

    public function test_apply_discount_uses_coverage_rule_when_available()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->makePet($tutor);
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);

        ConvenioCoverageRule::create([
            'convenio_id' => $convenio->id,
            'item_type' => 'service',
            'coverage_percent' => 50,
        ]);

        $sub = ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 100,
            'is_active' => true,
        ]);
        $sub->coveredPets()->create(['pet_id' => $pet->id]);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'status' => 'pending',
            'total' => 200,
            'subtotal' => 200,
            'due_date' => now(),
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Consulta',
            'quantity' => 1,
            'unit_price' => 200,
            'total' => 200,
            'item_type' => 'service',
        ]);

        $this->service->applyDiscount($invoice, $sub);

        $invoice->refresh();

        $this->assertEquals(100, $invoice->convenio_discount);
        $this->assertEquals(100, $invoice->total);
    }

    public function test_apply_discount_skips_products()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->makePet($tutor);
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $sub = ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 10,
            'is_active' => true,
        ]);
        $sub->coveredPets()->create(['pet_id' => $pet->id]);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'status' => 'pending',
            'total' => 100,
            'subtotal' => 100,
            'due_date' => now(),
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Ração',
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100,
            'item_type' => 'product',
        ]);

        $this->service->applyDiscount($invoice, $sub);

        $invoice->refresh();

        $this->assertEquals(0, $invoice->convenio_discount);
        $this->assertEquals(100, $invoice->total);
    }

    public function test_apply_discount_idempotent()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->makePet($tutor);
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $sub = ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 10,
            'is_active' => true,
        ]);
        $sub->coveredPets()->create(['pet_id' => $pet->id]);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'status' => 'pending',
            'total' => 200,
            'subtotal' => 200,
            'due_date' => now(),
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Consulta',
            'quantity' => 1,
            'unit_price' => 200,
            'total' => 200,
            'item_type' => 'service',
        ]);

        // Chama duas vezes
        $this->service->applyDiscount($invoice, $sub);
        $this->service->applyDiscount($invoice, $sub);

        $invoice->refresh();

        // Apenas um item de desconto
        $discountItems = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('total', '<', 0)
            ->count();
        $this->assertEquals(1, $discountItems, 'Deve ter apenas um item de desconto (idempotente)');
    }
}
