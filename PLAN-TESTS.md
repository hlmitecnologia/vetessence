# Plano de Testes End-to-End — VetEssence

> **Referenciado por:** `PLAN.md → Phase ZH`
> **Progresso:** [TESTS-PROGRESS.md](TESTS-PROGRESS.md)

---

## Ferramenta

**Laravel Dusk** — navega como browser real, testando Livewire, modais, redirects, permissões, JavaScript e integrações.

## Escopo

~520 métodos de controller (94 arquivos), 26 módulos + Portal do Tutor + API Mobile, testados como **69 fluxos end-to-end**, cobrindo **12 perfis de usuário**.

---

## Fases de Implementação

| Fase | Descrição | Testes | Status |
|------|-----------|:------:|:------:|
| 1 | Setup Dusk + Factory + Helpers | — | ✅ |
| 2 | Testes de Permissão (sidebar + acesso negado) | 3 | ✅ |
| 3 | Fluxos Super Admin/Admin | 7 | ✅ |
| 4 | Fluxos Veterinário parte 1 (prontuário, prescrição, fatura, plano, vacina, cirurgia) | 6 | ✅ |
| 5 | Fluxos Veterinário parte 2 (internação, exames, dietas, consentimento, odonto, CVI, óbito) | 8 | ✅ |
| 6 | Fluxos Veterinário parte 3 (triagem, emergência, calculadora, peso) | 4 | ✅ |
| 7 | Fluxos Recepcionista (tutor, pet, timeline, agenda, hospedagem, banho, chat) | 8 | ✅ |
| 8 | Fluxos Financeiro parte 1 (fatura, event chain, pagamentos, gateway) | 5 | ✅ |
| 9 | Fluxos Financeiro parte 2 (NFSe, NF-e, comissão, conciliação, serviços) | 5 | ✅ (1 sem UI) |
| 10 | Fluxos Financeiro parte 3 (roteamento NF, relatórios, claims) | 3 | Pendente |
| 11 | Fluxos Estoque (produto, movimentações, pedido compra, estoque inteligente, substâncias, pacotes) | 9 | ✅ |
| 12 | Fluxos Tutor Portal (autenticação, agendamento, pagamento, visualizações, chat, vacinas) | 6 | Pendente |
| 13 | Fluxos RH (departamento, cargo, funcionário, escala, plantão, folga) | 1 | Pendente |
| 14 | Fluxos Auditor | 1 | Pendente |
| 15 | Testes API Mobile + Webhooks | 3 | Pendente |
| 16 | CI + Ajustes finos | — | Pendente |

---

## Matriz de Fluxos por Perfil

### Super Admin / Admin (perfil irrestrito)

| # | Fluxo | Controllers | Etapas |
|---|-------|-------------|-------:|
| 01 | **Auto-Update** — Configurar token → Verificar → Aplicar → Histórico | SystemUpdateController | 5 |
| 02 | **Branding** — Upload logo → Favicon → Cores → Verificar sidebar/login | BrandingController | 4 |
| 03 | **Multi-filial** — Criar filial → Configurar dados fiscais → Ver isolamento | BranchController | 5 |
| 04 | **Usuário completo** — Criar com RH → Atribuir roles → Editar → Desativar | UserController | 6 |
| 05 | **Perfil completo** — Criar → Atribuir 284 permissões → Sincronizar → Excluir | RoleController | 5 |
| 06 | **Auditoria** — Visualizar logs → Filtrar → Exportar | AuditLogController | 3 |
| 07 | **Backup** — Visualizar → Criar → Excluir | (backup views) | 3 |

### Veterinário

| # | Fluxo | Controllers | Etapas |
|---|-------|-------------|-------:|
| 08 | **Prontuário SOAP + IA** — Criar → Preencher → Sugerir IA → Salvar → Anexar | MedicalRecordController | 6 |
| 09 | **Prontuário → Prescrição → Fatura** — Fluxo completo | MedicalRecordController, PrescriptionController, InvoiceController | 7 |
| 10 | **Plano de Tratamento** — Criar → Aprovação tutor → Executar | TreatmentPlanController | 5 |
| 11 | **Vacinação completa** — Aplicar → Certificado CFMV → Protocolo → Lembrete | VaccinationController, VaccineProtocolController, VaccinationReminderController | 6 |
| 12 | **Cirurgia completa** — Agendar → Checklist → Pré-anestésico → Trans → Pós | SurgeryController, PreAnestheticEvaluationController, AnesthesiaMonitoringController | 7 |
| 13 | **Internação completa** — Internar → Evolução → Prescrição → Fluidoterapia → Mapa → Alta | HospitalizationController, HospitalizationDailyRecordController, HospitalizationPrescriptionController, ExecutionMapController | 8 |
| 14 | **Exame laboratório** — Solicitar → Coletar → Processar → Resultado → Liberar | ExamController, LaboratoryOrderController | 6 |
| 15 | **Exame imagem** — Solicitar → Upload → Laudo → Assinar | ExamController, ImagingExamController | 5 |
| 16 | **Dieta prescrita** — Criar → Vincular prontuário | DietPlanController | 3 |
| 17 | **Termo de Consentimento** — Template → Associar → Tutor assina | ConsentFormController, ConsentTemplateController | 5 |
| 18 | **Odontograma** — Condição → Procedimento → Periodontia | DentalChartController | 4 |
| 19 | **Atestado/CVI** — Preencher → Emitir PDF | HealthCertificateController | 3 |
| 20 | **Registro de Óbito** — Causa → Crematório → Memorial | PetDeathRecordController | 4 |
| 21 | **Triagem Manchester** — Classificar → Alterar → Fluxo até alta | TriageRecordController | 5 |
| 22 | **Emergência** — Consultar protocolo | EmergencyProtocolController | 2 |
| 23 | **Calculadora Dosagem** — Fármaco → Calcular | DrugFormularyController | 3 |
| 24 | **Controle de Peso** — Registrar → Gráfico | (peso no PetController) | 2 |

### Recepcionista

| # | Fluxo | Controllers | Etapas |
|---|-------|-------------|-------:|
| 25 | **Tutor completo** — Cadastrar → CEP auto → Preferências → Editar | TutorController | 6 |
| 26 | **Pet completo** — Cadastrar → Múltiplos tutores → Microchip → Alergias | PetController | 6 |
| 27 | **Timeline do paciente** — Visualizar → Filtrar | PatientTimelineController | 3 |
| 28 | **Agendamento presencial** — Calendário → Novo → Drag & drop → Confirmar | AppointmentController | 6 |
| 29 | **Agendamento online** — Tutor solicita → Recepção confirma/rejeita | OnlineBookingController | 4 |
| 30 | **Hospedagem** — Check-in → Tarefas → Check-out → Fatura | BoardingController | 7 |
| 31 | **Banho e Tosa** — Agendar → Template → Pacote | GroomingTemplateController | 3 |
| 32 | **Chat** — Visualizar → Responder → Anexo | (chat view) | 3 |

### Financeiro

| # | Fluxo | Controllers | Etapas |
|---|-------|-------------|-------:|
| 33 | **Fatura completa** — Criar → Itens → Parcelas → Pagar → Cancelar | InvoiceController | 7 |
| 34 | **Event Chain InvoicePaid** — Pagar → NFSe → NF-e → Estoque → Comissão | InvoiceController@pay + listeners | 6 |
| 35 | **Pagamento PDV** — Cobrar → Gateway → Webhook → Atualizar | InvoiceController@charge, PaymentWebhookController | 5 |
| 36 | **Pagamento Portal** — Tutor paga → Webhook → Fatura atualizada | Portal\InvoiceController@checkout, PaymentWebhookController | 5 |
| 37 | **Gateway de Pagamento** — Configurar MP/PagSeguro/Stripe/PIX → Canal → Ativar | PaymentGatewayController | 5 |
| 38 | **NFSe completa** — Configurar → Emitir → XML/PDF → Cancelar → Exportar | NfseController, NfseConfigController | 7 |
| 39 | **NF-e completa** — Configurar → Emitir → DANFE → Cancelar | NfeController, NfeConfigController | 6 |
| 40 | **Comissão** — Configurar taxa → Gerar → Financeiro paga | CommissionController | 5 |
| 41 | **Conciliação bancária** — Importar OFX → Sugerir → Conciliar → Desfazer | BankReconciliationController, BankAccountController | 6 |
| 42 | **Serviços + Mapeamento** — Criar serviço → Preço → Mapear tipo→serviço | ServiceController | 5 |
| 43 | **Auto-faturamento** — Consulta concluída → Fatura gerada automaticamente | AppointmentController (evento) | 4 |
| 44 | **Roteamento NFSe/NF-e** — Fatura mista → Serviço→NFSe, Produto→NF-e + baixa | InvoiceController@pay | 4 |
| 45 | **Relatório financeiro** — DRE → Contas receber → Fluxo caixa → Exportar | ReportController | 5 |
| 46 | **Claim convênio** — Criar → Enviar → Webhook → Atualizar | ConvenioClaimController | 5 |

### Estoque

| # | Fluxo | Controllers | Etapas |
|---|-------|-------------|-------:|
| 47 | **Produto completo** — Cadastrar → SKU → Barras → Lotes → Preços → NCM/CFOP | ProductController | 7 |
| 48 | **Movimentações** — Entrada → Saída → Ajuste → Perda → Devolução → Saldo | StockController | 7 |
| 49 | **Transferência entre filiais** — Produto/qtd → Origem/destino → Auditoria | StockController@transfer | 5 |
| 50 | **Pedido de Compra completo** — Draft → Confirmar → Aprovar → Receber parcial → Total | PurchaseOrderController | 8 |
| 51 | **Estoque Inteligente** — Dashboard → Sugestão reposição → Consumo médio | StockController@reorderSuggestions | 4 |
| 52 | **Scanner código de barras** — Abrir → Câmera → Produto encontrado | (scanner view) | 3 |
| 53 | **Fornecedores** — Cadastrar → Histórico → Editar | SupplierController | 4 |
| 54 | **Substâncias Controladas** — Produto controlado → Lote-a-lote → Relatório ANVISA | ControlledSubstanceController | 5 |
| 55 | **Pacotes Petshop** — Pacote → Assinar → Consumir → Economia → Renovar | PetShopPackageController, PetShopSubscriptionController, PetShopConsumptionController | 7 |

### Tutor (Portal)

| # | Fluxo | Controllers | Etapas |
|---|-------|-------------|-------:|
| 56 | **Autenticação** — Registrar → Login → Recuperar senha → Dashboard | Portal\Auth\*, DashboardController | 4 |
| 57 | **Agendamento online** — Pet → Disponibilidade → Veterinário → Confirmar | Portal\AppointmentController, VetAvailabilityController | 5 |
| 58 | **Pagamento online** — Faturas → Pagar gateway → Confirmar | Portal\InvoiceController | 4 |
| 59 | **Visualizações** — Pets → Prontuários → Exames → Vacinas → Receitas | Portal\PetController, MedicalRecordController, ExamController, PrescriptionController, VaccinationController | 6 |
| 60 | **Chat** — Enviar mensagem → Anexar | (chat view) | 3 |
| 61 | **Próximas Vacinas** — Widget → Calendário → Certificado | Portal\VaccinationController | 4 |

### RH

| # | Fluxo | Controllers | Etapas |
|---|-------|-------------|-------:|
| 62 | **RH completo** — Departamento → Cargo → Funcionário (dados RH) → Escala → Plantão → Folga | DepartmentController, PositionController, EmployeeController, StaffScheduleController, StaffTimeOffController | 8 |

### Auditor

| # | Fluxo | Controllers | Etapas |
|---|-------|-------------|-------:|
| 63 | **Auditoria leitura** — Todos módulos sem escrita | (todos .view) | 5 |

### Testes de Permissão

| # | Teste | Descrição |
|---|-------|-----------|
| 64 | **Sidebar por perfil** — Snapshot do menu para cada role | 12 asserts |
| 65 | **Acesso negado** — Tutor tenta /admin → 403 | 1 |
| 66 | **Acesso negado** — Recep tenta /config → 403 | 1 |

### API Mobile + Webhooks

| # | Fluxo | Controllers | Etapas |
|---|-------|-------------|-------:|
| 67 | **Autenticação API** — Login → Token → Refresh → Logout | Api\AuthController | 4 |
| 68 | **CRUD via API** — Tutor → Pet → Appointment → Vaccination → Invoice | Api\*Controller | 6 |
| 69 | **Webhooks externos** — Pagamento → NFSe → Lab → Insurance (todos 200) | Api\PaymentWebhookController, NfseWebhookController, LabEquipmentController, InsuranceWebhookController | 4 |

---

## Resumo

| Métrica | Valor |
|---------|:-----:|
| Fluxos end-to-end | 69 |
| Etapas totais | ~330 |
| Controllers envolvidos | ~80 |
| Perfis de usuário | 12 |
| Testes de permissão | 3 |
| Testes de API + webhook | 3 |
| **Total testes Dusk** | **~75** |
| **Tempo execução (paralelo)** | **3–5 min** |

---

## Estrutura de Arquivos

```
tests/
├── Browser/
│   ├── Flows/
│   │   ├── AdminFlowTest.php          # Fluxos 01–07
│   │   ├── VeterinarioFlowTest.php    # Fluxos 08–24
│   │   ├── RecepcionistaFlowTest.php  # Fluxos 25–32
│   │   ├── FinanceiroFlowTest.php     # Fluxos 33–46
│   │   ├── EstoqueFlowTest.php        # Fluxos 47–55
│   │   ├── TutorPortalFlowTest.php    # Fluxos 56–61
│   │   ├── RhFlowTest.php             # Fluxo 62
│   │   └── AuditorFlowTest.php        # Fluxo 63
│   ├── Permissions/
│   │   └── SidebarPermissionTest.php  # Fluxos 64–66
│   └── Api/
│       └── MobileApiFlowTest.php      # Fluxos 67–69
```

---

## Dependências

- MySQL 8+ para banco de testes
- ChromeDriver para Dusk
- `APP_URL` configurado para `http://localhost:8000`
- CI: `laravel/dusk` incluso, ChromeDriver baixado no setup
