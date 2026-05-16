# VetEssence — Sistema de Gerenciamento Veterinário

**VetEssence** é um sistema completo de gestão para clínicas veterinárias, no modelo SaaS. Atende prontuário clínico, farmácia, financeiro, conformidade regulatória (ANVISA, LGPD, CFMV) e fluxos diários de trabalho — acessível via web, mobile e API.

Construído com **Laravel 8**, **AdminLTE 3.2**, **Livewire 2**, **Spatie Permissions**, **MySQL**, **Tailwind CSS** e **Alpine.js**. Interface em português brasileiro, código em inglês.

---

## Funcionalidades

### Clínico
- **Prontuários** — Registros SOAP com diagnóstico, anamnese, plano terapêutico
- **Prescrições** — Receitas digitais com medicamento, dosagem, duração; verificação via QR code
- **Vacinas** — Agendamento por protocolo, multi-dose, certificado PDF (layout CFMV)
- **Cirurgias** — Agendamento, monitoramento anestésico, avaliação pré-anestésica
- **Internações** — Registros diários, evolução clínica, resumo de alta
- **Laboratório** — Pedidos de exame, rastreamento de amostras, lançamento de resultados
- **Imagem** — Pedidos de raio-X, ultrassom, tomografia com laudos
- **Odontologia** — Ficha odontológica e registro de procedimentos
- **Teleconsultas** — Agendamento e anotações de consultas remotas
- **Controle de Peso** — Acompanhamento de peso/crescimento ao longo do tempo
- **Controle Parasitário** — Vermifugação e tratamento de ectoparasitas agendados
- **Interações Medicamentosas** — Ferramenta de verificação cruzada de conflitos

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
- **Faturas** — Faturamento de serviços/produtos com controle de pagamentos
- **Pagamentos** — Multi-forma (dinheiro, cartão, PIX via gateway), parcelamentos
- **Conciliação Bancária** — Importação de extrato e correspondência automática
- **Comissões** — Cálculo de comissão por procedimento ou produto por veterinário/funcionário
- **Guias de Convênio** — Faturamento de convênios, comando de envio automático, webhook
- **Relatórios Financeiros** — Receita, contas a receber, formas de pagamento

### Regulatório & Compliance
- **ANVISA** — Controle de substâncias controladas, receituário, rastreamento de lotes
- **LGPD** — Termos de consentimento, políticas de retenção, auditoria de privacidade
- **CFMV** — Atestados sanitários conforme Res. 974/2006 (CVI), certificados de vacina, bloco de assinatura digital
- **Verificação de Prescrição** — Hash SHA-256 via URL pública (`/r/{hash}`), com limite de taxa
- **Trilha de Auditoria** — Todas as alterações registradas com usuário, data, IP

### Pets
- **Cadastro de Tutores e Pets** — Registro global compartilhado entre unidades
- **Atestados Sanitários** — Modelos CVI com selo CRMV, campos de transporte/destino
- **Lembretes de Vacina** — E-mail/SMS automáticos para doses futuras
- **Registro de Óbito** — Causa mortis, flag de necropsia
- **Microchip** — Registro e consulta de chip de identificação
- **Hospedagem / Banho e Tosa** — Hotel, banho & tosa com check-in/check-out

### Comunicação
- **WhatsApp/SMS** — Integração Z-API para WhatsApp, fallback SMS, canal configurável por modelo
- **Chat Interno** — Chat em tempo real com Livewire entre equipe, indicador de não lido
- **Notas Internas** — Recados para a equipe da clínica
- **Fila de Comunicação** — Processamento em lote via comando Artisan
- **Notificações** — Acompanhamento de entrega de todas as mensagens enviadas

### Mobile
- **Layout Responsivo** — Navegação inferior (Início, Triagem, Receitas, Prontuários), visões simplificadas para veterinários em campo

### Administração
- **Usuários** — Controle de acesso por papel (10 perfis), permissões CRUD
- **Unidades** — Suporte multi-unidade com dados escopados por filial
- **Categorias** — Classificação de serviços/produtos
- **Modelos de Termos** — Termos de consentimento reutilizáveis
- **Modelos de Comunicação** — Mensagens pré-definidas por canal
- **Backup** — Backup automatizado com retenção configurável

---

## Stack Tecnológica

| Camada | Tecnologia |
|--------|-----------|
| Backend | Laravel 8, PHP 8.x |
| Frontend | AdminLTE 3.2, Tailwind CSS, Alpine.js |
| Componentes | Livewire 2, FullCalendar 6, Chart.js |
| Banco | MySQL |
| Autenticação | Laravel Breeze, Spatie Permissions |
| PDF | Dompdf (barryvdh/laravel-dompdf) |
| QR Code | endroid/qr-code |
| Testes | PHPUnit, DatabaseTransactions |

---

## Suite de Testes

```
Tests: 293 Unit + 385 Feature = 678 total (0 failures)
```

---

## Início Rápido

```bash
cp .env.example .env
composer install
npm install && npm run dev
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

---

## Plano de Build

O plano detalhado de construção, fases e status de cada módulo está em [PLAN.md](./PLAN.md).

---

## Regra de Atualização

Este README é mantido em **português brasileiro**. Ao fazer commit:
1. Atualize apenas a linha `Tests:` com o número atual de testes do `PLAN.md`
2. **NÃO** sobrescreva este arquivo com `cp PLAN.md README.md`
3. Mantenha o formato, seção de funcionalidades e idioma
