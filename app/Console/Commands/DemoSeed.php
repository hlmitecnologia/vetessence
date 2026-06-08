<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Branch;
use App\Models\Category;
use App\Models\CommissionLog;
use App\Models\CommissionRate;
use App\Models\CommunicationQueue;
use App\Models\Convenio;
use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use App\Models\Exam;
use App\Models\Hospitalization;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Role;
use App\Models\Service;
use App\Models\StaffSchedule;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanItem;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Vaccination;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeed extends Command
{
    protected $signature = 'demo:seed {--force : Skip confirmation prompt}';
    protected $description = 'Popula o banco com dados de demonstração abrangentes para o servidor de demo';

    private Branch $mainBranch;
    private Branch $sulBranch;
    private Branch $norteBranch;
    private User $vetCarlos;
    private User $vetBeatriz;
    private User $vetRafael;
    private User $recepMarina;
    private User $financialRicardo;

    public function handle(): int
    {
        if (Setting::get('demo_data_seeded') === 'true') {
            $this->warn('Dados de demonstração já foram semeados anteriormente.');
            if (!$this->option('force') && !$this->confirm('Deseja continuar mesmo assim?')) {
                $this->info('Comando abortado.');
                return Command::SUCCESS;
            }
        }

        if (!$this->option('force')) {
            $this->line('Este comando irá CRIAR dados de demonstração no banco de dados.');
            $this->line('Nenhum dado existente será apagado ou modificado.');
            if (!$this->confirm('Continuar?')) {
                $this->info('Comando abortado.');
                return Command::SUCCESS;
            }
        }

        if ($this->option('force')) {
            $this->cleanExistingData();
        }

        $this->line('Criando dados de demonstração...');
        $this->newLine();

        DB::beginTransaction();
        try {
            $this->createBranches();
            $this->createUsers();
            $this->createVetShifts();
            $this->createCategoriesAndServices();
            $this->createProducts();
            $this->createSuppliers();
            $this->createConvenios();
            $this->createTutors();
            $this->createPets();
            $this->createAppointments();
            $this->createMedicalRecords();
            $this->createPrescriptions();
            $this->createVaccinations();
            $this->createExams();
            $this->createInvoices();
            $this->createCommissions();
            $this->createStockMovements();
            $this->createPurchaseOrders();
            $this->createBankAccounts();
            $this->createBankTransactions();
            $this->createConvenioPetsAndClaims();
            $this->createTreatmentPlans();
            $this->createHospitalizations();
            $this->createCommunicationQueue();

            Setting::set('demo_data_seeded', 'true');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Erro ao criar dados de demonstração: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('Dados de demonstração criados com sucesso!');
        return Command::SUCCESS;
    }

    private function createBranches(): void
    {
        $this->mainBranch = Branch::where('is_main', true)->first()
            ?? Branch::firstOrCreate(
                ['slug' => 'matriz'],
                ['name' => 'Matriz', 'city' => 'São Paulo', 'state' => 'SP', 'phone' => '(11) 3000-0000', 'is_active' => true, 'is_main' => true]
            );

        $this->sulBranch = Branch::firstOrCreate(
            ['slug' => 'filial-zona-sul'],
            ['name' => 'Filial Zona Sul', 'city' => 'São Paulo', 'state' => 'SP', 'address' => 'Av. Santo Amaro, 1500', 'phone' => '(11) 3111-0001', 'is_active' => true, 'is_main' => false]
        );

        $this->norteBranch = Branch::firstOrCreate(
            ['slug' => 'filial-zona-norte'],
            ['name' => 'Filial Zona Norte', 'city' => 'São Paulo', 'state' => 'SP', 'address' => 'Av. Braz Leme, 2000', 'phone' => '(11) 3222-0002', 'is_active' => true, 'is_main' => false]
        );

        $this->line('  [ branches ]  Matriz, Zona Sul, Zona Norte');
    }

    private function createUsers(): void
    {
        $roles = [
            'vet' => Role::where('slug', 'veterinario')->first(),
            'recep' => Role::where('slug', 'recepcionista')->first(),
            'financial' => Role::where('slug', 'financeiro')->first(),
        ];

        $this->vetCarlos = $this->makeUser('Dr. Carlos Almeida', 'carlos.vet@demo.com', 'vet123', $roles['vet'], $this->mainBranch, 'CRMV-SP 12345');
        $this->vetBeatriz = $this->makeUser('Dra. Beatriz Oliveira', 'beatriz.vet@demo.com', 'vet123', $roles['vet'], $this->sulBranch, 'CRMV-SP 23456');
        $this->vetRafael = $this->makeUser('Dr. Rafael Santos', 'rafael.vet@demo.com', 'vet123', $roles['vet'], $this->norteBranch, 'CRMV-SP 34567');
        $this->recepMarina = $this->makeUser('Marina Costa', 'marina@demo.com', 'demo123', $roles['recep'], $this->mainBranch);
        $this->financialRicardo = $this->makeUser('Ricardo Pereira', 'ricardo@demo.com', 'demo123', $roles['financial'], $this->mainBranch);

        $this->line('  [ users ]      Dr. Carlos, Dra. Beatriz, Dr. Rafael, Marina, Ricardo');
    }

    private function makeUser(string $name, string $email, string $password, ?Role $role, Branch $branch, string $crmv = null): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role_id' => $role?->id,
                'branch_id' => $branch->id,
                'is_active' => true,
                'crmv' => $crmv,
            ]
        );
    }

    private function createVetShifts(): void
    {
        $today = today();
        $branches = [$this->mainBranch, $this->sulBranch, $this->norteBranch];
        $vets = [$this->vetCarlos, $this->vetBeatriz, $this->vetRafael];

        $shiftTypes = ['morning', 'afternoon', 'night'];

        foreach ($vets as $vet) {
            for ($day = 0; $day < 30; $day++) {
                if (rand(0, 2) === 0) {
                    continue;
                }

                $date = $today->copy()->addDays($day);
                $branch = $branches[array_rand($branches)];
                $shiftType = $shiftTypes[array_rand($shiftTypes)];

                $times = match ($shiftType) {
                    'morning' => ['start' => '08:00', 'end' => '12:00'],
                    'afternoon' => ['start' => '13:00', 'end' => '18:00'],
                    'night' => ['start' => '18:00', 'end' => '22:00'],
                };

                StaffSchedule::create([
                    'user_id' => $vet->id,
                    'work_date' => $date,
                    'start_time' => $times['start'],
                    'end_time' => $times['end'],
                    'shift_type' => $shiftType,
                    'is_vet_shift' => true,
                    'branch_id' => $branch->id,
                    'created_by' => $vet->id,
                ]);
            }
        }

        $this->line('  [ shifts ]     30 dias de plantões para 3 veterinários');
    }

    private function createCategoriesAndServices(): void
    {
        $catServicos = Category::firstOrCreate(['name' => 'Cirurgias', 'type' => 'service']);
        $catExames = Category::where('name', 'Exames')->where('type', 'service')->first();
        $catConsultas = Category::where('name', 'Consultas')->where('type', 'service')->first();

        $extraServices = [
            ['name' => 'Cirurgia Castração Canina', 'price' => 450.00, 'duration' => 90, 'cat' => $catServicos, 'code' => '6.01', 'iss' => 2.00],
            ['name' => 'Cirurgia Castração Felina', 'price' => 350.00, 'duration' => 60, 'cat' => $catServicos, 'code' => '6.01', 'iss' => 2.00],
            ['name' => 'Retirada de Tártaro', 'price' => 280.00, 'duration' => 45, 'cat' => $catServicos, 'code' => '6.02', 'iss' => 2.00],
            ['name' => 'Exame de Fezes', 'price' => 60.00, 'duration' => 10, 'cat' => $catExames, 'code' => '6.03', 'iss' => 3.00],
            ['name' => 'Teste Rápido FIV/FeLV', 'price' => 120.00, 'duration' => 15, 'cat' => $catExames, 'code' => '6.03', 'iss' => 3.00],
            ['name' => 'Aferição de Pressão Arterial', 'price' => 50.00, 'duration' => 15, 'cat' => $catConsultas, 'code' => '6.04', 'iss' => 2.00],
        ];

        foreach ($extraServices as $s) {
            Service::firstOrCreate(
                ['name' => $s['name']],
                [
                    'description' => $s['name'],
                    'price' => $s['price'],
                    'duration' => $s['duration'],
                    'category_id' => $s['cat']?->id,
                    'service_code' => $s['code'],
                    'iss_aliquot' => $s['iss'],
                ]
            );
        }

        $this->line('  [ services ]   Castrações, Tártaro, Exames complementares');
    }

    private function createProducts(): void
    {
        $catMed = Category::where('name', 'Medicamentos')->where('type', 'product')->first();
        $catRacao = Category::where('name', 'Rações')->where('type', 'product')->first();
        $catAcess = Category::where('name', 'Acessórios')->where('type', 'product')->first();

        $products = [
            ['sku' => 'AMOXI001', 'name' => 'Amoxicilina 50mg', 'cost' => 18.00, 'sale' => 45.00, 'stock' => 60, 'min' => 15, 'cat' => $catMed, 'ncm' => '30041000', 'cfop' => '5102', 'cst' => '00', 'csosn' => null, 'unit' => 'COMP'],
            ['sku' => 'MELOX001', 'name' => 'Meloxicam 0,5%', 'cost' => 22.00, 'sale' => 52.00, 'stock' => 45, 'min' => 10, 'cat' => $catMed, 'ncm' => '30049099', 'cfop' => '5102', 'cst' => '00', 'csosn' => null, 'unit' => 'FR'],
            ['sku' => 'NEXG001', 'name' => 'Antipulgas NexGard', 'cost' => 55.00, 'sale' => 119.90, 'stock' => 35, 'min' => 8, 'cat' => $catMed, 'ncm' => '30049099', 'cfop' => '5102', 'cst' => '00', 'csosn' => null, 'unit' => 'UN'],
            ['sku' => 'OMEP001', 'name' => 'Omeprazol 20mg', 'cost' => 12.00, 'sale' => 32.00, 'stock' => 50, 'min' => 10, 'cat' => $catMed, 'ncm' => '30049099', 'cfop' => '5102', 'cst' => '00', 'csosn' => null, 'unit' => 'COMP'],
            ['sku' => 'RACAOC001', 'name' => 'Ração Premium Cães Filhotes', 'cost' => 90.00, 'sale' => 165.00, 'stock' => 20, 'min' => 5, 'cat' => $catRacao, 'ncm' => '23099090', 'cfop' => '5102', 'cst' => '00', 'csosn' => null, 'unit' => 'KG'],
            ['sku' => 'RACAOG001', 'name' => 'Ração Terapêutica Renal', 'cost' => 120.00, 'sale' => 210.00, 'stock' => 15, 'min' => 3, 'cat' => $catRacao, 'ncm' => '23099090', 'cfop' => '5102', 'cst' => '00', 'csosn' => null, 'unit' => 'KG'],
            ['sku' => 'CAMA001', 'name' => 'Cama Ortopédica Média', 'cost' => 65.00, 'sale' => 149.90, 'stock' => 12, 'min' => 4, 'cat' => $catAcess, 'ncm' => '94049000', 'cfop' => '5102', 'cst' => '00', 'csosn' => null, 'unit' => 'UN'],
            ['sku' => 'SHAMP001', 'name' => 'Shampoo Medicinal Clorexidine', 'cost' => 28.00, 'sale' => 69.90, 'stock' => 25, 'min' => 6, 'cat' => $catMed, 'ncm' => '33051000', 'cfop' => '5102', 'cst' => '00', 'csosn' => null, 'unit' => 'FR'],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['sku' => $p['sku']],
                [
                    'name' => $p['name'],
                    'cost_price' => $p['cost'],
                    'sale_price' => $p['sale'],
                    'stock' => $p['stock'],
                    'min_stock' => $p['min'],
                    'category_id' => $p['cat']?->id,
                    'ncm' => $p['ncm'],
                    'cfop' => $p['cfop'],
                    'cst' => $p['cst'],
                    'csosn' => $p['csosn'],
                    'unit' => $p['unit'],
                ]
            );
        }

        $this->line('  [ products ]   8 novos produtos com NCM/CFOP/CST');
    }

    private function createSuppliers(): void
    {
        Supplier::firstOrCreate(
            ['cnpj' => '11.222.333/0001-55'],
            ['name' => 'Farmácia Veterinária Nacional', 'phone' => '(11) 4444-5555', 'email' => 'vendas@farmaciaveterinaria.com.br']
        );
        Supplier::firstOrCreate(
            ['cnpj' => '44.555.666/0001-88'],
            ['name' => 'Pet Shop Distribuidora Plus', 'phone' => '(11) 5555-6666', 'email' => 'contato@distribuidoraplus.com.br']
        );
        $this->line('  [ suppliers ]  2 novos fornecedores');
    }

    private function createConvenios(): void
    {
        Convenio::firstOrCreate(
            ['name' => 'PetCare Saúde'],
            ['plan_name' => 'Completo', 'discount_percent' => 15, 'is_active' => true]
        );
        Convenio::firstOrCreate(
            ['name' => 'Animal Total'],
            ['plan_name' => 'Master', 'discount_percent' => 25, 'is_active' => true]
        );
        $this->line('  [ convenios ]  PetCare, Animal Total');
    }

    private function createTutors(): void
    {
        $tutors = [
            ['name' => 'Ana Lúcia Mendes',     'cpf' => '111.222.333-44', 'phone' => '(11) 98765-0101', 'city' => 'São Paulo',      'state' => 'SP', 'notify_whatsapp' => true],
            ['name' => 'Roberto Ferreira',      'cpf' => '222.333.444-55', 'phone' => '(11) 98765-0202', 'city' => 'São Paulo',      'state' => 'SP'],
            ['name' => 'Carla Dias',            'cpf' => '333.444.555-66', 'phone' => '(21) 98765-0303', 'city' => 'Rio de Janeiro', 'state' => 'RJ'],
            ['name' => 'Fernando Lima',         'cpf' => '444.555.666-77', 'phone' => '(21) 98765-0404', 'city' => 'Rio de Janeiro', 'state' => 'RJ'],
            ['name' => 'Juliana Martins',       'cpf' => '555.666.777-88', 'phone' => '(31) 98765-0505', 'city' => 'Belo Horizonte', 'state' => 'MG'],
            ['name' => 'Marcos Andrade',        'cpf' => '666.777.888-99', 'phone' => '(31) 98765-0606', 'city' => 'Belo Horizonte', 'state' => 'MG'],
            ['name' => 'Patrícia Nunes',        'cpf' => '777.888.999-00', 'phone' => '(11) 98765-0707', 'city' => 'São Paulo',      'state' => 'SP', 'notify_email' => true, 'notify_sms' => true],
            ['name' => 'Gustavo Rocha',         'cpf' => '888.999.000-11', 'phone' => '(11) 98765-0808', 'city' => 'São Paulo',      'state' => 'SP'],
            ['name' => 'Simone Barbosa',        'cpf' => '999.000.111-22', 'phone' => '(11) 98765-0909', 'city' => 'São Paulo',      'state' => 'SP'],
            ['name' => 'Lucas Oliveira',        'cpf' => '000.111.222-33', 'phone' => '(11) 98765-1010', 'city' => 'São Paulo',      'state' => 'SP'],
            ['name' => 'Amanda Torres',         'cpf' => '123.456.789-01', 'phone' => '(11) 98765-1111', 'city' => 'São Paulo',      'state' => 'SP', 'notify_whatsapp' => true],
            ['name' => 'Eduardo Campos',        'cpf' => '234.567.890-12', 'phone' => '(11) 98765-1212', 'city' => 'São Paulo',      'state' => 'SP'],
        ];

        $password = Hash::make('tutor123');

        foreach ($tutors as $i => $data) {
            $email = 'demo.tutor' . ($i + 1) . '@vetessence.com.br';
            Tutor::updateOrCreate(
                ['cpf' => $data['cpf']],
                [
                    'name' => $data['name'],
                    'email' => $email,
                    'password' => $password,
                    'phone' => $data['phone'],
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'address' => 'Rua dos Testes, ' . random_int(100, 999),
                    'notify_sms' => $data['notify_sms'] ?? false,
                    'notify_whatsapp' => $data['notify_whatsapp'] ?? false,
                    'notify_email' => $data['notify_email'] ?? false,
                ]
            );
        }

        $this->line('  [ tutors ]     12 tutores');
    }

    private function createPets(): void
    {
        $allTutors = Tutor::whereNotNull('cpf')->get();
        $tutor = fn (int $i) => $allTutors[$i % $allTutors->count()];

        $pets = [
            // Cães
            ['Thor', 'canine', 'Golden Retriever', 'male', '2020-03-10', 32.0],
            ['Bolinha', 'canine', 'SRD', 'male', '2021-07-22', 15.0],
            ['Mel', 'canine', 'SRD Pequeno', 'female', '2022-01-15', 8.5],
            ['Toddy', 'canine', 'Shih-tzu', 'male', '2019-11-30', 7.2],
            ['Bela', 'canine', 'Buldogue Francês', 'female', '2023-06-01', 11.0],
            ['Buddy', 'canine', 'Pastor Alemão', 'male', '2020-09-18', 38.0],
            ['Lila', 'canine', 'Beagle', 'female', '2021-12-05', 14.5],
            ['Pipoca', 'canine', 'Yorkshire', 'female', '2023-09-20', 3.5],
            ['Duke', 'canine', 'Poodle', 'male', '2018-04-14', 9.0],
            ['Zara', 'canine', 'Pitbull', 'female', '2022-08-08', 24.0],
            ['Fred', 'canine', 'Border Collie', 'male', '2015-05-25', 20.0],
            ['Cacau', 'canine', 'Dachshund', 'female', '2024-01-10', 6.5],
            // Gatos
            ['Mimi', 'feline', 'SRD', 'female', '2021-04-03', 4.0],
            ['Simba', 'feline', 'Siamês', 'male', '2022-10-12', 5.5],
            ['Luna', 'feline', 'Persa', 'female', '2020-12-25', 4.8],
            ['Tigrão', 'feline', 'Maine Coon', 'male', '2023-03-07', 7.0],
            ['Frida', 'feline', 'Angorá', 'female', '2024-06-15', 3.2],
            ['Pretinha', 'feline', 'SRD', 'female', '2019-08-30', 3.8],
        ];

        foreach ($pets as $i => $pData) {
            [$name, $species, $breed, $gender, $birthDate, $weight] = $pData;
            $pet = Pet::firstOrCreate(
                ['name' => $name, 'species' => $species],
                [
                    'breed' => $breed,
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                    'weight' => $weight,
                    'color' => ['Caramelo', 'Preto', 'Branco', 'Cinza', 'Marrom', 'Amarelo'][random_int(0, 5)],
                    'is_active' => true,
                ]
            );

            $t = $tutor($i);
            if (!DB::table('pet_tutor')->where('pet_id', $pet->id)->where('tutor_id', $t->id)->exists()) {
                DB::table('pet_tutor')->insert([
                    'pet_id' => $pet->id,
                    'tutor_id' => $t->id,
                    'is_primary' => true,
                    'relationship' => 'Responsável',
                ]);
            }
        }

        $this->line('  [ pets ]       18 pets (12 cães, 6 gatos)');
    }

    private function getAllVets(): array
    {
        return [$this->vetCarlos, $this->vetBeatriz, $this->vetRafael];
    }

    private function getRandomVet(): User
    {
        $vets = $this->getAllVets();
        return $vets[array_rand($vets)];
    }

    private function getBranches(): array
    {
        return [$this->mainBranch, $this->sulBranch, $this->norteBranch];
    }

    private function createAppointments(): void
    {
        $pets = Pet::where('is_active', true)->get();
        if ($pets->isEmpty()) return;

        $statuses = ['completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'in_progress', 'in_progress', 'confirmed', 'scheduled', 'scheduled', 'scheduled', 'scheduled', 'scheduled', 'scheduled', 'scheduled', 'scheduled', 'cancelled', 'no_show'];
        $types = ['consulta', 'consulta', 'consulta', 'consulta', 'consulta', 'vacina', 'vacina', 'exame', 'exame', 'retorno', 'cirurgia', 'emergencia'];
        $reasons = [
            'Check-up anual', 'Vacinação', 'Exame de rotina', 'Febre e apatia',
            'Vômito e diarreia', 'Coceira intensa', 'Manchas na pele',
            'Dificuldade para urinar', 'Tosse persistente', 'Ferida na pata',
            'Queda de pelo', 'Consulta de retorno', 'Emergência — atropelamento',
            'Emergência — intoxicação', 'Cólica abdominal', 'Perda de apetite',
            'Exame pré-cirúrgico', 'Acompanhamento pós-operatório',
        ];

        $branches = $this->getBranches();

        foreach ($statuses as $i => $status) {
            $pet = $pets[$i % $pets->count()];
            $tutor = $pet->tutors->first();
            if (!$tutor) continue;

            $date = match ($status) {
                'completed' => now()->subDays(random_int(1, 45))->format('Y-m-d'),
                'cancelled', 'no_show' => now()->subDays(random_int(1, 15))->format('Y-m-d'),
                'in_progress' => now()->format('Y-m-d'),
                default => now()->addDays(random_int(1, 20))->format('Y-m-d'),
            };

            $time = sprintf('%02d:%02d', random_int(8, 17), [0, 15, 30, 45][array_rand([0, 15, 30, 45])]);
            $type = $types[array_rand($types)];
            $reason = $reasons[array_rand($reasons)];
            $branch = $branches[array_rand($branches)];
            $vet = $this->getRandomVet();

            Appointment::firstOrCreate(
                ['pet_id' => $pet->id, 'date' => $date, 'time' => $time],
                [
                    'vet_id' => $vet->id,
                    'type' => $type,
                    'status' => $status,
                    'reason' => $reason,
                    'duration' => 30,
                    'created_by' => $vet->id,
                    'branch_id' => $branch->id,
                ]
            );
        }

        $this->line('  [ appointments ]  ' . count($statuses) . ' consultas');
    }

    private function createMedicalRecords(): void
    {
        $completedAppts = Appointment::where('status', 'completed')->get();
        $count = 0;

        foreach ($completedAppts as $appt) {
            if ($count >= 10) break;
            if (MedicalRecord::where('appointment_id', $appt->id)->exists()) continue;

            $diagnoses = [
                'Dermatite alérgica', 'Otite externa bacteriana', 'Gastrite aguda',
                'Parvovirose canina', 'Infecção urinária', 'Luxação de patela',
                'Cinomose — fase neurológica', 'Obesidade mórbida',
                'Insuficiência renal crônica', 'Conjuntivite bacteriana',
            ];
            $diagnosis = $diagnoses[$count % count($diagnoses)];

            MedicalRecord::create([
                'pet_id' => $appt->pet_id,
                'appointment_id' => $appt->id,
                'user_id' => $appt->vet_id,
                'date' => $appt->date,
                'time' => $appt->time,
                'type' => $appt->type,
                'chief_complaint' => $appt->reason,
                'anamnesis' => 'Tutor relata que o animal apresenta os sintomas há aproximadamente ' . random_int(2, 14) . ' dias. Sem histórico de traumas. Vacinas em dia.',
                'physical_exam' => 'Animal alerta, mucosas normocoradas, TPC ' . random_int(1, 3) . 's, hidratado. Ausculta cardíaca e pulmonar sem alterações.',
                'vital_signs' => [
                    'temperature' => (string) random_float(37.5, 39.5),
                    'heart_rate' => (string) random_int(60, 140),
                    'respiratory_rate' => (string) random_int(15, 40),
                ],
                'diagnosis' => $diagnosis,
                'treatment' => 'Prescrito tratamento conforme quadro. Orientado retorno em ' . random_int(5, 15) . ' dias para reavaliação.',
                'prognosis' => 'Bom',
                'notes' => 'Animal respondeu bem à medicação inicial.',
                'branch_id' => $appt->branch_id,
                'record_id' => null,
            ]);

            $count++;
        }

        $this->line("  [ medical_records ]  {$count} prontuários");
    }

    private function createPrescriptions(): void
    {
        $records = MedicalRecord::take(6)->get();
        $meds = [
            ['medication' => 'Amoxicilina 50mg', 'dosage' => '1', 'unit' => 'comprimido', 'frequency' => '8/8h', 'duration' => '7 dias', 'route' => 'oral'],
            ['medication' => 'Meloxicam 0,5%', 'dosage' => '0,1', 'unit' => 'ml/kg', 'frequency' => '24/24h', 'duration' => '5 dias', 'route' => 'oral'],
            ['medication' => 'Omeprazol 20mg', 'dosage' => '1', 'unit' => 'comprimido', 'frequency' => '12/12h', 'duration' => '10 dias', 'route' => 'oral'],
            ['medication' => 'Dipirona 500mg', 'dosage' => '1', 'unit' => 'comprimido', 'frequency' => '8/8h', 'duration' => '3 dias', 'route' => 'oral'],
            ['medication' => 'Cetoconazol 100mg', 'dosage' => '1', 'unit' => 'comprimido', 'frequency' => '24/24h', 'duration' => '14 dias', 'route' => 'oral'],
            ['medication' => 'Prednisona 20mg', 'dosage' => '0,5', 'unit' => 'comprimido', 'frequency' => '12/12h', 'duration' => '7 dias', 'route' => 'oral'],
        ];

        foreach ($records as $j => $record) {
            if ($j >= count($meds)) break;
            $m = $meds[$j];
            Prescription::firstOrCreate(
                ['medical_record_id' => $record->id, 'medication' => $m['medication']],
                [
                    'dosage' => $m['dosage'],
                    'unit' => $m['unit'],
                    'frequency' => $m['frequency'],
                    'duration' => $m['duration'],
                    'route' => $m['route'],
                    'instructions' => 'Administrar conforme prescrição. Manter observação.',
                    'created_by' => $record->user_id,
                    'branch_id' => $record->branch_id,
                ]
            );
        }

        $this->line('  [ prescriptions ]  ' . min(count($records), count($meds)) . ' prescrições');
    }

    private function createVaccinations(): void
    {
        $pets = Pet::where('is_active', true)->get();
        $products = Product::all();

        $vaccines = [
            ['vaccine' => 'V10', 'batch' => 'LOT2401', 'manufacturer' => 'Zoetis'],
            ['vaccine' => 'Antirrábica', 'batch' => 'LOT2402', 'manufacturer' => 'Merial'],
            ['vaccine' => 'V4 Felina', 'batch' => 'LOT2403', 'manufacturer' => 'Zoetis'],
            ['vaccine' => 'Giárdia', 'batch' => 'LOT2404', 'manufacturer' => 'MSD'],
            ['vaccine' => 'Leucemia Felina', 'batch' => 'LOT2405', 'manufacturer' => 'Boehringer'],
            ['vaccine' => 'Polivalente', 'batch' => 'LOT2406', 'manufacturer' => 'MSD'],
        ];

        foreach ($vaccines as $i => $vac) {
            $pet = $pets[$i % $pets->count()];
            $vet = $this->getRandomVet();
            Vaccination::firstOrCreate(
                ['pet_id' => $pet->id, 'vaccine' => $vac['vaccine'], 'batch' => $vac['batch']],
                [
                    'vet_id' => $vet->id,
                    'date' => now()->subMonths(random_int(1, 6)),
                    'next_date' => now()->addMonths(random_int(6, 12)),
                    'manufacturer' => $vac['manufacturer'],
                    'product_id' => $products->isNotEmpty() ? $products[$i % $products->count()]->id : null,
                ]
            );
        }

        $this->line('  [ vaccinations ]  6 vacinas (com produto vinculado)');
    }

    private function createExams(): void
    {
        $pets = Pet::where('is_active', true)->get();
        $examTypes = ['Hemograma', 'Bioquímico', 'Urinálise', 'Ultrassom', 'Raio-X', 'Teste FIV/FeLV'];
        $statuses = ['ready', 'ready', 'ready', 'pending', 'pending', 'cancelled'];

        foreach ($examTypes as $i => $type) {
            $pet = $pets[$i % $pets->count()];
            $vet = $this->getRandomVet();
            $status = $statuses[$i];
            $result = $status === 'ready' ? "Resultado dentro dos parâmetros normais para a espécie." : null;

            Exam::firstOrCreate(
                ['pet_id' => $pet->id, 'type' => $type, 'requested_date' => now()->subDays(random_int(3, 30))],
                [
                    'vet_id' => $vet->id,
                    'status' => $status,
                    'result_date' => $status === 'ready' ? now()->subDays(random_int(0, 5)) : null,
                    'result' => $result,
                    'lab_name' => 'LabVet Análises Clínicas',
                ]
            );
        }

        $this->line('  [ exams ]       6 exames');
    }

    private function createInvoices(): void
    {
        $tutors = Tutor::all();
        $pets = Pet::where('is_active', true)->get();
        if ($tutors->isEmpty()) return;

        $services = Service::all();
        $products = Product::all();
        $vets = $this->getAllVets();
        $branches = $this->getBranches();

        if ($services->isEmpty() || $products->isEmpty()) return;

        // ── Cenário 1: Apenas serviço (NFSe) ──
        $info = $this->makeInvoice('paid', now()->subDays(3), 'pix', $tutors, $pets, $vets, $branches, 0);
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $services[0]->name,
            'quantity' => 1, 'unit_price' => $services[0]->price, 'total' => $services[0]->price,
            'service_id' => $services[0]->id, 'item_type' => 'service', 'branch_id' => $info['branch']->id,
        ]);
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 2: Apenas produto (NF-e + baixa estoque) ──
        $info = $this->makeInvoice('paid', now()->subDays(2), 'credit_card', $tutors, $pets, $vets, $branches, 1);
        $prod = $products[0];
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $prod->name,
            'quantity' => 2, 'unit_price' => $prod->sale_price, 'total' => $prod->sale_price * 2,
            'product_id' => $prod->id, 'item_type' => 'product', 'branch_id' => $info['branch']->id,
        ]);
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 3: Misto (serviço + produto → NFSe + NF-e) ──
        $info = $this->makeInvoice('paid', now()->subDays(1), 'cash', $tutors, $pets, $vets, $branches, 2);
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $services[1]->name,
            'quantity' => 1, 'unit_price' => $services[1]->price, 'total' => $services[1]->price,
            'service_id' => $services[1]->id, 'item_type' => 'service', 'branch_id' => $info['branch']->id,
        ]);
        $prod2 = $products[1];
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $prod2->name,
            'quantity' => 1, 'unit_price' => $prod2->sale_price, 'total' => $prod2->sale_price,
            'product_id' => $prod2->id, 'item_type' => 'product', 'branch_id' => $info['branch']->id,
        ]);
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 4: Só avulso (sem NF, sem estoque) ──
        $info = $this->makeInvoice('paid', now()->subDays(5), 'pix', $tutors, $pets, $vets, $branches, 3);
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => 'Taxa de transporte',
            'quantity' => 1, 'unit_price' => 25.00, 'total' => 25.00,
            'item_type' => 'avulso', 'branch_id' => $info['branch']->id,
        ]);
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 5: Misto com 2 produtos + 1 serviço ──
        $info = $this->makeInvoice('paid', now()->subDays(7), 'credit_card', $tutors, $pets, $vets, $branches, 4);
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $services[2]->name,
            'quantity' => 1, 'unit_price' => $services[2]->price, 'total' => $services[2]->price,
            'service_id' => $services[2]->id, 'item_type' => 'service', 'branch_id' => $info['branch']->id,
        ]);
        foreach ([$products[2], $products[3]] as $p) {
            InvoiceItem::create([
                'invoice_id' => $info['invoice']->id,
                'description' => $p->name,
                'quantity' => 1, 'unit_price' => $p->sale_price, 'total' => $p->sale_price,
                'product_id' => $p->id, 'item_type' => 'product', 'branch_id' => $info['branch']->id,
            ]);
        }
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 6: Pendente (só serviço) ──
        $info = $this->makeInvoice('pending', null, null, $tutors, $pets, $vets, $branches, 5);
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $services[3]->name,
            'quantity' => 1, 'unit_price' => $services[3]->price, 'total' => $services[3]->price,
            'service_id' => $services[3]->id, 'item_type' => 'service', 'branch_id' => $info['branch']->id,
        ]);
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 7: Pendente (só produto) ──
        $info = $this->makeInvoice('pending', null, null, $tutors, $pets, $vets, $branches, 6);
        $p7 = $products[4];
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $p7->name,
            'quantity' => 1, 'unit_price' => $p7->sale_price, 'total' => $p7->sale_price,
            'product_id' => $p7->id, 'item_type' => 'product', 'branch_id' => $info['branch']->id,
        ]);
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 8: Pendente (misto) ──
        $info = $this->makeInvoice('pending', null, null, $tutors, $pets, $vets, $branches, 7);
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $services[4]->name,
            'quantity' => 1, 'unit_price' => $services[4]->price, 'total' => $services[4]->price,
            'service_id' => $services[4]->id, 'item_type' => 'service', 'branch_id' => $info['branch']->id,
        ]);
        $p8 = $products[5];
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $p8->name,
            'quantity' => 1, 'unit_price' => $p8->sale_price, 'total' => $p8->sale_price,
            'product_id' => $p8->id, 'item_type' => 'product', 'branch_id' => $info['branch']->id,
        ]);
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 9: Vencida (só serviço) ──
        $info = $this->makeInvoice('overdue', null, null, $tutors, $pets, $vets, $branches, 8, now()->subDays(40));
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $services[5]->name,
            'quantity' => 1, 'unit_price' => $services[5]->price, 'total' => $services[5]->price,
            'service_id' => $services[5]->id, 'item_type' => 'service', 'branch_id' => $info['branch']->id,
        ]);
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 10: Cancelada ──
        $info = $this->makeInvoice('cancelled', null, null, $tutors, $pets, $vets, $branches, 9);
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $services[0]->name,
            'quantity' => 1, 'unit_price' => $services[0]->price, 'total' => $services[0]->price,
            'service_id' => $services[0]->id, 'item_type' => 'service', 'branch_id' => $info['branch']->id,
        ]);
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 11: Paga com múltiplos produtos ──
        $info = $this->makeInvoice('paid', now()->subDays(1), 'pix', $tutors, $pets, $vets, $branches, 0);
        foreach ([$products[6], $products[7]] as $p) {
            InvoiceItem::create([
                'invoice_id' => $info['invoice']->id,
                'description' => $p->name,
                'quantity' => 1, 'unit_price' => $p->sale_price, 'total' => $p->sale_price,
                'product_id' => $p->id, 'item_type' => 'product', 'branch_id' => $info['branch']->id,
            ]);
        }
        $this->refreshInvoiceTotal($info['invoice']);

        // ── Cenário 12: Misto com avulso ──
        $info = $this->makeInvoice('paid', now()->subDays(4), 'credit_card', $tutors, $pets, $vets, $branches, 1);
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => $services[2]->name,
            'quantity' => 1, 'unit_price' => $services[2]->price, 'total' => $services[2]->price,
            'service_id' => $services[2]->id, 'item_type' => 'service', 'branch_id' => $info['branch']->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $info['invoice']->id,
            'description' => 'Taxa de visita emergencial',
            'quantity' => 1, 'unit_price' => 80.00, 'total' => 80.00,
            'item_type' => 'avulso', 'branch_id' => $info['branch']->id,
        ]);
        $this->refreshInvoiceTotal($info['invoice']);

        $this->line('  [ invoices ]    12 faturas (6 pagas, 3 pendentes, 1 vencida, 1 cancelada, 1 com NF-e+NFSe+Avulso)');
    }

    /**
     * Limpa dados financeiros existentes para regenerar cenários de venda.
     */
    private function cleanExistingData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        CommunicationQueue::truncate();
        CommissionLog::truncate();
        StaffSchedule::truncate();
        StockMovement::truncate();
        DB::table('appointment_invoice')->truncate();
        InvoiceItem::truncate();
        Invoice::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        Setting::set('demo_data_seeded', 'false');

        $this->line('Dados financeiros anteriores removidos.');
    }

    /**
     * Recalcula subtotal e total da fatura somando os itens no banco.
     */
    private function refreshInvoiceTotal(Invoice $invoice): void
    {
        $sum = InvoiceItem::where('invoice_id', $invoice->id)->sum('total');
        $invoice->update([
            'subtotal' => $sum,
            'total' => $sum,
        ]);
    }

    /**
     * Helper: cria uma fatura com dados aleatórios mas consistentes.
     * Retorna ['invoice' => Invoice, 'branch' => Branch] para o caller adicionar itens.
     */
    private function makeInvoice(string $status, $paidAt, $paymentMethod, $tutors, $pets, $vets, $branches, int $indexSeed, ?Carbon $overdueDate = null): array
    {
        $tutor = $tutors[$indexSeed % $tutors->count()];
        $pet = $pets[$indexSeed % $pets->count()];
        $vet = $vets[array_rand($vets)];
        $branch = $branches[array_rand($branches)];

        $dueDate = match ($status) {
            'overdue' => $overdueDate ?? now()->subDays(random_int(15, 45)),
            'paid' => $paidAt ? Carbon::parse($paidAt)->subDays(random_int(1, 10)) : now()->subDays(random_int(1, 15)),
            default => now()->addDays(random_int(5, 30)),
        };

        $invoice = Invoice::firstOrCreate(
            ['invoice_number' => Invoice::generateNumber()],
            [
                'tutor_id' => $tutor->id,
                'pet_id' => $pet->id,
                'user_id' => $vet->id,
                'subtotal' => 0,
                'discount' => 0,
                'total' => 0,
                'status' => $status,
                'due_date' => $dueDate,
                'paid_at' => $paidAt,
                'payment_method' => $paymentMethod,
                'branch_id' => $branch->id,
            ]
        );

        return ['invoice' => $invoice, 'branch' => $branch];
    }

    private function createCommissions(): void
    {
        $paidInvoices = Invoice::where('status', 'paid')->get();
        $vets = $this->getAllVets();
        $firstService = Service::first();

        foreach ($vets as $vet) {
            CommissionRate::firstOrCreate(
                ['user_id' => $vet->id, 'rate_type' => 'percentage'],
                [
                    'rate_value' => 10.00,
                    'is_active' => true,
                    'commissionable_type' => $firstService ? Service::class : null,
                    'commissionable_id' => $firstService?->id,
                ]
            );
        }

        foreach ($paidInvoices as $j => $inv) {
            if ($j >= 4) break;
            $vet = $vets[$j % count($vets)];
            $commissionValue = round($inv->total * 0.1, 2);
            CommissionLog::firstOrCreate(
                ['invoice_id' => $inv->id, 'user_id' => $vet->id],
                [
                    'commission_rate_id' => CommissionRate::where('user_id', $vet->id)->first()?->id,
                    'description' => "Comissão sobre fatura {$inv->invoice_number}",
                    'base_value' => $inv->total,
                    'commission_value' => $commissionValue,
                    'status' => $j % 2 === 0 ? 'paid' : 'pending',
                    'paid_at' => $j % 2 === 0 ? now()->subDays(random_int(1, 10)) : null,
                ]
            );
        }

        $this->line('  [ commissions ] ' . count($paidInvoices) . ' registros de comissão');
    }

    private function createStockMovements(): void
    {
        $products = Product::all();
        $users = [$this->vetCarlos, $this->recepMarina, $this->financialRicardo];

        $movements = [];

        foreach ($products->take(5) as $prod) {
            $user = $users[array_rand($users)];
            $inQty = random_int(10, 30);
            $movements[] = [
                'product_id' => $prod->id,
                'type' => 'in',
                'quantity' => $inQty,
                'balance_after' => $prod->stock + $inQty,
                'reference' => 'Compra #' . random_int(100, 999),
                'user_id' => $user->id,
                'created_at' => now()->subDays(random_int(5, 30)),
                'branch_id' => $this->mainBranch->id,
            ];
            $prod->increment('stock', $inQty);
        }

        foreach ($products->take(4) as $prod) {
            $user = $users[array_rand($users)];
            $outQty = random_int(1, 3);
            $movements[] = [
                'product_id' => $prod->id,
                'type' => 'out',
                'quantity' => $outQty,
                'balance_after' => $prod->stock - $outQty,
                'reference' => 'Venda fatura',
                'user_id' => $user->id,
                'created_at' => now()->subDays(random_int(1, 10)),
                'branch_id' => $this->mainBranch->id,
            ];
        }

        foreach ($movements as $mov) {
            StockMovement::create($mov);
        }

        $this->line('  [ stock_movements ]  ' . count($movements) . ' movimentações');
    }

    private function createPurchaseOrders(): void
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        if ($suppliers->isEmpty()) return;

        $ordersData = [
            ['draft', null, null, null],
            ['ordered', now()->subDays(5), now()->subDays(5), null],
            ['partial', now()->subDays(12), now()->subDays(12), null],
            ['received', now()->subDays(20), now()->subDays(20), now()->subDays(18)],
        ];

        foreach ($ordersData as $i => $od) {
            [$status, $orderedAt, $approvedAt, $receivedAt] = $od;
            $supplier = $suppliers[$i % $suppliers->count()];
            $total = 0;

            $po = PurchaseOrder::firstOrCreate(
                ['order_number' => PurchaseOrder::generateNumber()],
                [
                    'supplier_id' => $supplier->id,
                    'branch_id' => $this->mainBranch->id,
                    'status' => $status,
                    'requested_by' => $this->financialRicardo->id,
                    'approved_by' => $status !== 'draft' ? $this->financialRicardo->id : null,
                    'ordered_at' => $orderedAt,
                    'approved_at' => $approvedAt,
                    'received_at' => $receivedAt,
                ]
            );

            if ($po->wasRecentlyCreated) {
                $itemProduct = $products[$i % $products->count()];
                $qty = random_int(5, 20);
                $unitPrice = $itemProduct->cost_price;
                $lineTotal = $qty * $unitPrice;
                $total += $lineTotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $itemProduct->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'received_quantity' => $status === 'received' ? $qty : ($status === 'partial' ? intdiv($qty, 2) : 0),
                ]);

                $po->update(['total' => $total]);
            }
        }

        $this->line('  [ purchase_orders ]  4 ordens de compra (contas a pagar)');
    }

    private function createBankAccounts(): void
    {
        BankAccount::firstOrCreate(
            ['bank' => 'Banco do Brasil', 'agency' => '1234-5', 'account' => '67890-1'],
            ['branch_id' => $this->mainBranch->id, 'account_type' => 'corrente', 'description' => 'Conta principal Matriz', 'is_active' => true]
        );
        BankAccount::firstOrCreate(
            ['bank' => 'Itaú', 'agency' => '5678-9', 'account' => '12345-6'],
            ['branch_id' => $this->sulBranch->id, 'account_type' => 'corrente', 'description' => 'Conta Zona Sul', 'is_active' => true]
        );
        BankAccount::firstOrCreate(
            ['bank' => 'Caixa Econômica', 'agency' => '0001', 'account' => '99999-0'],
            ['branch_id' => $this->mainBranch->id, 'account_type' => 'poupanca', 'description' => 'Poupança reserva', 'is_active' => true]
        );

        $this->line('  [ bank_accounts ]  3 contas bancárias');
    }

    private function createBankTransactions(): void
    {
        $accounts = BankAccount::all();
        if ($accounts->isEmpty()) return;

        $paidInvoices = Invoice::where('status', 'paid')->get();
        $account = $accounts->first();

        // 2 reconciled (from invoices)
        foreach ($paidInvoices->take(2) as $i => $inv) {
            BankTransaction::firstOrCreate(
                ['description' => "Recebimento fatura {$inv->invoice_number}"],
                [
                    'bank_account_id' => $account->id,
                    'amount' => $inv->total,
                    'transaction_date' => $inv->paid_at ?? now()->subDays($i + 1),
                    'type' => 'credit',
                    'status' => 'reconciled',
                    'invoice_id' => $inv->id,
                ]
            );
        }

        // 2 pending
        BankTransaction::firstOrCreate(
            ['description' => 'Transferência recebida do cliente XPTO'],
            ['bank_account_id' => $account->id, 'amount' => 1500.00, 'transaction_date' => now()->subDays(2), 'type' => 'credit', 'status' => 'pending']
        );
        BankTransaction::firstOrCreate(
            ['description' => 'Pagamento fornecedor Farmácia Veterinária'],
            ['bank_account_id' => $account->id, 'amount' => 890.50, 'transaction_date' => now()->subDays(1), 'type' => 'debit', 'status' => 'pending']
        );

        // 2 unmatched
        BankTransaction::firstOrCreate(
            ['description' => 'Depósito identificado'],
            ['bank_account_id' => $account->id, 'amount' => 320.00, 'transaction_date' => now()->subDays(7), 'type' => 'credit', 'status' => 'unmatched']
        );
        BankTransaction::firstOrCreate(
            ['description' => 'Tarifa bancária'],
            ['bank_account_id' => $account->id, 'amount' => 25.00, 'transaction_date' => now()->subDays(10), 'type' => 'debit', 'status' => 'unmatched']
        );

        $this->line('  [ bank_transactions ]  6 transações bancárias');
    }

    private function createConvenioPetsAndClaims(): void
    {
        $convenios = Convenio::all();
        $pets = Pet::where('is_active', true)->take(4)->get();

        foreach ($pets as $i => $pet) {
            $conv = $convenios[$i % $convenios->count()];
            $cp = ConvenioPet::firstOrCreate(
                ['pet_id' => $pet->id, 'convenio_id' => $conv->id],
                [
                    'policy_number' => 'POL-' . str_pad((string) random_int(10000, 99999), 8, '0', STR_PAD_LEFT),
                    'start_date' => now()->subMonths(random_int(3, 12)),
                    'end_date' => now()->addMonths(random_int(6, 18)),
                ]
            );

            if ($i < 3) {
                $invoice = Invoice::where('tutor_id', $pet->tutors->first()?->id)->first();
                $claimsStatus = ['approved', 'pending', 'rejected'];
                ConvenioClaim::firstOrCreate(
                    ['claim_number' => 'CLM-' . str_pad((string) random_int(1000, 9999), 6, '0', STR_PAD_LEFT)],
                    [
                        'convenio_pet_id' => $cp->id,
                        'invoice_id' => $invoice?->id,
                        'status' => $claimsStatus[$i],
                        'amount_requested' => random_int(150, 800),
                        'amount_approved' => $claimsStatus[$i] === 'approved' ? random_int(100, 600) : null,
                        'filed_at' => now()->subDays(random_int(10, 30)),
                        'response_at' => $claimsStatus[$i] !== 'pending' ? now()->subDays(random_int(1, 5)) : null,
                    ]
                );
            }
        }

        $this->line('  [ convenios ]   4 pets com convênio, 3 claims');
    }

    private function createTreatmentPlans(): void
    {
        $pets = Pet::where('is_active', true)->take(3)->get();
        $statuses = ['draft', 'approved', 'in_progress'];
        $titles = ['Plano de castração', 'Tratamento ortodôntico', 'Fisioterapia pós-cirúrgica'];

        foreach ($pets as $i => $pet) {
            $tutor = $pet->tutors->first();
            if (!$tutor) continue;
            $vet = $this->getRandomVet();

            $tp = TreatmentPlan::firstOrCreate(
                ['plan_number' => TreatmentPlan::generateNumber()],
                [
                    'pet_id' => $pet->id,
                    'tutor_id' => $tutor->id,
                    'vet_id' => $vet->id,
                    'title' => $titles[$i],
                    'description' => "{$titles[$i]} para {$pet->name}",
                    'total_estimated' => random_int(300, 2000),
                    'status' => $statuses[$i],
                    'client_approved_at' => $statuses[$i] !== 'draft' ? now()->subDays(random_int(1, 10)) : null,
                    'branch_id' => $pet->created_at_branch_id ?? $this->mainBranch->id,
                ]
            );

            if ($tp->wasRecentlyCreated) {
                TreatmentPlanItem::create([
                    'treatment_plan_id' => $tp->id,
                    'description' => 'Consulta de avaliação',
                    'category' => 'consulta',
                    'quantity' => 1,
                    'unit_price' => 150.00,
                    'total' => 150.00,
                    'is_authorized' => $statuses[$i] !== 'draft',
                ]);
            }
        }

        $this->line('  [ treatment_plans ]  3 planos de tratamento');
    }

    private function createHospitalizations(): void
    {
        $pets = Pet::where('is_active', true)->take(2)->get();
        $cases = [
            ['reason' => 'Parvovirose — desidratação severa', 'status' => 'active', 'department' => 'UTI', 'emergency' => true],
            ['reason' => 'Pós-operatório de castração', 'status' => 'discharged', 'department' => 'Enfermaria', 'emergency' => false],
        ];

        foreach ($pets as $i => $pet) {
            $tutor = $pet->tutors->first();
            if (!$tutor || !isset($cases[$i])) continue;
            $c = $cases[$i];
            $vet = $this->getRandomVet();

            Hospitalization::firstOrCreate(
                ['pet_id' => $pet->id, 'admission_reason' => $c['reason']],
                [
                    'tutor_id' => $tutor->id,
                    'vet_id' => $vet->id,
                    'admission_date' => $c['status'] === 'active' ? now()->subDays(2) : now()->subDays(10),
                    'admission_reason' => $c['reason'],
                    'initial_diagnosis' => $c['reason'],
                    'department' => $c['department'],
                    'is_emergency' => $c['emergency'],
                    'status' => $c['status'],
                    'discharged_at' => $c['status'] === 'discharged' ? now()->subDays(5) : null,
                    'discharge_summary' => $c['status'] === 'discharged' ? 'Animal recuperado, alta concedida.' : null,
                    'branch_id' => $this->mainBranch->id,
                ]
            );
        }

        $this->line('  [ hospitalizations ]  2 internações');
    }

    private function createCommunicationQueue(): void
    {
        $tutors = Tutor::all();
        if ($tutors->isEmpty()) return;

        $messages = [
            ['channel' => 'whatsapp', 'subject' => 'Lembrete de consulta', 'message' => 'Olá! Lembramos da consulta do seu pet amanhã às 10h. Confirme presença!'],
            ['channel' => 'whatsapp', 'subject' => 'Vacinação atrasada', 'message' => 'A vacina do seu pet está atrasada. Agende hoje mesmo!'],
            ['channel' => 'email', 'subject' => 'Campanha de castração', 'message' => 'Aproveite nossa campanha de castração com 20% de desconto. Vagas limitadas!'],
            ['channel' => 'sms', 'subject' => 'Resultado de exame', 'message' => 'O resultado do exame do seu pet já está disponível. Acesse nosso sistema.'],
            ['channel' => 'whatsapp', 'subject' => 'Aniversário do pet', 'message' => 'Parabéns pelo aniversário do seu pet! Ganhe 10% de desconto em qualquer serviço.'],
        ];

        foreach ($messages as $i => $msg) {
            $tutor = $tutors[$i % $tutors->count()];
            CommunicationQueue::create([
                'tutor_id' => $tutor->id,
                'channel' => $msg['channel'],
                'destination' => $tutor->phone ?? '11999999999',
                'message_content' => $msg['message'],
                'scheduled_at' => $i < 3 ? now()->addHours(random_int(1, 48)) : now()->subDays(random_int(1, 5)),
                'sent_at' => $i >= 3 ? now()->subDays(random_int(1, 5)) : null,
                'status' => $i < 3 ? 'pending' : 'sent',
            ]);
        }

        $this->line('  [ communication_queue ]  5 mensagens');
    }
}

function random_float(float $min, float $max): float
{
    return round($min + mt_rand() / mt_getrandmax() * ($max - $min), 1);
}
