# Manual Técnico

Documentação para desenvolvedores e administradores do sistema.

---

## Arquitetura

### Stack
| Camada | Tecnologia |
|--------|-----------|
| Backend | Laravel 8, PHP 7.4 |
| Frontend | AdminLTE 3.2, Tailwind CSS, Alpine.js |
| Componentes | Livewire 2, FullCalendar 6, Chart.js |
| Banco | MySQL |
| Autenticação | Laravel Breeze, Spatie Permissions |
| PDF | Dompdf (barryvdh/laravel-dompdf) |
| QR Code | endroid/qr-code |

### Estrutura de Diretórios
```
app/
├─ Console/Commands/     # Comandos Artisan
├─ Events/               # Eventos do sistema
├─ Exceptions/           # Exceções customizadas
├─ Http/
│  ├─ Controllers/      # Controladores
│  │  └─ Portal/        # Portal do tutor
│  ├─ Livewire/         # Componentes Livewire
│  └─ Middleware/       # Middlewares
├─ Listeners/           # Listeners de eventos
├─ Models/              # Eloquent Models
├─ Providers/           # Service Providers
└─ Services/            # Classes de serviço
resources/
├─ views/
│  ├─ layouts/          # Layouts (adminlte, sidebar, mobile)
│  ├─ portal/           # Views do portal do tutor
│  └─ ...               # Views por módulo
routes/
├─ web.php              # Rotas principais
├─ portal.php           # Rotas do portal do tutor
├─ api.php              # Rotas de API
└─ console.php          # Rotas de console
```

### Escopo de Dados
- **Tutores e Pets**: Globais (compartilhados entre filiais)
- **Dados Operacionais**: Escopados por filial (branch_id)
- **Usuários**: Possuem branch_id (null = global)

---

## Módulos

### Fases Implementadas

| Fase | Descrição | Status |
|------|-----------|--------|
| A-G | Infraestrutura (schema, roles, middleware) | ✅ |
| H-K | RH (departamentos, cargos, funcionários, escalas) | ✅ |
| L-N | Clínico (prontuários, prescrições, vacinas, exames) | ✅ |
| O-P | Farmácia (produtos, estoque, lotes, substâncias controladas) | ✅ |
| Q | Gaps reais (aprovação, comissões, auto-invoice, Rx verification) | ✅ |
| R | Enhancement (Livewire triage, CVI PDF, auto-claim, QR Rx) | ✅ |
| S | Workflow diário (calendário, dashboard, chat, mobile, ordens de compra) | ✅ |
| T | Cobertura 100% (timeline, dosage calculator, portal tutor, price tiers, emergency protocols, corporate dashboard) | ✅ |
| U | Manutenção (auto-update, rebranding, docs) | Em andamento |

---

## Permissões

O sistema utiliza **Spatie Laravel Permission** com 10 papéis:

| Papel | Descrição |
|-------|-----------|
| super-admin | Acesso total |
| branch-admin | Administração por filial |
| veterinarian | Acesso clínico |
| receptionist | Agenda e cadastro |
| financial | Financeiro |
| super-financial | Financeiro global |
| stock-manager | Estoque |
| human-resources | RH |
| tutor | Portal do tutor |
| auditor | Apenas leitura |

As permissões seguem o padrão `modulo.acaO` (ex: `appointments.create`, `products.view`).

---

## Deploy

### Pré-requisitos
- PHP 7.4+
- MySQL 5.7+
- Composer
- Node.js (para assets)
- Git

### Passos
```bash
git clone https://github.com/hectordufau/vetessence.git
cd vetessence
cp .env.example .env
composer install --no-dev
npm install && npm run production
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### Manutenção
```bash
php artisan down  # Modo manutenção
php artisan up    # Reativar
```

### Auto-Update
Admin pode atualizar via painel em **Configurações > Atualizar Sistema**.
Requer token GitHub configurado.

---

## Variáveis de Ambiente

| Variável | Descrição |
|----------|-----------|
| `APP_NAME` | Nome do sistema |
| `DB_HOST` | Host do banco |
| `DB_PORT` | Porta do banco |
| `DB_DATABASE` | Nome do banco |
| `DB_USERNAME` | Usuário do banco |
| `DB_PASSWORD` | Senha do banco |
| `Z_API_TOKEN` | Token Z-API para WhatsApp |
| `SMS_PROVIDER` | Provedor de SMS |
| `SMS_API_KEY` | Chave da API de SMS |
| `GITHUB_TOKEN` | Token para auto-update |
| `SESSION_DRIVER` | Driver de sessão (file, database, redis) |
| `QUEUE_CONNECTION` | Driver de fila (sync, database, redis) |
