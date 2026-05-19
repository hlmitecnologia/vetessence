<?php

namespace Database\Seeders;

use App\Models\Tutor;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Convenio;
use App\Models\Appointment;
use App\Models\Vaccination;
use App\Models\Exam;
use App\Models\Surgery;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Categories
        $cat1 = Category::firstOrCreate(['name' => 'Consultas', 'type' => 'service']);
        $cat2 = Category::firstOrCreate(['name' => 'Vacinas', 'type' => 'vaccine']);
        $cat3 = Category::firstOrCreate(['name' => 'Exames', 'type' => 'service']);
        $cat4 = Category::firstOrCreate(['name' => 'Medicamentos', 'type' => 'product']);
        $cat5 = Category::firstOrCreate(['name' => 'Rações', 'type' => 'product']);
        $cat6 = Category::firstOrCreate(['name' => 'Acessórios', 'type' => 'product']);

        // Services
        Service::firstOrCreate(['name' => 'Consulta Geral'], ['description' => 'Consulta veterinária geral', 'price' => 150.00, 'duration' => 30, 'category_id' => $cat1->id]);
        Service::firstOrCreate(['name' => 'Consulta Emergencial'], ['description' => 'Atendimento de emergência', 'price' => 250.00, 'duration' => 45, 'category_id' => $cat1->id]);
        Service::firstOrCreate(['name' => 'Retorno'], ['description' => 'Retorno de consulta', 'price' => 80.00, 'duration' => 20, 'category_id' => $cat1->id]);
        Service::firstOrCreate(['name' => 'Vacina V10'], ['description' => 'Vacina múltipla para cães', 'price' => 120.00, 'duration' => 15, 'category_id' => $cat2->id]);
        Service::firstOrCreate(['name' => 'Vacina Antirrábica'], ['description' => 'Vacina antirrábica', 'price' => 80.00, 'duration' => 15, 'category_id' => $cat2->id]);
        Service::firstOrCreate(['name' => 'Exame de Sangue'], ['description' => 'Hemograma completo', 'price' => 180.00, 'duration' => 10, 'category_id' => $cat3->id]);
        Service::firstOrCreate(['name' => 'Ultrassonografia'], ['description' => 'Exame de ultrassom', 'price' => 250.00, 'duration' => 30, 'category_id' => $cat3->id]);

        // Suppliers
        $sup1 = Supplier::firstOrCreate(['cnpj' => '12.345.678/0001-90'], ['name' => 'Distribuidora Pet Brasil', 'phone' => '(11) 3333-4444', 'email' => 'contato@distribuidorapet.com.br']);
        $sup2 = Supplier::firstOrCreate(['cnpj' => '98.765.432/0001-10'], ['name' => 'Veterinários Online', 'phone' => '(21) 2222-3333', 'email' => 'vendas@vetonline.com.br']);

        // Products
        Product::firstOrCreate(['sku' => 'FRONT001'], ['name' => 'Frontline Plus', 'description' => 'Antiparasitário tópico', 'cost_price' => 45.00, 'sale_price' => 89.90, 'stock' => 50, 'min_stock' => 10, 'category_id' => $cat4->id, 'supplier_id' => $sup1->id]);
        Product::firstOrCreate(['sku' => 'RACAO001'], ['name' => 'Ração Premium Cães', 'description' => 'Ração para cães adultos', 'cost_price' => 80.00, 'sale_price' => 145.00, 'stock' => 30, 'min_stock' => 5, 'category_id' => $cat5->id, 'supplier_id' => $sup2->id]);
        Product::firstOrCreate(['sku' => 'ACESS001'], ['name' => 'Coleira Guia', 'description' => 'Coleira guia para cães', 'cost_price' => 15.00, 'sale_price' => 35.00, 'stock' => 25, 'min_stock' => 5, 'category_id' => $cat6->id]);
        Product::firstOrCreate(['sku' => 'DRONT001'], ['name' => 'Vermífugo Drontal', 'description' => 'Vermífugo para cães e gatos', 'cost_price' => 25.00, 'sale_price' => 55.00, 'stock' => 40, 'min_stock' => 10, 'category_id' => $cat4->id, 'supplier_id' => $sup1->id]);

        // Convênios
        Convenio::firstOrCreate(['name' => 'Plano Pet Saúde'], ['plan_name' => 'Básico', 'discount_percent' => 10]);
        Convenio::firstOrCreate(['name' => 'Seguro Animal'], ['plan_name' => 'Premium', 'discount_percent' => 20]);

        // Get users for sample data
        $vet = User::where('role_id', Role::where('slug', 'veterinario')->first()->id)->first();
        $tutorUser = User::where('role_id', Role::where('slug', 'tutor')->first()->id)->first();

        // Tutor
        $tutor = Tutor::firstOrCreate(
            ['user_id' => $tutorUser->id],
            [
                'cpf' => '123.456.789-00',
                'phone' => '(11) 99999-8888',
                'email' => 'maria.tutor@email.com',
                'address' => 'Rua das Flores, 123',
                'city' => 'São Paulo',
                'state' => 'SP',
            ]
        );

        // Pets
        $pet1 = Pet::firstOrCreate(
            ['name' => 'Rex', 'species' => 'canine'],
            [
                'breed' => 'SRD',
                'gender' => 'male',
                'birth_date' => '2020-05-15',
                'color' => 'Marrom',
                'weight' => 12.5,
            ]
        );

        $pet2 = Pet::firstOrCreate(
            ['name' => 'Luna', 'species' => 'feline'],
            [
                'breed' => 'SRD',
                'gender' => 'female',
                'birth_date' => '2021-03-20',
                'color' => 'Cinza',
                'weight' => 4.2,
            ]
        );

        // Link pets to tutor
        if (!\DB::table('pet_tutor')->where('pet_id', $pet1->id)->where('tutor_id', $tutor->id)->exists()) {
            \DB::table('pet_tutor')->insert([
                ['pet_id' => $pet1->id, 'tutor_id' => $tutor->id, 'is_primary' => true],
                ['pet_id' => $pet2->id, 'tutor_id' => $tutor->id, 'is_primary' => true],
            ]);
        }

        // Vaccinations
        Vaccination::firstOrCreate(
            ['pet_id' => $pet1->id, 'vaccine' => 'V10', 'batch' => 'LOT2024001'],
            [
                'vet_id' => $vet->id,
                'date' => now()->subMonths(3),
                'next_date' => now()->addMonths(9),
            ]
        );

        // Appointments
        Appointment::firstOrCreate(
            ['pet_id' => $pet1->id, 'date' => now()->addDays(2)->format('Y-m-d')],
            [
                'vet_id' => $vet->id,
                'time' => '10:00',
                'type' => 'consulta',
                'status' => 'scheduled',
                'reason' => 'Check-up anual',
            ]
        );

        Appointment::firstOrCreate(
            ['pet_id' => $pet2->id, 'date' => now()->subDays(5)->format('Y-m-d')],
            [
                'vet_id' => $vet->id,
                'time' => '14:30',
                'type' => 'consulta',
                'status' => 'completed',
                'reason' => 'Vacinação',
            ]
        );

        // Exams
        Exam::firstOrCreate(
            ['pet_id' => $pet1->id, 'type' => 'Hemograma'],
            [
                'vet_id' => $vet->id,
                'status' => 'ready',
                'requested_date' => now()->subDays(3),
                'result_date' => now()->subDays(1),
            ]
        );

        // Invoices
        Invoice::firstOrCreate(
            ['invoice_number' => 'FAT-0001'],
            [
                'tutor_id' => $tutor->id,
                'pet_id' => $pet1->id,
                'user_id' => $vet->id,
                'subtotal' => 270.00,
                'discount' => 0,
                'total' => 270.00,
                'status' => 'pending',
                'due_date' => now()->addDays(15),
            ]
        );
    }
}
