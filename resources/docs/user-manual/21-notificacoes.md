# Notificações

## Canais Disponíveis
- **WhatsApp**: Mensagens via API (recomendado)
- **SMS**: Mensagens de texto
- **E-mail**: Envio de e-mails transacionais

## Preferências do Tutor (T10)

### Configurar Preferências
1. Acesse o cadastro do tutor > **Preferências de Notificação**
2. Opções de canal (escolha um ou mais):
   - [ ] Notificações via **WhatsApp**
   - [ ] Notificações via **SMS**
   - [ ] Notificações via **E-mail**
   - [ ] **Não deseja receber notificações**
3. O tutor também pode configurar pelo **Portal do Tutor**
4. O sistema respeita as preferências ao enviar notificações

### Hierarquia de Canais
1. WhatsApp (prioridade máxima)
2. SMS (fallback se WhatsApp falhar)
3. E-mail (último recurso)
- Limite de 2 SMS/dia por tutor
- Após 3 falhas consecutivas, canal é desativado temporariamente

## Tipos de Notificação

### Lembretes de Agendamento
- **24h antes**: Confirmação de consulta/cirurgia
- **2h antes**: Lembrete de horário
- Tutor pode confirmar ou reagendar pelo link

### Vacinas
- **Próxima dose**: Lembrete na data programada
- **Vacinas anuais**: Alerta 30 dias antes do vencimento

### Retornos
- **Pós-cirúrgico**: Lembrete de retorno
- **Exames**: Resultado disponível
- **Tratamento**: Fim do ciclo de medicação

### Campanhas
- **Promocional**: Ofertas e campanhas (opt-in)
- **Recall**: Vacinação em massa
- **Aniversário do Pet**: Saudação automática

## Configuração

### WhatsApp
1. Acesse **Configurações > Integrações > WhatsApp**
2. Configure:
   - **API Key** (provedor: WATI, Twilio, Z-API)
   - **Número remetente**
   - **Template de mensagens**
3. Teste o envio antes de ativar

### SMS
1. Acesse **Configurações > Integrações > SMS**
2. Configure provedor (Twilio, Zenvia, etc.)
3. Defina limite mensal de SMS

### E-mail
1. Configure servidor SMTP em **.env**
2. Templates em `resources/views/emails/`
3. Teste de envio na tela de configurações

## Relatórios
- **Taxa de entrega** por canal
- **Histórico** de notificações enviadas
- **Tentativas falhas** e motivos

## Regras de Negócio
- Lembretes são enviados apenas com consentimento do tutor
- Limite de 2 SMS por dia por tutor
- WhatsApp tem prioridade sobre outros canais
- Falha após 3 tentativas desativa temporariamente o canal
