# ── Roteiros de Treinamento ──────────────────────────────────────────────────
# Cada roteiro define os passos para um módulo da documentação.
# Formato dos passos:
#   {"passo": N, "acao": str, "legenda": str, "pausa": N, ...}
#
# Ações suportadas:
#   navegar    → {"url": "..."}
#   preencher  → {"seletor": "css", "valor": "..."}
#   livewire   → {"wire_model": "...", "valor": "..."}
#   clicar     → {"seletor": "css"}
#   esperar    → {"seletor": "css"}  (espera elemento aparecer)
#   legenda    → {"texto": "..."}  (exibe no console, sem ação no browser)
#   submit_modal → {"modal": "#tutorModal"}  (clica submit dentro do modal)
#   tom_select → {"wire_model": "...", "valor": "...", "label": "..."}
BASE_URL = "http://127.0.0.1:8000"

# ── Helpers para criar passos comuns ─────────────────────────────────────────

def login(email, senha):
    return [
        {"passo": 1, "acao": "navegar", "url": f"{BASE_URL}/login",
         "legenda": "Acessando o sistema…", "pausa": 2},
        {"passo": 2, "acao": "preencher", "seletor": "input[name=email]", "valor": email,
         "legenda": "Digitando o email…", "pausa": 1},
        {"passo": 3, "acao": "preencher", "seletor": "input[name=password]", "valor": senha,
         "legenda": "Digitando a senha…", "pausa": 1},
        {"passo": 4, "acao": "clicar", "seletor": "button[type=submit]",
         "legenda": "Entrando no sistema…", "pausa": 3},
    ]

def logout():
    return [
        {"passo": 999, "acao": "clicar", "seletor": "a[onclick*='logout-form']",
         "legenda": "Saindo do sistema…", "pausa": 2},
    ]

# ── Roteiros ──────────────────────────────────────────────────────────────────

ROTEIRO_11_TUTORES_PETS = {
    "nome": "Tutores e Pets",
    "arquivo": "11-tutores-pets",
    "credenciais": {"email": "recep@vet.com", "senha": "recep123"},
    "passos": [
        *login("recep@vet.com", "recep123"),
        # ── Cadastro de tutor ──
        {"passo": 5, "acao": "navegar", "url": f"{BASE_URL}/tutors",
         "legenda": "Abrindo cadastro de tutores…", "pausa": 2},
        {"passo": 6, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 7, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Novo Tutor…", "pausa": 2},
        {"passo": 8, "acao": "esperar", "seletor": "#tutorModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 9, "acao": "livewire", "wire_model": "name", "valor": "Maria das Dores",
         "legenda": "Preenchendo nome…", "pausa": 1},
        {"passo": 10, "acao": "livewire", "wire_model": "cpf", "valor": "123.456.789-00",
         "legenda": "Preenchendo CPF…", "pausa": 1},
        {"passo": 11, "acao": "livewire", "wire_model": "email", "valor": "maria.dores@email.com",
         "legenda": "Preenchendo e-mail…", "pausa": 1},
        {"passo": 12, "acao": "livewire", "wire_model": "phone", "valor": "(11) 99999-8888",
         "legenda": "Preenchendo telefone…", "pausa": 1},
        {"passo": 13, "acao": "livewire", "wire_model": "zipcode", "valor": "01001-000",
         "legenda": "Preenchendo CEP…", "pausa": 2},
        {"passo": 14, "acao": "livewire", "wire_model": "address", "valor": "Rua das Flores, 123",
         "legenda": "Preenchendo endereço…", "pausa": 1},
        {"passo": 15, "acao": "livewire", "wire_model": "neighborhood", "valor": "Centro",
         "legenda": "Preenchendo bairro…", "pausa": 1},
        {"passo": 16, "acao": "submit_modal", "modal": "#tutorModal",
         "legenda": "Salvando tutor…", "pausa": 4},
        # ── Cadastro de pet (modal) ──
        {"passo": 17, "acao": "navegar", "url": f"{BASE_URL}/pets",
         "legenda": "Abrindo cadastro de pets…", "pausa": 2},
        {"passo": 18, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 19, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Novo Pet…", "pausa": 2},
        {"passo": 20, "acao": "esperar", "seletor": "#petModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 21, "acao": "livewire", "wire_model": "name", "valor": "Rex",
         "legenda": "Preenchendo nome do pet…", "pausa": 1},
        {"passo": 22, "acao": "livewire", "wire_model": "species", "valor": "canine",
         "legenda": "Selecionando espécie…", "pausa": 2},
        {"passo": 23, "acao": "tom_select", "wire_model": "tutor_id",
         "valor": "Maria das Dores",
         "legenda": "Selecionando tutor…", "pausa": 2},
        {"passo": 24, "acao": "livewire", "wire_model": "gender", "valor": "male",
         "legenda": "Selecionando sexo…", "pausa": 1},
        {"passo": 25, "acao": "livewire", "wire_model": "size", "valor": "medium",
         "legenda": "Selecionando porte…", "pausa": 1},
        {"passo": 26, "acao": "livewire", "wire_model": "color", "valor": "Caramelo",
         "legenda": "Preenchendo cor…", "pausa": 1},
        {"passo": 27, "acao": "livewire", "wire_model": "birth_date", "valor": "2023-05-15",
         "legenda": "Preenchendo data de nascimento…", "pausa": 1},
        {"passo": 28, "acao": "submit_modal", "modal": "#petModal",
         "legenda": "Salvando pet…", "pausa": 5},
        # ── Final ──
        {"passo": 29, "acao": "navegar", "url": f"{BASE_URL}/pets",
         "legenda": "Visualizando lista de pets…", "pausa": 2},
        {"passo": 30, "acao": "legenda", "texto": "Tutor Maria das Dores e pet Rex cadastrados com sucesso!",
         "pausa": 4},
    ],
}

ROTEIRO_07_FARMACIA = {
    "nome": "Farmácia",
    "arquivo": "07-farmacia",
    "credenciais": {"email": "super@vet.com", "senha": "super123"},
    "passos": [
        # Cleanup obrigatório: remove dados de execuções anteriores
        {"passo": 0, "acao": "shell",
         "comando": "php artisan treinamento:cleanup --module=07-farmacia",
         "legenda": "Limpando dados de execuções anteriores…", "pausa": 2},
        *login("super@vet.com", "super123"),
        # ── Categoria Produto (admin) ──
        {"passo": 5, "acao": "navegar", "url": f"{BASE_URL}/categories",
         "legenda": "Abrindo categorias…", "pausa": 2},
        {"passo": 6, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 7, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Nova Categoria…", "pausa": 2},
        {"passo": 8, "acao": "esperar", "seletor": "#categoryModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 9, "acao": "livewire", "wire_model": "name", "valor": "Medicamentos",
         "legenda": "Preenchendo nome da categoria…", "pausa": 1},
        {"passo": 10, "acao": "livewire", "wire_model": "type", "valor": "product",
         "legenda": "Selecionando tipo Produto…", "pausa": 1},
        {"passo": 11, "acao": "submit_modal", "modal": "#categoryModal",
         "legenda": "Salvando categoria…", "pausa": 4},
        # ── Fornecedor ──
        {"passo": 12, "acao": "navegar", "url": f"{BASE_URL}/suppliers",
         "legenda": "Abrindo fornecedores…", "pausa": 2},
        {"passo": 13, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 14, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Novo Fornecedor…", "pausa": 2},
        {"passo": 15, "acao": "esperar", "seletor": "#supplierModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 16, "acao": "livewire", "wire_model": "name", "valor": "FarMed Distribuidora",
         "legenda": "Preenchendo nome do fornecedor…", "pausa": 1},
        {"passo": 17, "acao": "livewire", "wire_model": "phone", "valor": "(11) 3000-4000",
         "legenda": "Preenchendo telefone…", "pausa": 1},
        {"passo": 18, "acao": "livewire", "wire_model": "email", "valor": "contato@farmdist.com.br",
         "legenda": "Preenchendo e-mail…", "pausa": 1},
        {"passo": 19, "acao": "submit_modal", "modal": "#supplierModal",
         "legenda": "Salvando fornecedor…", "pausa": 4},
        # ── Produto ──
        {"passo": 20, "acao": "navegar", "url": f"{BASE_URL}/products",
         "legenda": "Abrindo produtos…", "pausa": 2},
        {"passo": 21, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 22, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Novo Produto…", "pausa": 2},
        {"passo": 23, "acao": "esperar", "seletor": "#productModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 24, "acao": "livewire", "wire_model": "name", "valor": "Dipirona 500mg",
         "legenda": "Preenchendo nome do produto…", "pausa": 1},
        {"passo": 25, "acao": "livewire", "wire_model": "stock", "valor": "100",
         "legenda": "Preenchendo estoque inicial…", "pausa": 1},
        {"passo": 26, "acao": "livewire", "wire_model": "cost_price", "valor": "5,00",
         "legenda": "Preenchendo preço de custo…", "pausa": 1},
        {"passo": 27, "acao": "livewire", "wire_model": "sale_price", "valor": "15,00",
         "legenda": "Preenchendo preço de venda…", "pausa": 1},
        {"passo": 28, "acao": "tom_select", "wire_model": "category_id",
         "valor": "Medicamentos",
         "legenda": "Selecionando categoria…", "pausa": 2},
        {"passo": 29, "acao": "tom_select", "wire_model": "supplier_id",
         "valor": "FarMed Distribuidora",
         "legenda": "Selecionando fornecedor…", "pausa": 2},
        {"passo": 30, "acao": "submit_modal", "modal": "#productModal",
         "legenda": "Salvando produto…", "pausa": 5},
        # ── Ajuste de Estoque (name-based TomSelect) ──
        {"passo": 31, "acao": "navegar", "url": f"{BASE_URL}/stock/adjust",
         "legenda": "Abrindo ajuste de estoque…", "pausa": 2},
        {"passo": 32, "acao": "preencher", "seletor": "select[name=type]", "valor": "entry",
         "legenda": "Selecionando tipo Entrada…", "pausa": 1},
        {"passo": 33, "acao": "tom_select", "wire_model": "branch_id",
         "valor": "Matriz",
         "legenda": "Selecionando unidade…", "pausa": 2},
        {"passo": 34, "acao": "tom_select", "wire_model": "product_id",
         "valor": "Dipirona 500mg",
         "legenda": "Selecionando produto…", "pausa": 2},
        {"passo": 35, "acao": "preencher", "seletor": "input[name=quantity]", "valor": "100",
         "legenda": "Preenchendo quantidade…", "pausa": 1},
        {"passo": 36, "acao": "preencher", "seletor": "input[name=batch_number]", "valor": "LOTE-001",
         "legenda": "Preenchendo lote…", "pausa": 1},
        {"passo": 37, "acao": "clicar", "seletor": "button[type=submit]",
         "legenda": "Salvando ajuste de estoque…", "pausa": 4},
        # ── Formulário de Medicamentos ──
        {"passo": 38, "acao": "navegar", "url": f"{BASE_URL}/drug-formulary",
         "legenda": "Abrindo formulário de fármacos…", "pausa": 2},
        {"passo": 39, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 40, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Clicando em Novo Fármaco…", "pausa": 2},
        {"passo": 41, "acao": "esperar", "seletor": "#drugFormularyModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 42, "acao": "livewire", "wire_model": "drug", "valor": "Dipirona Sódica",
         "legenda": "Preenchendo nome do fármaco…", "pausa": 1},
        {"passo": 43, "acao": "livewire", "wire_model": "species", "valor": "canina",
         "legenda": "Selecionando espécie…", "pausa": 1},
        {"passo": 44, "acao": "livewire", "wire_model": "dosage_mg_kg", "valor": "25",
         "legenda": "Preenchendo dosagem…", "pausa": 1},
        {"passo": 45, "acao": "livewire", "wire_model": "route", "valor": "IV",
         "legenda": "Selecionando via IV…", "pausa": 1},
        {"passo": 46, "acao": "submit_modal", "modal": "#drugFormularyModal",
         "legenda": "Salvando fármaco…", "pausa": 5},
        # ── Final: mostra lista de produtos com estoque ──
        {"passo": 47, "acao": "navegar", "url": f"{BASE_URL}/products",
         "legenda": "Visualizando lista de produtos…", "pausa": 3},
        {"passo": 48, "acao": "legenda", "texto": "Categoria, fornecedor, produto e fármaco cadastrados com sucesso!",
         "pausa": 4},
        *logout(),
    ],
}

ROTEIRO_10_AGENDAMENTO = {
    "nome": "Agendamento",
    "arquivo": "10-agendamento",
    "credenciais": {"email": "recep@vet.com", "senha": "recep123"},
    "passos": [
        *login("recep@vet.com", "recep123"),
        {"passo": 5, "acao": "legenda", "texto": "Módulo Agendamento (implementação futura)",
         "pausa": 3},
    ],
}

ROTEIRO_01_PRONTUARIOS = {
    "nome": "Prontuários",
    "arquivo": "01-prontuarios",
    "credenciais": {"email": "vet@vet.com", "senha": "vet123"},
    "passos": [
        *login("vet@vet.com", "vet123"),
        {"passo": 5, "acao": "legenda", "texto": "Módulo Prontuários (implementação futura)",
         "pausa": 3},
    ],
}

ROTEIRO_09_FINANCEIRO = {
    "nome": "Financeiro",
    "arquivo": "09-financeiro",
    "credenciais": {"email": "vet@vet.com", "senha": "vet123"},
    "passos": [
        *login("vet@vet.com", "vet123"),
        {"passo": 5, "acao": "legenda", "texto": "Módulo Financeiro (implementação futura)",
         "pausa": 3},
        *logout(),
        *login("financeiro@vet.com", "fin123"),
        {"passo": 6, "acao": "legenda", "texto": "Perfil Financeiro logado",
         "pausa": 3},
    ],
}

def portal_login(email, senha):
    return [
        {"passo": 1, "acao": "navegar", "url": f"{BASE_URL}/portal/login",
         "legenda": "Acessando o Portal do Tutor…", "pausa": 2},
        {"passo": 2, "acao": "preencher", "seletor": "input[name=email]", "valor": email,
         "legenda": "Digitando o email…", "pausa": 1},
        {"passo": 3, "acao": "preencher", "seletor": "input[name=password]", "valor": senha,
         "legenda": "Digitando a senha…", "pausa": 1},
        {"passo": 4, "acao": "clicar", "seletor": "button[type=submit]",
         "legenda": "Entrando no portal…", "pausa": 3},
    ]

# ── Roteiros ──────────────────────────────────────────────────────────────────

ROTEIRO_05_PORTAL_TUTOR = {
    "nome": "Portal do Tutor",
    "arquivo": "05-portal-tutor",
    "credenciais": {"email": "tutor@vet.com", "senha": "tutor123"},
    "passos": [
        {"passo": 0, "acao": "shell",
         "comando": "php artisan treinamento:cleanup --module=05-portal-tutor",
         "legenda": "Limpando dados de execuções anteriores…", "pausa": 2},
        *portal_login("tutor@vet.com", "tutor123"),
        # ── Dashboard ──
        {"passo": 5, "acao": "navegar", "url": f"{BASE_URL}/portal/dashboard",
         "legenda": "Acessando o painel do tutor…", "pausa": 3},
        {"passo": 6, "acao": "esperar", "seletor": ".content-wrapper, .portal-card",
         "legenda": "Aguardando dashboard carregar…", "pausa": 2},
        {"passo": 7, "acao": "legenda", "texto": "Este é o painel principal do tutor, com acesso rápido a pets, consultas e faturas.",
         "pausa": 3},
        # ── Pets ──
        {"passo": 8, "acao": "navegar", "url": f"{BASE_URL}/portal/pets",
         "legenda": "Acessando lista de pets…", "pausa": 2},
        {"passo": 9, "acao": "esperar", "seletor": ".content-wrapper, .portal-card",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 10, "acao": "legenda", "texto": "Aqui o tutor visualiza todos os seus pets cadastrados.",
         "pausa": 3},
        # ── Detalhe do primeiro pet (se existir) ──
        {"passo": 11, "acao": "clicar", "seletor": "a[href*='/portal/pets/']",
         "legenda": "Clicando no primeiro pet…", "pausa": 3},
        {"passo": 12, "acao": "esperar", "seletor": ".content-wrapper, .portal-card",
         "legenda": "Aguardando detalhes do pet…", "pausa": 2},
        {"passo": 13, "acao": "legenda", "texto": "No detalhe do pet, o tutor vê prontuários, vacinas e consultas.",
         "pausa": 3},
        # ── Agendamentos ──
        {"passo": 14, "acao": "navegar", "url": f"{BASE_URL}/portal/appointments",
         "legenda": "Acessando agendamentos…", "pausa": 2},
        {"passo": 15, "acao": "esperar", "seletor": ".content-wrapper, .portal-card",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 16, "acao": "legenda", "texto": "Lista de consultas agendadas e anteriores do tutor.",
         "pausa": 3},
        # ── Faturas ──
        {"passo": 17, "acao": "navegar", "url": f"{BASE_URL}/portal/invoices",
         "legenda": "Acessando faturas…", "pausa": 2},
        {"passo": 18, "acao": "esperar", "seletor": ".content-wrapper, .portal-card",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 19, "acao": "legenda", "texto": "Faturas pendentes e pagas do tutor, com opção de pagamento via PIX.",
         "pausa": 3},
        # ── Prontuários ──
        {"passo": 20, "acao": "navegar", "url": f"{BASE_URL}/portal/medical-records",
         "legenda": "Acessando prontuários…", "pausa": 2},
        {"passo": 21, "acao": "esperar", "seletor": ".content-wrapper, .portal-card",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 22, "acao": "legenda", "texto": "Histórico de atendimentos dos pets do tutor.",
         "pausa": 3},
        # ── Vacinas ──
        {"passo": 23, "acao": "navegar", "url": f"{BASE_URL}/portal/vaccinations",
         "legenda": "Acessando vacinas…", "pausa": 2},
        {"passo": 24, "acao": "esperar", "seletor": ".content-wrapper, .portal-card",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 25, "acao": "legenda", "texto": "Carteira de vacinação dos pets, com lembretes de doses futuras.",
         "pausa": 3},
        # ── Manual do Tutor ──
        {"passo": 26, "acao": "navegar", "url": f"{BASE_URL}/portal/docs",
         "legenda": "Acessando manual do tutor…", "pausa": 2},
        {"passo": 27, "acao": "esperar", "seletor": ".content-wrapper, .portal-card",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 28, "acao": "legenda", "texto": "Documentação de ajuda disponível diretamente no portal.",
         "pausa": 3},
        # ── Final ──
        {"passo": 29, "acao": "legenda", "texto": "Portal do Tutor demonstrado com sucesso!",
         "pausa": 4},
    ],
}

ROTEIRO_08_ADMIN_CONFIG = {
    "nome": "Administração e Configurações",
    "arquivo": "08-admin-config",
    "credenciais": {"email": "super@vet.com", "senha": "super123"},
    "passos": [
        *login("super@vet.com", "super123"),
        # ── Lista de Usuários ──
        {"passo": 5, "acao": "navegar", "url": f"{BASE_URL}/users",
         "legenda": "Abrindo lista de usuários…", "pausa": 2},
        {"passo": 6, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 7, "acao": "legenda", "texto": "Gerenciamento de usuários do sistema — criação, edição e perfis de acesso.",
         "pausa": 3},
        # ── Criar novo usuário (visualizar modal) ──
        {"passo": 8, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Abrindo formulário de novo usuário…", "pausa": 2},
        {"passo": 9, "acao": "esperar", "seletor": "#userModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 10, "acao": "legenda", "texto": "Formulário de cadastro com campos nome, email, senha e seleção de perfil.",
         "pausa": 3},
        {"passo": 11, "acao": "clicar", "seletor": "#userModal .btn-secondary, #userModal button[data-dismiss='modal']",
         "legenda": "Fechando modal…", "pausa": 1},
        # ── Perfis / Permissões (Roles) ──
        {"passo": 12, "acao": "navegar", "url": f"{BASE_URL}/roles",
         "legenda": "Abrindo perfis e permissões…", "pausa": 2},
        {"passo": 13, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 14, "acao": "legenda", "texto": "Perfis de acesso (roles) com permissões granulares para cada módulo do sistema.",
         "pausa": 3},
        # ── Unidades (Branches) ──
        {"passo": 15, "acao": "navegar", "url": f"{BASE_URL}/branches",
         "legenda": "Abrindo unidades…", "pausa": 2},
        {"passo": 16, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 17, "acao": "legenda", "texto": "Cadastro de unidades (filiais) da clínica, com endereço e dados de contato.",
         "pausa": 3},
        # ── Categorias ──
        {"passo": 18, "acao": "navegar", "url": f"{BASE_URL}/categories",
         "legenda": "Abrindo categorias…", "pausa": 2},
        {"passo": 19, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 20, "acao": "legenda", "texto": "Categorias de produtos e serviços para organização do catálogo.",
         "pausa": 3},
        # ── Notificações ──
        {"passo": 21, "acao": "navegar", "url": f"{BASE_URL}/configuracoes/notificacoes",
         "legenda": "Abrindo configurações de notificações…", "pausa": 2},
        {"passo": 22, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 23, "acao": "legenda", "texto": "Configuração de canais de notificação: e-mail (MailerSend), WhatsApp (Z-API),templates e fila.",
         "pausa": 3},
        # ── Personalização (Branding) ──
        {"passo": 24, "acao": "navegar", "url": f"{BASE_URL}/configuracoes/branding",
         "legenda": "Abrindo personalização…", "pausa": 2},
        {"passo": 25, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 26, "acao": "legenda", "texto": "Personalização visual: cores, logo e nome da clínica.",
         "pausa": 3},
        # ── Atualizar Sistema ──
        {"passo": 27, "acao": "navegar", "url": f"{BASE_URL}/system-update",
         "legenda": "Abrindo atualização do sistema…", "pausa": 2},
        {"passo": 28, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 29, "acao": "legenda", "texto": "Painel de atualização do sistema com verificação de versão e aplicação de patches.",
         "pausa": 3},
        # ── Configuração NF ──
        {"passo": 30, "acao": "navegar", "url": f"{BASE_URL}/nf/config",
         "legenda": "Abrindo configuração de notas fiscais…", "pausa": 2},
        {"passo": 31, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 32, "acao": "legenda", "texto": "Configuração unificada de NFS-e e NF-e/NFC-e com provedores NFE.io, Webmania e FocusNFe.",
         "pausa": 3},
        # ── Logs de Auditoria ──
        {"passo": 33, "acao": "navegar", "url": f"{BASE_URL}/audit-logs",
         "legenda": "Abrindo logs de auditoria…", "pausa": 2},
        {"passo": 34, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 35, "acao": "legenda", "texto": "Registro de auditoria de todas as ações realizadas no sistema.",
         "pausa": 3},
        # ── Final ──
        {"passo": 36, "acao": "legenda", "texto": "Módulo de administração e configurações demonstrado com sucesso!",
         "pausa": 4},
        *logout(),
    ],
}

ROTEIRO_12_COMUNICACAO = {
    "nome": "Comunicação",
    "arquivo": "12-comunicacao",
    "credenciais": {"email": "super@vet.com", "senha": "super123"},
    "passos": [
        *login("super@vet.com", "super123"),
        # ── Chat Interno ──
        {"passo": 5, "acao": "navegar", "url": f"{BASE_URL}/chat",
         "legenda": "Abrindo chat interno…", "pausa": 2},
        {"passo": 6, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 7, "acao": "legenda", "texto": "Chat interno para comunicação em tempo real entre os colaboradores da clínica.",
         "pausa": 3},
        # ── Notas Internas ──
        {"passo": 8, "acao": "navegar", "url": f"{BASE_URL}/staff-notes",
         "legenda": "Abrindo notas internas…", "pausa": 2},
        {"passo": 9, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 10, "acao": "legenda", "texto": "Notas internas para registro de observações entre a equipe.",
         "pausa": 3},
        # ── Criar nota interna ──
        {"passo": 11, "acao": "clicar", "seletor": "button[onclick*='openCreateModal']",
         "legenda": "Abrindo formulário de nova nota…", "pausa": 2},
        {"passo": 12, "acao": "esperar", "seletor": "#staffNoteModal",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 13, "acao": "livewire", "wire_model": "title", "valor": "Nota de Treinamento",
         "legenda": "Preenchendo título da nota…", "pausa": 1},
        {"passo": 14, "acao": "livewire", "wire_model": "content", "valor": "Esta é uma nota interna criada durante o treinamento do sistema.",
         "legenda": "Preenchendo conteúdo…", "pausa": 1},
        {"passo": 15, "acao": "submit_modal", "modal": "#staffNoteModal",
         "legenda": "Salvando nota interna…", "pausa": 4},
        # ── Logs de Notificação ──
        {"passo": 16, "acao": "navegar", "url": f"{BASE_URL}/notification-logs",
         "legenda": "Abrindo logs de notificação…", "pausa": 2},
        {"passo": 17, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 18, "acao": "legenda", "texto": "Histórico de notificações enviadas: e-mails, WhatsApp e lembretes de vacinas.",
         "pausa": 3},
        # ── Fila de Comunicação ──
        {"passo": 19, "acao": "navegar", "url": f"{BASE_URL}/communication-queues",
         "legenda": "Abrindo fila de comunicação…", "pausa": 2},
        {"passo": 20, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 21, "acao": "legenda", "texto": "Fila de envio de comunicações: e-mails e mensagens pendentes, enviados e com erro.",
         "pausa": 3},
        # ── Modelos de Comunicação ──
        {"passo": 22, "acao": "navegar", "url": f"{BASE_URL}/communication-templates",
         "legenda": "Abrindo modelos de comunicação…", "pausa": 2},
        {"passo": 23, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 24, "acao": "legenda", "texto": "Templates de e-mail e mensagens para lembretes, confirmações e notificações automáticas.",
         "pausa": 3},
        # ── Lembretes de Vacinas (envio de notificação) ──
        {"passo": 25, "acao": "navegar", "url": f"{BASE_URL}/vaccination-reminders",
         "legenda": "Abrindo lembretes de vacinas…", "pausa": 2},
        {"passo": 26, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 27, "acao": "legenda", "texto": "Lembretes automáticos de vacinação com envio de notificação para o tutor.",
         "pausa": 3},
        # ── Tutor — Histórico de Comunicação ──
        {"passo": 28, "acao": "navegar", "url": f"{BASE_URL}/tutors",
         "legenda": "Abrindo lista de tutores…", "pausa": 2},
        {"passo": 29, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 30, "acao": "clicar", "seletor": "a[href*='/communication']",
         "legenda": "Clicando no histórico de comunicação do primeiro tutor…", "pausa": 3},
        {"passo": 31, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 32, "acao": "legenda", "texto": "Histórico de todas as comunicações enviadas ao tutor (e-mails, WhatsApp, notas).",
         "pausa": 3},
        # ── Final ──
        {"passo": 33, "acao": "legenda", "texto": "Módulo de comunicação demonstrado com sucesso!",
         "pausa": 4},
        *logout(),
    ],
}

ROTEIRO_13_AGENDA_EQUIPE = {
    "nome": "Agenda da Equipe",
    "arquivo": "13-agenda-equipe",
    "credenciais": {"email": "super@vet.com", "senha": "super123"},
    "passos": [
        {"passo": 0, "acao": "shell",
         "comando": "php artisan treinamento:cleanup --module=13-agenda-equipe",
         "legenda": "Limpando dados de execuções anteriores…", "pausa": 2},
        *login("super@vet.com", "super123"),
        # ── Lista de Escalas ──
        {"passo": 5, "acao": "navegar", "url": f"{BASE_URL}/staff-schedules",
         "legenda": "Abrindo escalas de trabalho…", "pausa": 2},
        {"passo": 6, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 7, "acao": "legenda", "texto": "Lista de escalas de trabalho registradas, com data, profissional e unidade.",
         "pausa": 3},
        # ── Criar nova escala ──
        {"passo": 8, "acao": "clicar", "seletor": "a[href*='staff-schedules/create']",
         "legenda": "Abrindo formulário de nova escala…", "pausa": 2},
        {"passo": 9, "acao": "esperar", "seletor": ".card",
         "legenda": "Aguardando formulário…", "pausa": 2},
        {"passo": 10, "acao": "tom_select", "wire_model": "user_id",
         "valor": "Dr. Roberto",
         "legenda": "Selecionando funcionário…", "pausa": 2},
        {"passo": 11, "acao": "tom_select", "wire_model": "branch_id",
         "valor": "Matriz",
         "legenda": "Selecionando unidade…", "pausa": 2},
        {"passo": 12, "acao": "preencher", "seletor": "input[name=work_date]", "valor": "2026-07-25",
         "legenda": "Preenchendo data…", "pausa": 1},
        {"passo": 13, "acao": "preencher", "seletor": "input[name=start_time]", "valor": "08:00",
         "legenda": "Preenchendo horário de início…", "pausa": 1},
        {"passo": 14, "acao": "preencher", "seletor": "input[name=end_time]", "valor": "18:00",
         "legenda": "Preenchendo horário de término…", "pausa": 1},
        {"passo": 15, "acao": "preencher", "seletor": "select[name=shift_type]", "valor": "regular",
         "legenda": "Selecionando tipo de turno…", "pausa": 1},
        {"passo": 16, "acao": "clicar", "seletor": "button[type=submit]",
         "legenda": "Salvando escala…", "pausa": 4},
        {"passo": 17, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando confirmação…", "pausa": 1},
        {"passo": 18, "acao": "legenda", "texto": "Escala criada com sucesso para Dr. Roberto na Matriz.",
         "pausa": 3},
        # ── Calendário de Plantão ──
        {"passo": 19, "acao": "navegar", "url": f"{BASE_URL}/staff-schedules/on-call-calendar",
         "legenda": "Abrindo calendário de plantões…", "pausa": 2},
        {"passo": 20, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 21, "acao": "legenda", "texto": "Visualização em calendário dos plantões do mês, com indicadores por tipo (presencial, sobreaviso, telefone).",
         "pausa": 3},
        # ── Plantões Veterinários ──
        {"passo": 22, "acao": "navegar", "url": f"{BASE_URL}/staff-schedules/vet-shifts",
         "legenda": "Abrindo plantões veterinários…", "pausa": 2},
        {"passo": 23, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 24, "acao": "legenda", "texto": "Turnos veterinários disponíveis para agendamento online pelo portal do tutor.",
         "pausa": 3},
        # ── Solicitações de Folga ──
        {"passo": 25, "acao": "navegar", "url": f"{BASE_URL}/staff-time-off",
         "legenda": "Abrindo solicitações de folga…", "pausa": 2},
        {"passo": 26, "acao": "esperar", "seletor": ".content-wrapper",
         "legenda": "Aguardando página…", "pausa": 1},
        {"passo": 27, "acao": "legenda", "texto": "Solicitações de folga dos colaboradores: férias, licença médica e folgas pessoais.",
         "pausa": 3},
        # ── Criar solicitação de folga ──
        {"passo": 28, "acao": "clicar", "seletor": "button[data-target='#modalTimeOff']",
         "legenda": "Abrindo formulário de nova solicitação…", "pausa": 2},
        {"passo": 29, "acao": "esperar", "seletor": "#modalTimeOff",
         "legenda": "Aguardando modal…", "pausa": 2},
        {"passo": 30, "acao": "legenda", "texto": "Formulário de solicitação com seleção de funcionário, tipo, datas e motivo.",
         "pausa": 3},
        {"passo": 31, "acao": "clicar", "seletor": "#modalTimeOff .btn-secondary, #modalTimeOff button[data-dismiss='modal']",
         "legenda": "Fechando modal sem salvar…", "pausa": 1},
        # ── Final ──
        {"passo": 32, "acao": "legenda", "texto": "Módulo de agenda da equipe demonstrado com sucesso!",
         "pausa": 4},
        *logout(),
    ],
}

# ── Catálogo de roteiros ─────────────────────────────────────────────────────
CATALOGO = {
    "05-portal-tutor": ROTEIRO_05_PORTAL_TUTOR,
    "07-farmacia": ROTEIRO_07_FARMACIA,
    "08-admin-config": ROTEIRO_08_ADMIN_CONFIG,
    "10-agendamento": ROTEIRO_10_AGENDAMENTO,
    "12-comunicacao": ROTEIRO_12_COMUNICACAO,
    "13-agenda-equipe": ROTEIRO_13_AGENDA_EQUIPE,
    "11-tutores-pets": ROTEIRO_11_TUTORES_PETS,
    "01-prontuarios": ROTEIRO_01_PRONTUARIOS,
    "09-financeiro": ROTEIRO_09_FINANCEIRO,
}

def listar_modulos():
    print("Módulos disponíveis:")
    for chave, valor in sorted(CATALOGO.items()):
        print(f"  {chave} — {valor['nome']}")
    print(f"\nTotal: {len(CATALOGO)} módulos")
