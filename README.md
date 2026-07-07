<div align="center">

# 🐾 VetEssence

### Sistema completo de gestão para clínicas veterinárias

**Gratuito • Open Source • Self-Hosted**

[![Licença](https://img.shields.io/badge/licença-MIT-green.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![GitHub stars](https://img.shields.io/github/stars/hlmitecnologia/vetessence?style=social)](https://github.com/hlmitecnologia/vetessence)
[![GitHub issues](https://img.shields.io/github/issues/hlmitecnologia/vetessence)](https://github.com/hlmitecnologia/vetessence/issues)
[![Testes](https://github.com/hlmitecnologia/vetessence/actions/workflows/tests.yml/badge.svg)](https://github.com/hlmitecnologia/vetessence/actions/workflows/tests.yml)

---

[Demo](https://demo.vetessence.com.br) •
[Documentação](docs/README.md) •
[Instalação](#instalação-rápida) •
[Suporte](#suporte-profissional)

</div>

---

## Sobre

O **VetEssence** é um sistema ERP completo para clínicas veterinárias, construído sobre **Laravel 13** com foco em **prontuário clínico**, **farmácia**, **financeiro**, **conformidade regulatória** (ANVISA, LGPD, CFMV) e **multi-filiais** — tudo em um único código aberto, gratuito e auto-hospedado.

> ⚠️ **O software é gratuito.** Suporte, instalação, hospedagem e customizações são serviços pagos pela [HLMI Tecnologia](https://vetessence.com.br). Ao utilizar serviços pagos, você concorda com nossos [termos de serviço](https://vetessence.com.br/termos).

---

## Funcionalidades

### Clínico
- **Prontuários** — Registros SOAP com diagnóstico, anamnese, plano terapêutico, aprovação de orçamento pelo tutor
- **Sugestão de Diagnóstico por IA** — Botão "Sugerir (IA)" no prontuário que envia sinais clínicos, histórico de atendimentos, vacinações, tratamento e medicações em andamento para provedores LLM (OpenAI, Anthropic, Gemini, Grok, Ollama), pré-preenche o diagnóstico e sugere ajustes no tratamento
- **Prescrições** — Receitas digitais com medicamento, dosagem, duração; verificação via QR code
- **Vacinas** — Aplicação por protocolo, multi-dose, certificado PDF (layout CFMV), previsão de vencimento, recall
- **Cirurgias** — Agendamento, checklist pré-operatório, monitoramento anestésico
- **Avaliação Pré-Anestésica** — Classificação ASA, exames, jejum, checklist completo
- **Internações** — Registros diários, evolução clínica, prescrição diária, resumo de alta
- **Mapa de Execução** — Cronograma visual de tarefas por dia de internação, com geração automática a partir de prescrições e perfil Técnico exclusivo para execução
- **Laboratório** — Pedidos de exame, rastreamento de amostras, lançamento de resultados
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
- **Disponibilidade em Tempo Real** — Portal do Tutor mostra veterinários com turno e slots livres

### Farmácia & Estoque
- **Produtos** — Catálogo completo com SKU, código de barras, preço custo/venda, rastreamento por lote
- **Movimentações de Estoque** — Entrada/saída/ajuste/transferência, alertas de estoque baixo
- **Estoque Inteligente** — Dashboard com sugestão de reposição baseada em consumo médio + lead time + estoque de segurança, alerta de vencimentos
- **Pedidos de Compra** — Fluxo de compras: rascunho → pedido → recebimento → conciliação
- **Substâncias Controladas** — Rastreamento conforme ANVISA com registro de uso
- **Fornecedores** — Gestão de fornecedores e histórico de pedidos
- **Pacotes Petshop** — Pacotes de banho & tosa/hotel com assinaturas e consumo

### Financeiro
- **Faturas** — Faturamento de serviços/produtos com controle de pagamentos
- **Auto-Faturamento** — Geração automática de fatura ao concluir consulta
- **NFSe** — Nota Fiscal de Serviços Eletrônica com múltiplos provedores, emissão manual ou automática
- **Pagamentos** — Multi-forma (dinheiro, cartão, PIX via gateway), parcelamentos
- **Conciliação Bancária** — Importação de extrato OFX/QIF/CSV, correspondência automática
- **Comissões** — Cálculo de comissão por procedimento/produto por veterinário
- **Guias de Convênio** — Faturamento de convênios, claims, auto-envio via API
- **Relatórios Financeiros** — Receita, contas a receber, DRE, formas de pagamento

### Regulatório & Compliance
- **ANVISA (Portaria 344/98)** — Controle de substâncias controladas, receituário especial, rastreamento lote-a-lote
- **LGPD** — Termos de consentimento, políticas de retenção, direito de exclusão/anonimização, trilha de auditoria
- **CFMV** — Atestados sanitários (Res. 974/2006), certificados de vacina, telemedicina (Res. 1465/2022)
- **Verificação de Prescrição** — Hash SHA-256 via URL pública com limite de taxa
- **Zoonoses** — Cadastro de notificação compulsória, relatórios epidemiológicos

### Pets & Tutores
- **Cadastro Global** — Registro compartilhado entre unidades, auto-preenchimento de endereço por CEP
- **Microchip / RG Animal** — Número do microchip, registro geral animal
- **Timeline do Paciente** — Histórico unificado: consultas, vacinas, exames, cirurgias, internações
- **Lembretes Automáticos** — E-mail/SMS/WhatsApp para doses futuras
- **Registro de Óbito** — Causa mortis, autorização, cremação, memorial
- **Hospedagem / Banho e Tosa** — Hotel, banho & tosa com check-in/check-out

### Comunicação
- **WhatsApp/SMS/E-mail** — Notificações multi-canal com painel de configuração por provedor
- **Chat Interno** — Chat em tempo real com Livewire entre equipe
- **Notas Internas** — Recados para a equipe da clínica
- **Notificações** — Acompanhamento de entrega de todas as mensagens

### Mobile
- **Layout Responsivo** — Interface adaptável (desktop, tablet, celular)
- **Modo Mobile (/m)** — Navegação inferior para veterinários em campo

### Administração
- **Usuários** — Controle de acesso por papel (12 perfis), 160+ permissões CRUD
- **Unidades (Multi-filiais)** — Dados escopados por filial, dashboard corporativo
- **Backup** — Backup automatizado com retenção configurável
- **Auto-Update** — Atualização via GitHub diretamente do painel admin
- **Gateways de Pagamento** — Multi-provedor (Mercado Pago, PagSeguro, Stripe, PIX)
- **Rebranding** — Personalização de logo, cores, nome da clínica
- **Dashboard Corporativo** — Indicadores consolidados de todas as filiais
- **Documentação do Sistema (/docs)** — Manuais do usuário (26 módulos) em Markdown

---

## Stack Tecnológica

| Camada | Tecnologia |
|--------|-----------|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | AdminLTE 3.2, Tailwind CSS, Alpine.js |
| Componentes | Livewire 3, FullCalendar 6, Chart.js, TomSelect |
| Banco | MySQL / MariaDB 10.6+ |
| Autenticação | Laravel Breeze, Spatie Permissions |
| PDF | Dompdf |
| QR Code | endroid/qr-code |
| Pagamentos | Mercado Pago, PagSeguro, Stripe, PIX |
| IA | OpenAI, Anthropic, Gemini, Grok, Ollama |
| Testes | PHPUnit (2.045+ testes), Laravel Dusk (56 testes E2E) |

---

## Demonstração

Acesse [**demo.vetessence.com.br**](https://demo.vetessence.com.br) para testar o sistema.

| Perfil | Email | Senha |
|--------|-------|-------|
| Super Admin | `super@vet.com` | `super123` |
| Admin | `admin@vet.com` | `admin123` |
| Veterinário | `vet@vet.com` | `vet123` |
| Recepcionista | `recep@vet.com` | `recep123` |
| Financeiro | `financeiro@vet.com` | `fin123` |
| Tutor (portal) | `tutor@vet.com` | `tutor123` |

---

## Instalação Rápida

### Docker (recomendado para testes)

```bash
git clone https://github.com/hlmitecnologia/vetessence.git
cd vetessence
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Instalação Manual (Ubuntu 24.04)

```bash
chmod +x install/install.sh
sudo ./install/install.sh
```

O script interativo configura PHP, Nginx, MariaDB, Node.js, Redis, Supervisor e Certbot automaticamente. Consulte [install/install.sh](install/install.sh).

### Servidor de Produção

Para ambientes de produção, veja o **Guia de Performance** em [docs/performance.md](docs/performance.md) com configurações de OPcache, MariaDB, PHP-FPM e filas.

---

## Suporte Profissional

O VetEssence é **gratuito e open source**. Se você precisa de:

- **Instalação e configuração** do servidor
- **Hospedagem gerenciada** (nós cuidamos do servidor)
- **Customizações** e novas funcionalidades
- **Treinamento da equipe**
- **Suporte técnico** prioritário

Entre em contato:

- 🌐 [vetessence.com.br](https://vetessence.com.br)
- 📱 [WhatsApp](https://wa.me/5511998464769)
- 📧 [contato@vetessence.com.br](mailto:contato@vetessence.com.br)

---

## Suite de Testes

```
Tests: 2.045 total (397 arquivos)
├── Livewire: 222 (33 arquivos)
├── Controllers: 780 (109 arquivos)
├── Services: 213 (33 arquivos)
└── Models: 378 (92 arquivos)
```

```bash
php artisan test
```

---

## Licença

Distribuído sob licença **MIT**. Veja [LICENSE](LICENSE) para mais informações.

Copyright (c) 2026 **HLMI Tecnologia**

---

## Agradecimentos

- [Laravel](https://laravel.com)
- [AdminLTE](https://adminlte.io)
- [Livewire](https://livewire.laravel.com)
- [Spatie Permissions](https://spatie.be/docs/laravel-permission)
- [Vertebrate Breed Ontology (VBO)](https://github.com/mcic-science/vertebrate-breed-ontology) — dados de raças licenciados sob CC-BY-4.0
- Todos os [contribuidores](https://github.com/hlmitecnologia/vetessence/graphs/contributors) que dedicam tempo e esforço a este projeto
