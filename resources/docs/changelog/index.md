# Changelog

## [Não versionado] — 2026-05-28

### Adicionado
- Cancelamento de faturas (botão na listagem e no detalhe, status `cancelled`)
- Mapeamento Tipo de Atendimento → Serviço (`service_type_maps`)
- Método `cancel()` no `InvoiceController`

### Corrigido
- `@push('scripts')` sem tag `<script>` em 29 views causando `is not defined`
- Save do `MedicalRecordForm` — `vet_id` alterado para `user_id`
- Dashboard: script Chart.js carregado como stylesheet; `@stack('scripts')` dentro de tag `<script>`; gráfico consultando `Appointment` em vez de `MedicalRecord`
- Preview de foto no PetForm: `onerror="this.remove()"` para evitar 404
- Listagem de pets: dimensão fixa via `style` em vez de classes Tailwind
- DataTables: `columns` explícito para evitar erro TN18 com `colspan`
- NFSe: `InvoicePaid::dispatch()` adicionado no `pay()`; `use App\Models\NfseConfig` adicionado ao controller
- Rota `invoices.pay` registrada em `web.php`

## Versão 1.0.0 — 2026-05

### Adicionado

#### Infraestrutura (Fases A–G)
- Schema completo com migrations para todos os módulos
- 11 papéis (roles) com 160+ permissões Spatie
- Middleware `SetBranchContext` + escopo global `BranchScope`
- Módulos RH: Departamentos, Cargos, Funcionários, Escalas de Plantão
- Papel `super-financial` com acesso financeiro global
- Gates e controllers auditados com Spatie permissions

#### Testes (Fases H–M)
- ~335 testes unitários de modelos (90 arquivos)
- ~313 testes de feature de controllers (53 controllers)
- 15 testes de comandos Artisan
- 34 testes de serviços (Pix, Email, BranchContext)
- 10 testes de traits (Auditable, BranchScoped, HasPhoto)
- 12 testes de integração (fluxos completos)
- Schema–model mismatches corrigidos (40+ colunas em 10 tabelas via 8 migrations)

#### Conformidade Regulatória (Fase N)
- ANVISA: substâncias controladas, receituário especial, relatórios mensais/anuais
- LGPD: consentimento, exportação, anonimização, política de retenção
- CFMV: prontuário digital, prescrição com verificação QR, telemedicina, CVI

#### Features Clínicas (Fases P–Q)
- Eutanásia/cremação (`pet_death_records`)
- Avaliação pré-anestésica (ASA score, checklist)
- Dietas prescritas (renais, hepáticas, etc.)
- Claims de convênio (envio automático via API Porto Seguro)
- CVI (Certificado Veterinário Internacional)
- Triagem ER (painel colorido Manchester, status flow)
- Lote/validade em produtos com alerta de vencimento
- Fluxo de aprovação de orçamento (Treatment Plan)
- Microchip / RG Animal
- Auto-faturamento pós-consulta
- Comissões de veterinários
- Prescrição eletrônica validável (QR + hash SHA-256)
- Conciliação bancária (importação OFX/QIF/CSV)

#### Workflow Diário (Fase S)
- Calendário visual FullCalendar 6 com drag-and-drop
- Dashboard com KPIs (receita, agendamentos, triagem, estoque)
- Chat interno em tempo real
- Certificado de vacina PDF (layout CFMV)
- Integração WhatsApp/SMS/E-mail com provedores(Z-API, Twilio, Mailgun, etc.)
- Modo mobile `/m` com navegação inferior
- Pedidos de compra (solicitação → aprovação → recebimento)

#### Cobertura 100% (Fase T)
- Timeline do paciente (eventos consolidados)
- Dedução automática de estoque
- Calculadora de dosagem
- Lembrete automático de consultas
- Portal do tutor completo (prontuários, exames, prescrições)
- Previsão de vacinas a vencer
- Scanner de código de barras/QR
- Tabela de preços por espécie/porte
- Transferência de estoque entre filiais
- Preferências de notificação do tutor
- Protocolos de emergência
- Dashboard corporativo multi-unidade

#### Manutenção & Governança (Fase U)
- Auto-update via GitHub (painel admin)
- Rebranding (logo, cores, nome da clínica)
- Documentação do sistema (/docs) com 25 manuais + manual técnico

#### Modal CRUD (Fase V)
- 29 Livewire form components em modais Bootstrap
- 27 index views convertidas para modal
- Delete com SweetAlert2 global

#### NFSe — Nota Fiscal Eletrônica (Fase W)
- 5 provedores: Webmania®, FocusNFe, Spedy, Tecnospeed, NFE.io
- Adapter Pattern com interface única
- Emissão manual e automática ao faturar
- Cancelamento (prazo 24h), exportação XML, PDF
- Dados fiscais por filial
- Permissões: `nfse.view`, `nfse.emit`, `nfse.cancel`, `nfse-config.edit`

### Corrigido
- `User.php:hasRole()` — tratamento de parâmetro Spatie Collection
- `StaffNote.php` — cast `branch_id` como `datetime` (corrigido)
- `Category.php` — FKs apontando para `branch_id` em vez de `id`
- `MedicalRecord.php` — `vet_id` (coluna é `user_id`)
- `StockMovement.php` — fillable com colunas inexistentes
- `ConvenioPet.php` — fillable com colunas inexistentes
- `CommunicationQueue.php` — nome da tabela incorreto
- `HospitalizationFluidTherapy.php` — nome da tabela incorreto
- 6 controllers sem passar variáveis necessárias para as views
- 9 rotas incorretas (ordem, validação, parâmetros faltantes)
