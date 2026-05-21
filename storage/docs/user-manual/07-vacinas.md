# Vacinas

## Aplicar Vacina
1. Acesse **Clínico > Vacinas**
2. Clique em **Nova Vacina**
3. Preencha:
   - **Pet** (selecionar)
   - **Vacina** (selecionar do protocolo ou digitar)
   - **Lote** e **validade**
   - **Fabricante**
   - **Veterinário** responsável
   - **Data da aplicação**
   - **Próxima dose** (se houver)
4. Se houver estoque integrado, o sistema deduz automaticamente
5. Clique em **Salvar**

## Protocolos de Vacinação
- Acesse **Clínico > Protocolos de Vacinação**
- Crie protocolos por espécie (ex: filhote canino, felino adulto)
- Defina as vacinas, idades e intervalos
- Ao aplicar, o sistema sugere a próxima dose conforme o protocolo

## Certificado de Vacina (PDF)
1. Acesse a vacina aplicada
2. Clique em **Certificado**
3. O PDF é gerado com layout padrão CFMV contendo:
   - Dados do animal (nome, espécie, raça, cor)
   - Dados do tutor
   - Vacina, lote, fabricante, data
   - Assinatura e número do CRMV do veterinário

## Lembretes Automáticos
- O sistema envia lembretes de vacinas futuras:
  - **Próxima dose**: Quando chegar a data programada
  - **Vacinas anuais**: Lembrete 30 dias antes do vencimento
- Canais: WhatsApp, SMS, E-mail (conforme preferência do tutor)
- Comando `vaccines:remind` dispara lembretes automaticamente

## Previsão de Vacinas a Vencer
1. Acesse **Clínico > Previsão de Vacinas**
2. Filtre por **espécie** e **dias para vencer**
3. Visualize lista de vacinas próximas ao vencimento
4. Use para campanhas de recall
5. Exporte a lista para CSV

## Campanhas de Recall
1. Acesse **Clínico > Campanhas de Recall**
2. Selecione o **tipo de vacina** e **período**
3. O sistema lista todos os pets com vacinas atrasadas
4. Clique em **Enviar Lembrete** para disparar notificações em massa
5. Comando `recall:process` executa campanhas automaticamente

## Protocolos de Vacinação
1. Acesse **Clínico > Protocolos de Vacinação**
2. Crie protocolos por **espécie** (ex: filhote canino, felino adulto)
3. Defina as vacinas, idades e intervalos entre doses
4. Ao aplicar, o sistema sugere a **próxima dose** conforme o protocolo
5. Configure **lembretes automáticos** baseados no protocolo

## Vacinação Remota (Lembretes Programados)
- Configure regras de lembrete por vacina
- Defina **dias de antecedência** para alerta
- Escolha **canais** de notificação (WhatsApp, SMS, E-mail)
- Lembretes são processados pelo comando `vaccines:remind`

## Regras de Negócio
- Vacinas múltiplas podem ter intervalo mínimo entre doses
- O certificado é um documento legal (CFMV)
- Vacinas de raiva e outras obrigatórias têm notificação especial
