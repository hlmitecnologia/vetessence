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
- Necessário para realizar auto-update do sistema
- Gere um token seguro em **Configurações > Sistema**
- O token valida requisições de atualização
- Guarde em local seguro

## Integrações

As integrações são configuradas via **arquivo `.env`** na raiz do sistema (acesso via servidor/SSH) ou via painel admin conforme indicado.

### WhatsApp (Z-API)
| Variável | Descrição | Onde configurar |
|----------|-----------|-----------------|
| `WHATSAPP_API_URL` | URL base da API Z-API | `.env` |
| `WHATSAPP_API_TOKEN` | Token de autenticação Bearer | `.env` |
| `WHATSAPP_INSTANCE` | ID da instância Z-API | `.env` |

**Provider**: Z-API ([https://z-api.io](https://z-api.io))
**Uso**: Lembretes de consulta, vacinas, campanhas, notificações em geral.
**Como obter**: Crie uma conta no Z-API, crie uma instância e copie o token.

### SMS
| Variável | Descrição | Onde configurar |
|----------|-----------|-----------------|
| `SMS_API_URL` | URL base da API de SMS | `.env` |
| `SMS_API_KEY` | Chave de API (Bearer token) | `.env` |

**Provider**: Twilio, Zenvia ou similar
**Uso**: Fallback quando WhatsApp não está disponível.

### E-mail Transacional
| Variável | Descrição | Onde configurar |
|----------|-----------|-----------------|
| `EMAIL_API_URL` | URL base da API de e-mail | `.env` |
| `EMAIL_API_TOKEN` | Token de autenticação | `.env` |
| `EMAIL_API_TIMEOUT` | Timeout em segundos (padrão: 15) | `.env` |

**Uso**: Envio de e-mails transacionais (notificações, campanhas).

### SMTP (E-mail Padrão)
| Variável | Descrição | Onde configurar |
|----------|-----------|-----------------|
| `MAIL_MAILER` | Driver (smtp, mailgun, ses, postmark) | `.env` |
| `MAIL_HOST` | Servidor SMTP | `.env` |
| `MAIL_PORT` | Porta SMTP | `.env` |
| `MAIL_USERNAME` | Usuário SMTP | `.env` |
| `MAIL_PASSWORD` | Senha SMTP | `.env` |

**Uso**: Recuperação de senha, e-mails do sistema.

### PIX
| Variável | Descrição | Onde configurar |
|----------|-----------|-----------------|
| `PIX_KEY` | Chave PIX (CPF, CNPJ, e-mail, telefone) | `.env` |
| `PIX_MERCHANT_NAME` | Nome do recebedor (até 25 caracteres) | `.env` |
| `PIX_CITY` | Cidade do recebedor | `.env` |

**Uso**: Geração de QR Code PIX para pagamento de faturas.
**Nota**: O sistema gera o QR Code localmente — não depende de API externa.

### Gateway de Pagamento (Cartão/Boleto)
1. Acesse **Financeiro > Gateways de Pagamento**
2. Clique em **Novo**
3. Configure:
   - **Provedor**: Mercado Pago, PagSeguro, Stripe
   - **Chave pública**
   - **Chave secreta**
   - **Webhook secret** (para notificações)
   - **Modo de teste** (sandbox)
4. Clique em **Salvar**

> **Nota**: O cadastro do gateway está implementado. A chamada real para a SDK do provedor está em desenvolvimento.

### Convênios (Porto Seguro)
| Variável | Descrição | Onde configurar |
|----------|-----------|-----------------|
| `PORTO_SEGURO_API_URL` | URL base da API Porto Seguro | `.env` |
| `PORTO_SEGURO_API_KEY` | Chave de API | `.env` |

**Uso**: Envio automático de claims de convênio.

### Equipamentos de Laboratório
1. Acesse **Configurações > Equipamentos de Laboratório**
2. Clique em **Novo**
3. Configure:
   - **Tipo de equipamento**
   - **Protocolo** (REST, HL7, FHIR)
   - **URL de conexão**
   - **Chave de API**
   - **IP e porta** (para conexão direta)
4. Clique em **Salvar**

**Uso**: Recebimento automático de resultados de exames.

## Personalização

### Identidade Visual
1. Acesse **Configurações > Identidade Visual**
2. Configure:
   - **Nome da clínica** (exibido no título, sidebar e documentos)
   - **Cor primária** (usada na sidebar e botões principais)
   - **Logotipo** (upload PNG, JPG ou SVG)
   - **Favicon** (ícone da aba do navegador)

### Impressão
- **Header**: Texto exibido no topo dos documentos
- **Footer**: Texto exibido no rodapé
- **Logo**: Logo nos documentos

## Auditoria
- Visualize logs de acesso e alterações
- Configure período de retenção
- Exporte logs

## Atualização do Sistema
1. Acesse **Configurações > Atualização do Sistema**
2. Configure o **token de atualização**
3. Clique em **Verificar Atualizações** para checar GitHub
4. Se disponível, clique em **Aplicar Atualização**
5. O sistema:
   - Coloca em manutenção
   - Executa `git pull`
   - Roda migrações
   - Limpa cache
   - Reativa o sistema
6. Histórico de atualizações é mantido com data, versão e status
