<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    private function groupedPermissions(): array
    {
        $labels = [
            'admin' => 'Administração',
            'anesthesia' => 'Anestesia',
            'appointments' => 'Agendamentos',
            'audit-logs' => 'Auditoria',
            'backups' => 'Backups',
            'bank-reconciliation' => 'Conciliação Bancária',
            'boardings' => 'Hospedagem',
            'branches' => 'Filiais',
            'branding' => 'Branding',
            'breed-defaults' => 'Padrões de Raça',
            'categories' => 'Categorias',
            'chat' => 'Chat',
            'clinical-report-templates' => 'Modelos de Laudos',
            'commissions' => 'Comissões',
            'communication' => 'Comunicação',
            'configuracoes' => 'Configurações',
            'consent-forms' => 'Termos de Consentimento',
            'controlled-substances' => 'Substâncias Controladas',
            'convenio-claims' => 'Guias de Convênio',
            'convenios' => 'Convênios',
            'corporate-dashboard' => 'Painel Corporativo',
            'dental-charts' => 'Odontologia',
            'departments' => 'Departamentos',
            'diet-plans' => 'Planos Alimentares',
            'docs' => 'Documentos',
            'drug-formulary' => 'Formulário de Medicamentos',
            'drug-interactions' => 'Interações Medicamentosas',
            'emergency-protocols' => 'Protocolos de Emergência',
            'employees' => 'Funcionários',
            'exams' => 'Exames',
            'execution-maps' => 'Mapas de Execução',
            'financial-reports' => 'Relatórios Financeiros',
            'grooming-templates' => 'Modelos de Tosa',
            'health-certificates' => 'Atestados de Saúde',
            'hospitalizations' => 'Internações',
            'imaging' => 'Imaginologia',
            'invoices' => 'Faturas',
            'lab-equipment' => 'Equipamentos de Laboratório',
            'laboratory' => 'Laboratório',
            'medical-records' => 'Prontuários',
            'nfe' => 'NF-e',
            'nfe-config' => 'Config. NF-e',
            'nfse' => 'NFS-e',
            'nfse-config' => 'Config. NFS-e',
            'notification-logs' => 'Logs de Notificação',
            'online-bookings' => 'Agendamentos Online',
            'parasite-controls' => 'Controle de Parasitas',
            'payment-gateways' => 'Gateways de Pagamento',
            'payments' => 'Pagamentos',
            'pet-death-records' => 'Óbitos',
            'pets' => 'Pets',
            'positions' => 'Cargos',
            'pre-anesthetic' => 'Pré-Anestésico',
            'prescriptions' => 'Prescrições',
            'products' => 'Produtos',
            'purchase-orders' => 'Ordens de Compra',
            'referrals' => 'Encaminhamentos',
            'roles' => 'Perfis',
            'schedules-on-call' => 'Plantões',
            'services' => 'Serviços',
            'staff-notes' => 'Anotações Internas',
            'staff-schedules' => 'Escalas',
            'stock' => 'Estoque',
            'suppliers' => 'Fornecedores',
            'surgeries' => 'Cirurgias',
            'system-update' => 'Atualização do Sistema',
            'teleconsultations' => 'Teleconsultas',
            'therapy-sessions' => 'Sessões de Terapia',
            'treatment-plans' => 'Planos de Tratamento',
            'triage' => 'Triagem',
            'tutors' => 'Tutores',
            'users' => 'Usuários',
            'vaccination-reminders' => 'Lembretes de Vacina',
            'vaccinations' => 'Vacinações',
            'vaccine-protocols' => 'Protocolos de Vacina',
            'vet-shifts' => 'Turnos',
            'weight-records' => 'Controle de Peso',
            'zoonotic-diseases' => 'Doenças Zoonóticas',
        ];

        $permissions = Permission::orderBy('name')->get();
        $grouped = [];
        foreach ($permissions as $perm) {
            $parts = explode('.', $perm->name, 2);
            $group = $parts[0] ?? 'outros';
            $grouped[$group]['perms'][] = $perm;
            $grouped[$group]['label'] = $labels[$group] ?? ucfirst($group);
        }
        return $grouped;
    }

    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $groupedPermissions = $this->groupedPermissions();
        return view('roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'slug' => 'required|string|max:50|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:Spatie\Permission\Models\Permission,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'guard_name' => 'web',
        ]);

        $spatieRole = SpatieRole::findOrCreate($validated['slug'], 'web');
        $spatieRole->syncPermissions($request->permissions ?? []);

        $role->spatiePermissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Perfil cadastrado!');
    }

    public function show(Role $role)
    {
        $role->load('users');
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $groupedPermissions = $this->groupedPermissions();
        $role->load('spatiePermissions');
        return view('roles.edit', compact('role', 'groupedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'slug' => 'required|string|max:50|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:Spatie\Permission\Models\Permission,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        try {
            $spatieRole = SpatieRole::findByName($role->slug, 'web');
            $spatieRole->syncPermissions($request->permissions ?? []);
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            // Spatie role will be created on next sync
        }

        $role->spatiePermissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Perfil atualizado!');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Perfil possui usuários vinculados.');
        }

        try {
            $spatieRole = SpatieRole::findByName($role->slug, 'web');
            $spatieRole->delete();
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            // already gone
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Perfil excluído!');
    }
}
