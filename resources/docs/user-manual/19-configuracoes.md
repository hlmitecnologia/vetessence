# Configurações

## Sistema

### Gerais
1. Acesse **Configurações > Sistema**
2. Configure:
   - **Nome da clínica** (principal)
   - **CNPJ** da matriz
   - **Logo** (upload)
   - **Fuso horário**
   - **Moeda** (padrão: BRL)
   - **Idioma** (padrão: pt-BR)
   - **Manutenção**: Ativar/desativar modo de manutenção
   - **Token de atualização**: Para auto-update via Git

### Token de Atualização
- Necessário para realizar auto-update do sistema via GitHub
- Configure em **Configurações > Sistema**
- Armazenado com segurança na tabela `settings`
- O token valida requisições de atualização

## Notificações

### Painel de Configuração
Acesse **Configurações > Notificações** para configurar os provedores de cada canal. As configurações são salvas no banco de dados e gerenciadas pelo painel admin — sem necessidade de editar `.env`.

### E-mail

| Provedor | Campos |
|----------|--------|
| **MailerSend** | API Key (recomendado) |
| **SMTP** | Servidor, porta, usuário, senha, criptografia (TLS/SSL), remetente, nome do remetente |
| **Mailgun** | Domínio, API Key, endpoint (EUA/UE) |
| **Amazon SES** | Access Key, Secret Key, região |
| **SendGrid** | API Key |

**Uso**: Envio de e-mails transacionais (notificações, campanhas, recuperação de senha).

### SMS

| Provedor | Campos |
|----------|--------|
| **Twilio** | Account SID, Auth Token, número remetente |
| **Zenvio** | API Key, número remetente |
| **Amazon SNS** | Access Key, Secret Key, região |

**Uso**: Envio de SMS como canal de notificação (fallback do WhatsApp).

### WhatsApp

| Provedor | Campos |
|----------|--------|
| **Z-API** | URL da API, Token, Instância |
| **Weni** | API Key, Project UUID, número remetente |
| **WhatsApp Cloud API (Meta)** | Access Token, Phone Number ID, Business Account ID |
| **Twilio WhatsApp** | Account SID, Auth Token, número remetente |

**Uso**: Canal prioritário para lembretes, confirmações e campanhas.

### Preferências do Tutor
Cada tutor escolhe os canais que deseja receber (WhatsApp, SMS, E-mail) no cadastro ou pelo Portal do Tutor. O sistema respeita as preferências ao enviar.

## Integrações

As integrações abaixo são configuradas via painel admin.

### Gateway de Pagamento

O gateway **PIX** está sempre disponível. O **Mercado Pago** também pode ser ativado para pagamento online no portal do tutor (cartão de crédito e saldo). PagSeguro, Stripe e Stone estão previstos para próximas versões.

#### Configuração
1. Acesse **Financeiro > Gateways de Pagamento**
2. Clique em **Novo**
3. Configure:
   - **Provedor**: PIX ou Mercado Pago
   - **Canal**: Portal (PDV removido; Mercado Pago suporta apenas portal)
   - **Chave PIX** (CPF, CNPJ, e-mail, telefone ou chave aleatória)
   - **Unidade**: Todas as unidades ou uma específica
4. Marque **Ativo** para habilitar
5. O nome do recebedor e a cidade são obtidos da configuração do sistema

#### Funcionamento
A chave PIX cadastrada é usada para gerar o payload EMV BR Code, exibido como QR Code na fatura para o tutor pagar via app do banco. O pagamento é confirmado manualmente no sistema.

> O PIX não utiliza webhook. O pagamento é registrado manualmente na tela da fatura.

![Fluxo de Pagamento PIX](../diagrams/32-fluxo-pagamento-gateway.svg)

#### Geração do QR Code
1. Acesse uma fatura em aberto
2. Clique em **Gerar QR Code**
3. O tutor escaneia com o app do banco e efetua o pagamento
4. Na clínica, registre o pagamento manualmente em **Registrar Pagamento**

### NFSe / NFC-e / NF-e — Configuração Unificada
1. Acesse **Financeiro > Config. NF** (ou **Conf. Sistema > Config. NF** na sidebar)
2. A tela exibe dois cards lado a lado:
   - **NFS-e** (serviços): configurar provedor de nota fiscal de serviços
   - **NF-e / NFC-e** (produtos): configurar provedor de nota fiscal de produtos
3. Para cada card, configure:
   - **Provedor**: Webmania® ou NFE.io (FocusNFe também disponível para NF-e/NFC-e)
   - **Ambiente**: Homologação (testes) ou Produção
4. Preencha as credenciais conforme o provedor escolhido:
   - **Webmania® NFSe**: Access Token (Bearer)
   - **Webmania® NFe**: Consumer Key, Consumer Secret, Access Token, Access Token Secret
   - **NFE.io**: API Key, Company ID
   - **FocusNFe**: API Token
5. Ative a configuração

> **Dados fiscais por filial**: CNPJ, código IBGE do município, regime tributário e série da nota são configurados no **cadastro da filial** (Configurações > Unidades), não na tela de Config. NF.

### IA Diagnóstica — Sugestão de Diagnóstico por IA
1. Acesse **Configurações > IA Diagnóstica**
2. Ative o recurso com o switch **Ativar sugestão de diagnóstico por IA**
3. Configure o provedor:
   - **Provedor**: OpenAI, Anthropic (Claude), Google Gemini, Grok (xAI), Ollama (Local)
   - **Temperatura**: 0.0 (preciso) a 1.0 (criativo) — recomendado 0.3
   - **Max. Tokens**: limite da resposta (100–4096)
4. Preencha as credenciais conforme o provedor escolhido:
   - **OpenAI**: API Key, modelo (ex: `gpt-4o-mini`)
   - **Anthropic**: API Key, modelo (ex: `claude-3-haiku-20240307`)
   - **Gemini**: API Key, modelo (ex: `gemini-2.0-flash`)
   - **Grok**: API Key, modelo (ex: `grok-1`)
   - **Ollama**: Base URL (ex: `http://localhost:11434`), modelo (ex: `llama3`)
5. Salve a configuração

### Uso no Prontuário
1. Acesse um prontuário (criação ou edição)
2. Preencha os campos: Queixa Principal, Anamnese, Exame Físico e Sinais Vitais
3. Clique no botão **Sugerir (IA)** ao lado do campo Diagnóstico
4. O sistema enviará os dados do paciente + sinais ao provedor configurado e preencherá o diagnóstico com a sugestão
5. O veterinário pode editar, complementar ou ignorar a sugestão

> **Observações**: A sugestão é manual (não automática). O prompt é construído com dados do paciente (espécie, raça, idade, sexo), sinais vitais, queixa principal, anamnese e exame físico. A temperatura baixa (0.3) mantém as sugestões profissionais e determinísticas.

### NFSe / NFC-e / NF-e (Webmania®, NFE.io, FocusNFe)
- **Arquitetura**: Adapter Pattern — interface única para múltiplos provedores
- **Fluxo**: Fatura paga → NFC-e (itens produto) + NFSe (itens serviço) → XML/PDF disponíveis
- **NF-e** (modelo 55): emitida apenas para transferências de estoque entre unidades
- **Permissões**: `nfse.view`, `nfse.emit`, `nfse.cancel`, `nfse-config.edit`

### Convênios (Porto Seguro)
| Variável | Descrição | Onde configurar |
|----------|-----------|-----------------|
| `PORTO_SEGURO_API_URL` | URL base da API Porto Seguro | `.env` |
| `PORTO_SEGURO_API_KEY` | Chave de API | `.env` |

**Uso**: Envio automático de claims de convênio (`claims:auto-file`).

### Equipamentos de Laboratório
1. Acesse **Configurações > Equipamentos de Laboratório**
2. Clique em **Novo**
3. Configure:
   - **Tipo de equipamento**
   - **Protocolo** (REST, HL7, FHIR, Custom)
   - **URL de conexão** (endpoint)
   - **Chave de API**
   - **IP e porta** (para conexão direta HL7)
4. Clique em **Salvar**

**Uso**: Recebimento automático de resultados de exames via webhook `POST /api/v1/lab-equipment/{id}/receive`.
**Status**: Consulta via `GET /api/v1/lab-equipment/{id}/status`.

## Personalização

### Identidade Visual
1. Acesse **Configurações > Personalização** (Super Admin apenas)
2. Configure:

   **Geral:**
   - **Nome da clínica** — exibido no título, sidebar, navbar e documentos
   - **Logotipo** — upload PNG, JPG ou SVG (salvo em `storage/app/public/branding/`)
   - **Favicon** — ícone da aba do navegador

   **Exibição do Nome:**
   - **Exibir nome** — ativar/desativar exibição do nome ao lado do logo
   - **Posição** — escolher entre: acima, abaixo, esquerda ou direita do logo
   - A posição se aplica à sidebar, navbar AdminLTE e tela de login

   **Cores:**
   - **Cor primária** — usada na sidebar, botões e elementos principais
   - **Fundo do login** — cor de fundo da tela de login (AdminLTE e Portal)

   **Ajustes:**
   - **Largura do logo no sidebar** — em pixels (20–200)

3. A personalização afeta:
   - Sidebar (cor de fundo, logo + nome com posição configurável)
   - Navbar AdminLTE (brand link)
   - Tela de login AdminLTE (logo, nome, fundo)
   - Tela de login do Portal do Tutor (logo, nome, fundo, cor primária)
   - Cabeçalhos de documentos (PDF)
4. Permissão necessária: `configuracoes.branding` (Super Admin apenas)

### Impressão
- **Header**: Texto exibido no topo dos documentos
- **Footer**: Texto exibido no rodapé
- **Logo**: Logo nos documentos

## Auditoria
- Visualize logs de acesso e alterações
- Configure período de retenção
- Exporte logs

## Atualização do Sistema (Auto-Update U1)

### Como Funciona
1. Acesse **Configurações > Atualização do Sistema**
2. Configure:
   - **Token de atualização** (token de acesso pessoal do GitHub)
   - **Repositório** (ex: `hectordufau/vetessence`)
   - **Branch** (ex: `main`)
3. Clique em **Verificar Atualizações** para checar GitHub
4. Se disponível, clique em **Aplicar Atualização**

### Fluxo de Atualização
1. Sistema entra em modo de manutenção (`php artisan down`)
2. Executa `git pull` usando o token de acesso
3. Roda migrações pendentes (`php artisan migrate`)
4. Limpa cache (config, route, view)
5. Reativa o sistema (`php artisan up`)
6. Histórico de atualizações é registrado (data, versão, status)

### Segurança
- Permissão `system-update` (super-admin apenas)
- Token armazenado na tabela `settings`
- Sistema faz **backup automático** via mysqldump antes de aplicar a atualização (armazenado em `storage/app/backups/`)
- Merge conflicts podem interromper o processo

### Requisitos
- Servidor com `exec()` habilitado
- Git instalado no servidor
- Permissão de escrita na pasta do projeto
- Token GitHub com permissão de leitura do repositório

---

## Diagrama do Processo

![Auto-Update e Configurações](../diagrams/25-fluxo-autoupdate.svg)
*Clique na imagem para ampliar. Diagrama de Atividades UML com raias — retângulos = atividades, losangos = decisão, setas = fluxo entre atividades, raias = atores.*
