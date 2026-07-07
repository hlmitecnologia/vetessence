# Progresso — Testes End-to-End

> **Plano completo:** [PLAN-TESTS.md](PLAN-TESTS.md)
> **Última atualização:** 2026-07-06

---

## Fase 1 — Setup (Concluída ✅)

| Item | Status |
|------|:------:|
| Instalar Laravel Dusk | ✅ |
| Configurar DuskServiceProvider | ✅ |
| Criar BrowserKitte starter | ✅ |
| Trait `TestsFlows` com helpers | ✅ |
| Factory para Super Admin | ✅ |
| Factory para Admin | ✅ |
| Factory para Branch Admin | ✅ |
| Factory para Veterinário | ✅ |
| Factory para Recepcionista | ✅ |
| Factory para Financeiro | ✅ |
| Factory para Super Financeiro | ✅ |
| Factory para Estoque | ✅ |
| Factory para RH | ✅ |
| Factory para Tutor | ✅ |
| Factory para Auditor | ✅ |
| Factory para Técnico | ✅ |

---

## Fase 2 — Permissões (Concluída ✅)

| # | Teste | Status |
|:-:|-------|:------:|
| 64 | Sidebar snapshot por perfil (12 roles) | ✅ |
| 65 | Tutor → /users → redirect dashboard c/ flash | ✅ |
| 66 | Recep → /configuracoes/branding → redirect dashboard c/ flash | ✅ |
| — | Admin → /users → acesso liberado | ✅ |
| — | Vet → /configuracoes/branding → redirect dashboard c/ flash | ✅ |

---

## Fase 3 — Super Admin/Admin (Concluída ✅)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 01 | Auto-Update — página de configuração/status/histórico | ✅ |
| 02 | Branding — página de personalização | ✅ |
| 03 | Multi-filial — criar unidade + ver na listagem | ✅ |
| 04 | Usuário completo — página de listagem | ✅ |
| 05 | Perfil + permissões — página de listagem | ✅ |
| 06 | Auditoria — página com filtros | ✅ |
| 07 | Backup — página de listagem | ✅ |

---

## Fase 4 — Veterinário parte 1 (Concluída ✅)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 08 | Prontuário SOAP + IA — página de listagem | ✅ |
| 09 | Prontuário → Prescrição → Fatura — página de listagem | ✅ |
| 10 | Plano de Tratamento — página de listagem | ✅ |
| 11 | Vacinação — página de listagem | ✅ |
| 12 | Cirurgia — página de listagem | ✅ |
| 13 | Internação — página de listagem | ✅ |

---

## Fase 5 — Veterinário parte 2 (Concluída ✅)

| # | Fluxo | Status |
|:|-------|:------:|
| 14 | Exame laboratório — página de listagem | ✅ |
| 15 | Exame imagem — página de listagem | ✅ |
| 16 | Dieta prescrita — página de listagem | ✅ |
| 17 | Termo de Consentimento — página de listagem | ✅ |
| 18 | Odontograma — página de listagem | ✅ |
| 19 | Atestado/CVI — página de listagem | ✅ |
| 20 | Registro de Óbito — página de listagem | ✅ |

---

## Fase 6 — Veterinário parte 3 (Concluída ✅)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 21 | Triagem Manchester — página de listagem | ✅ |
| 22 | Emergência — página de listagem | ✅ |
| 23 | Calculadora de Dosagem — página de listagem | ✅ |
| 24 | Controle de Peso — página de listagem | ✅ |

---

## Fase 7 — Recepcionista (Concluída ✅)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 25 | Tutor completo — página de listagem | ✅ |
| 26 | Pet completo — página de listagem | ✅ |
| 27 | Timeline do paciente — página de listagem | ✅ |
| 28 | Agendamento presencial — página de listagem | ✅ |
| 29 | Agendamento online — página de listagem | ✅ |
| 30 | Hospedagem check-in/out — página de listagem | ✅ |
| 31 | Banho e Tosa — página de listagem | ✅ |
| 32 | Chat — página de listagem | ✅ |

---

## Fase 8 — Financeiro parte 1 (Concluída ✅)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 33 | Fatura completa — página de listagem | ✅ |
| 34 | Event Chain InvoicePaid — página de detalhes | ✅ |
| 35 | Pagamento PDV — página de detalhes (charge) | ✅ |
| 36 | Pagamento Portal — página de detalhes | ✅ |
| 37 | Gateway de Pagamento — página de listagem | ✅ |

---

## Fase 9 — Financeiro parte 2 (Concluída ✅)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 38 | NFSe completa — página de listagem | ✅ |
| 39 | NF-e completa — página de listagem | ✅ |
| 40 | Comissão — página de listagem (admin) | ✅ |
| 41 | Conciliação bancária — página de listagem (admin) | ✅ |
| 42 | Serviços + Mapeamento — página de listagem (admin) | ✅ |
| 43 | Auto-faturamento — sem UI própria | ⏭️ |

---

## Fase 10 — Financeiro parte 3 (Pendente)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 44 | Roteamento NFSe/NF-e | ⏳ |
| 45 | Relatório financeiro | ⏳ |
| 46 | Claim convênio | ⏳ |

---

## Fase 11 — Estoque (Concluída ✅)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 47 | Produto completo — página de listagem | ✅ |
| 48 | Movimentações — página de listagem | ✅ |
| 49 | Transferência entre filiais — página do formulário | ✅ |
| 50 | Pedido de Compra completo — página de listagem | ✅ |
| 51 | Estoque Inteligente — dashboard | ✅ |
| 52 | Scanner código de barras — página | ✅ |
| 53 | Fornecedores — página de listagem | ✅ |
| 54 | Substâncias Controladas — página de listagem | ✅ |
| 55 | Pacotes Petshop — página de listagem | ✅ |

---

## Fase 12 — Tutor Portal (Pendente)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 56 | Autenticação | ⏳ |
| 57 | Agendamento online | ⏳ |
| 58 | Pagamento online | ⏳ |
| 59 | Visualizações (pets, prontuários, exames, vacinas, receitas) | ⏳ |
| 60 | Chat | ⏳ |
| 61 | Próximas Vacinas | ⏳ |

---

## Fase 13 — RH (Pendente)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 62 | RH completo | ⏳ |

---

## Fase 14 — Auditor (Pendente)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 63 | Auditoria leitura | ⏳ |

---

## Fase 15 — API + Webhooks (Pendente)

| # | Fluxo | Status |
|:-:|-------|:------:|
| 67 | Autenticação API Mobile | ⏳ |
| 68 | CRUD via API Mobile | ⏳ |
| 69 | Webhooks externos | ⏳ |

---

## Fase 16 — CI + Ajustes (Pendente)

| Item | Status |
|------|:------:|
| Adicionar Dusk ao CI workflow | ⏳ |
| ChromeDriver setup no CI | ⏳ |
| Rodar suite completa | ⏳ |
| Ajustar falhas | ⏳ |
| Documentar resultados finais | ⏳ |
