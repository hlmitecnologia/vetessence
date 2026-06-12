<?php

namespace Tests\Unit\Commands;

use App\Models\Tutor;
use Tests\ModuleTestCase;

class AnonimizarDadosLGPDTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_anonymizes_tutor_data(): void
    {
        $tutor = Tutor::factory()->create([
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'email' => 'joao@example.com',
            'phone' => '11999999999',
            'address' => 'Rua Teste',
        ]);

        $this->artisan('lgpd:anonymize', ['tutor_id' => $tutor->id])
            ->expectsOutput("Dados do tutor {$tutor->id} anonimizados com sucesso.")
            ->assertExitCode(0);

        $tutor->refresh();

        $this->assertEquals('[ANONYMIZED]', $tutor->name);
        $this->assertTrue(empty($tutor->cpf));
        $this->assertTrue(empty($tutor->phone));
        $this->assertTrue(empty($tutor->address));
        $this->assertStringContainsString('anonimo', $tutor->email);
    }

    public function test_returns_error_when_tutor_not_found(): void
    {
        $this->artisan('lgpd:anonymize', ['tutor_id' => 9999])
            ->expectsOutput('Tutor não encontrado.')
            ->assertExitCode(1);
    }
}
