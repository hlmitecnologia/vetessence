<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\NfseInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class NfseInvoiceFactory extends Factory
{
    protected $model = NfseInvoice::class;

    public function definition()
    {
        return [
            'branch_id' => Branch::factory(),
            'invoice_id' => Invoice::factory(),
            'nfse_number' => $this->faker->numerify('######'),
            'nfse_code' => $this->faker->numerify('##########'),
            'nfse_url_xml' => $this->faker->url(),
            'nfse_url_pdf' => $this->faker->url(),
            'rps_number' => $this->faker->numerify('######'),
            'status' => 'issued',
            'issuance_date' => now(),
            'verification_code' => $this->faker->bothify('????-????-????-????'),
            'provider_response' => null,
            'error_message' => null,
        ];
    }

    public function pending()
    {
        return $this->state(fn() => [
            'status' => 'pending',
            'nfse_number' => null,
            'nfse_code' => null,
            'issuance_date' => null,
            'verification_code' => null,
        ]);
    }

    public function cancelled()
    {
        return $this->state(fn() => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}
