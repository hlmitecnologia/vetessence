# VetEssence — Sistema Completo para Clínicas Veterinárias (Laravel + Multi-filiais + ANVISA/CFMV)

**VetEssence** é um sistema completo de gestão para clínicas veterinárias, no modelo SaaS. Atende prontuário clínico, farmácia, financeiro, conformidade regulatória (ANVISA, LGPD, CFMV) e fluxos diários de trabalho — acessível via web, mobile e API.

Construído com **Laravel 13**, **AdminLTE 3.2**, **Livewire 3**, **Spatie Permissions**, **MySQL**, **Tailwind CSS** e **Alpine.js**. Interface em português brasileiro, código em inglês.

---

## Funcionalidades

### Clínico
- **Prontuários** — Registros SOAP com diagnóstico, anamnese, plano terapêutico, aprovação de orçamento pelo tutor
- **Sugestão de Diagnóstico por IA** — Botão "Sugerir (IA)" no prontuário que envia sinais clínicos, histórico de atendimentos, vacinações, tratamento e medicações em andamento para provedores LLM (OpenAI, Anthropic, Gemini, Grok, Ollama), pré-preenche o diagnóstico e sugere ajustes no tratamento. Detecta e exibe mensagem amigável quando o limite de tokens é excedido.
- **Prescrições** — Receitas digitais com medicamento, dosagem, duração; verificação via QR code
- **Vacinas** — Aplicação por protocolo, multi-dose, certificado PDF (layout CFMV), previsão de vencimento, recall
- **Cirurgias** — Agendamento, checklist pré-operatório, monitoramento anestésico
- **Avaliação Pré-Anestésica** — Classificação ASA, exames, jejum, checklist completo
- **Internações** — Registros diários, evolução clínica, prescrição diária, resumo de alta
- **Laboratório** — Pedidos de exame, rastreamento de amostras, lançamento de resultados, equipamentos integrados
- **Imagem** — Pedidos de raio-X, ultrassom, tomografia, laudos com assinatura digital
- **Odontologia** — Odontograma interativo, procedimentos, classificação periodontal
- **Dietas Prescritas** — Dietas renais, hepáticas, urinárias, com marca, quantidade, duração
- **Teleconsultas** — Agendamento e anotações de consultas remotas via Jitsi
- **Controle de Peso** — Acompanhamento de peso/crescimento ao longo do tempo
- **Controle Parasitário** — Vermifugação e tratamento de ectoparasitas agendados
- **Interações Medicamentosas** — Ferramenta de verificação cruzada de conflitos
- **Termos de Consentimento** — Templates configuráveis por tipo de procedimento, assinatura digital

### Triagem & Emergência
- **Painel de Triagem** — Livewire em tempo real com cores de gravidade (vermelho/laranja/amarelo/verde), polling a cada 5s, alerta sonoro
- **Protocolos de Emergência** — Modelos pré-configurados para atendimento urgente

### Agenda
- **Calendário Visual** — FullCalendar 6 com visões dia/semana/mês, arrastar e soltar, codificado por cor por veterinário/procedimento
- **Consultas** — CRUD com integração de agendamento online
- **Escala da Equipe** — Turnos de veterinários/recepcionistas e plantão

### Farmácia & Estoque
- **Produtos** — Catálogo completo com SKU, código de barras, preço custo/venda, rastreamento por lote
- **Movimentações de Estoque** — Entrada/saída/ajuste/transferência, alertas de estoque baixo
- **Pedidos de Compra** — Fluxo de compras: rascunho → pedido → recebimento → conciliação
- **Substâncias Controladas** — Rastreamento conforme ANVISA com registro de uso
- **Fornecedores** — Gestão de fornecedores e histórico de pedidos

### Financeiro
- **Faturas** — Faturamento de serviços/produtos com controle de pagamentos e cancelamento
- **Auto-Faturamento** — Geração automática de fatura ao concluir consulta (com fallback via mapeamento tipo→serviço)
- **Serviços** — Cadastro de serviços com preço base e preços por espécie/porte
- **Mapeamento Tipo→Serviço** — Associa cada tipo de atendimento a um serviço com preço para faturas de prontuário
- **NFSe** — Nota Fiscal de Serviços Eletrônica com suporte a múltiplos provedores (Webmania®, FocusNFe, Spedy, Tecnospeed, NFE.io), dados fiscais por unidade, emissão manual ou automática ao faturar, cancelamento, exportação XML/PDF
- **Pagamentos** — Multi-forma (dinheiro, cartão, PIX via gateway), parcelamentos
- **Conciliação Bancária** — Importação de extrato OFX/QIF/CSV, correspondência automática por valor
- **Comissões** — Cálculo de comissão por procedimento ou produto por veterinário, relatório por período
- **Guias de Convênio** — Faturamento de convênios, claims, comando de envio automático, webhook
- **Claims de Convênio** — Registro de solicitações, aprovação/rejeição, auto-envio via API Porto Seguro
- **Relatórios Financeiros** — Receita, contas a receber, DRE, formas de pagamento

### Regulatório & Compliance
- **ANVISA (Portaria 344/98)** — Controle de substâncias controladas, receituário especial, rastreamento lote-a-lote, relatórios mensais/anuais
- **LGPD** — Termos de consentimento, políticas de retenção, direito de exclusão/anonimização, trilha de auditoria
- **CFMV** — Atestados sanitários conforme Res. 974/2006 (CVI), certificados de vacina, bloco de assinatura digital, telemedicina (Res. 1465/2022)
- **Verificação de Prescrição** — Hash SHA-256 via URL pública (`/r/{hash}`), com limite de taxa (10 req/min)
- **Zoonoses** — Cadastro de notificação compulsória (raiva, leptospirose, leishmaniose), relatórios epidemiológicos

### Pets & Tutores
- **Cadastro de Tutores e Pets** — Registro global compartilhado entre unidades, com cascading select de estado/cidade, auto-preenchimento de endereço por CEP (ViaCEP + AwesomeAPI)
- **Microchip / RG Animal** — Número do microchip, data de implantação, registro geral animal (RG)
- **Timeline do Paciente** — Histórico unificado: consultas, vacinas, exames, cirurgias, internações, triagens
- **Atestados Sanitários** — Modelos CVI com selo CRMV, campos de transporte/destino
- **Lembretes de Vacina** — E-mail/SMS/WhatsApp automáticos para doses futuras
- **Registro de Óbito** — Causa mortis, autorização, cremação, memorial
- **Hospedagem / Banho e Tosa** — Hotel, banho & tosa com check-in/check-out, tarefas diárias

### Comunicação
- **WhatsApp/SMS/E-mail** — Notificações multi-canal com painel de configuração por provedor (SMTP/Mailgun/SES/SendGrid, Twilio/Zenvio/SNS, Z-API/Weni/Cloud API/Twilio WhatsApp)
- **Chat Interno** — Chat em tempo real com Livewire entre equipe, indicador de não lido
- **Notas Internas** — Recados para a equipe da clínica
- **Fila de Comunicação** — Processamento em lote via comando Artisan, com agendamento
- **Notificações** — Acompanhamento de entrega de todas as mensagens enviadas

### Calculadora de Dosagem
- **Formulário de Fármacos** — Cadastro por espécie com dosagem (mg/kg), dose máxima, via
- **Cálculo Automático** — Informe peso do animal + fármaco → dose calculada em mg

### Mobile
- **Layout Responsivo** — Interface adaptável (desktop, tablet, celular)
- **Modo Mobile (/m)** — Navegação inferior (Início, Triagem, Receitas, Prontuários), visões simplificadas para veterinários em campo

### Administração
- **Usuários** — Controle de acesso por papel (11 perfis), 160+ permissões CRUD
- **Unidades (Multi-filiais)** — Suporte multi-unidade com dados escopados por filial, dashboard corporativo; endereço com estado/cidade via cascading select e CEP
- **Categorias** — Classificação de serviços/produtos
- **Modelos de Termos** — Termos de consentimento reutilizáveis
- **Modelos de Comunicação** — Mensagens pré-definidas por canal
- **Backup** — Backup automatizado com retenção configurável
- **Auto-Update** — Atualização do sistema via GitHub diretamente do painel admin (git pull + migrate), com autenticação via GitHub PAT configurável
- **Gateways de Pagamento** — Configuração multi-provedor com campos condicionais (Mercado Pago, PagSeguro, Stripe, PIX, Outro), sandbox por gateway
- **PIX** — Gateway PIX nativo com chave PIX e suporte a PIX dinâmico, dados do recebedor obtidos da unidade
- **Rebranding** — Personalização de logo, cores, nome da clínica, posição do nome, fundo do login
- **Dashboard Corporativo** — Indicadores consolidados de todas as filiais (faturamento, agendamentos, ocupação)
- **Documentação do Sistema (/docs)** — Manuais do usuário (25 módulos), manual técnico e manual do tutor, renderizados em Markdown com diagramas BPMN interativos

---

## Stack Tecnológica

| Camada | Tecnologia |
|--------|-----------|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | AdminLTE 3.2, Tailwind CSS, Alpine.js |
| Componentes | Livewire 3, FullCalendar 6, Chart.js, TomSelect 2.3.1 |
| Banco | MySQL |
| Autenticação | Laravel Breeze, Spatie Permissions |
| PDF | Dompdf (barryvdh/laravel-dompdf) |
| QR Code | endroid/qr-code |
| Testes | PHPUnit, DatabaseTransactions |

---

## Suite de Testes

```
Tests: ~1,060 total (261 files, 1,057 methods), assertions vary, pre-existing failures/skipped; +13 new files (LLM provider architecture)
```

## Início Rápido

```bash
cp .env.example .env
composer install
npm install && npm run dev
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Instalação em Produção (Ubuntu/Debian)

```bash
chmod +x install/install.sh
sudo ./install/install.sh
```

O script interativo configura PHP, Nginx, MySQL/MariaDB, Node.js, Redis, Supervisor e Certbot automaticamente. Veja [install/install.sh](install/install.sh).

## Contas de Demonstração

Após rodar `php artisan migrate --seed`, os seguintes usuários estarão disponíveis:

| Perfil | Email | Senha |
|--------|-------|-------|
| Super Admin | `super@vet.com` | `super123` |
| Admin | `admin@vet.com` | `admin123` |
| Veterinário | `vet@vet.com` | `vet123` |
| Veterinário | `vet2@vet.com` | `vet2123` |
| Recepcionista | `recep@vet.com` | `recep123` |
| Recepcionista | `recep2@vet.com` | `recep2123` |
| Financeiro | `financeiro@vet.com` | `fin123` |
| Super Financeiro | `superfin@vet.com` | `superfin123` |
| Estoque | `estoque@vet.com` | `est123` |
| Recursos Humanos | `rh@vet.com` | `rh123` |
| Auditor | `auditor@vet.com` | `auditor123` |
| Tutor (portal) | `tutor@vet.com` | `tutor123` |

---

## Requisitos de Hardware

### Servidor de Demonstração (1–5 usuários simultâneos)

| Componente | Mínimo | Recomendado |
|---|---|---|
| **CPU** | 1 core (x86_64) | 2 cores |
| **RAM** | 2 GB | 4 GB |
| **Armazenamento** | 10 GB SSD | 20 GB SSD |
| **SO** | Ubuntu 22.04+ / Debian 12+ | Ubuntu 24.04 LTS |

**Stack:** PHP 8.2+, Nginx, MySQL 8+ / MariaDB 10.6+, Redis opcional (`QUEUE_CONNECTION=sync` + `CACHE_DRIVER=file` funcionam).

**Consumo estimado:** ~470 MB ocioso, ~1 GB em uso. Código-fonte ~565 MB em disco.

### Produção (50–200 usuários simultâneos)

Para cenário real com filas, cache, sessões e backups, recomenda-se **arquitetura com 2 servidores** separando aplicação e banco:

| Componente | Aplicação (Web + PHP-FPM + Redis) | Banco de Dados (MySQL / MariaDB) |
|---|---|---|
| **CPU** | 4 cores | 4–8 cores |
| **RAM** | 8 GB | 16 GB (ajustar `innodb_buffer_pool_size` para 70–80%) |
| **Armazenamento** | 50 GB SSD (sistema + logs + assets) | 100 GB SSD (dados + binlogs + backups) |
| **SO** | Ubuntu 24.04 LTS | Ubuntu 24.04 LTS |

**Stack de produção obrigatória:**
- **PHP-FPM** — 8–16 workers (`pm.max_children` conforme RAM disponível)
- **Nginx** — com micro-caching estático e compressão Brotli
- **MySQL 8+** — dedicado, com `innodb_buffer_pool_size` configurado
- **Redis** — obrigatório para filas (`QUEUE_CONNECTION=redis`), cache (`CACHE_DRIVER=redis`), sessões (`SESSION_DRIVER=redis`)
- **Supervisor** — para gerenciar workers de fila (recomendar 2–4 workers `queue:work`)
- **Backup** — snapshots diários do banco + storage (scripts Artisan ou cron)
- **Fail2ban** — proteção para SSH e aplicação
- **Certbot / Let's Encrypt** — TLS automático

**Consumo estimado por serviço (produção):**

| Serviço | RAM (carga moderada) |
|---|---|
| Nginx | ~50 MB |
| PHP-FPM (12 workers × 40 MB) | ~480 MB |
| MySQL (buffer pool 6–8 GB) | ~8 GB |
| Redis (cache + filas + sessões) | ~1 GB |
| Supervisor workers (4 × 60 MB) | ~240 MB |
| SO + buffers | ~1 GB |
| **Total (servidor app)** | **~2 GB** |
| **Total (servidor banco)** | **~10 GB** |

> **Nota:** Para ambientes com alta disponibilidade, adicionar um segundo servidor de aplicação atrás de um balanceador de carga (HAProxy / Nginx) e réplica do banco em failover.

---

## Plano de Build

O plano detalhado de construção, fases e status de cada módulo está em [PLAN.md](./PLAN.md).

---

## Atribuições

Este sistema utiliza dados do **Vertebrate Breed Ontology (VBO)**, licenciado sob CC-BY-4.0.
Consulte [NCBITAXONOMY.md](./NCBITAXONOMY.md) para detalhes de atribuição, fontes e dados incorporados.

---

## Regra de Atualização

Este README é mantido em **português brasileiro**. Ao fazer commit:
1. Atualize a linha `Tests:` com o número atual de testes do `PLAN.md`
2. Mantenha a seção de funcionalidades sincronizada com os módulos documentados no `PLAN.md`
3. **NÃO** sobrescreva este arquivo com `cp PLAN.md README.md`
4. Mantenha o formato, seção de funcionalidades e idioma
