# Convênios

## Cadastro de Convênio
1. Acesse **Financeiro > Convênios**
2. Clique em **Novo**
3. Preencha:
   - **Nome** do convênio (obrigatório)
   - **CNPJ**
   - **Registro ANS**
   - **Contato**: Telefone, e-mail, representante
   - **Endereço**
4. Clique em **Salvar**

## Tabela de Procedimentos

### Cadastrar Procedimento
1. Acesse o convênio
2. Na aba **Procedimentos**, clique em **Adicionar**
3. Selecione:
   - **Tipo**: Consulta, Exame, Cirurgia, Procedimento
   - **Código do procedimento** (TUSS)
   - **Descrição**
   - **Valor do convênio**
   - **Valor coparticipação**
   - **Valor glosa**
4. **Cobertura**: % que o convênio cobre
5. **Exige autorização?**
6. Clique em **Salvar**

### Importar Tabela
- Importe a tabela de procedimentos via CSV/Excel
- O sistema associa automaticamente por código TUSS
- Atualização em lote de valores

## Guias de Atendimento
1. Acesse **Financeiro > Guias**
2. Tipos de guia:
   - **SP/SADT**: Serviços profissionais / Serviços auxiliares de diagnóstico e terapia
   - **Consulta**
   - **Internação**
3. Preencha dados do atendimento
4. Imprima a guia para entrega ao tutor

## Faturamento
1. Acesse **Financeiro > Faturamento de Convênios**
2. Selecione o **convênio** e **período**
3. Selecione os atendimentos a faturar
4. Gere o **lote de faturamento**
5. Imprima **demonstrativo** e **guias**
6. Acompanhe o **status** (enviado, pago, glosado, pendente)

### Glosas
- Registre glosas recebidas
- Recurso de glosa com justificativa
- Relatório de glosas por convênio

## Relatórios
- Faturamento por convênio (mensal)
- Taxa de glosa
- Procedimentos mais realizados por convênio
- Demonstrativo de valores a receber

## Claims de Convênio (Insurance Claims)

### Registrar Claim

1. Acesse **Financeiro > Convênios > Claims**
2. Clique em **Novo Claim**
3. Preencha:
   - **Convênio**
   - **Pet** e **tutor**
   - **Valor solicitado**
   - **Procedimentos** cobertos
   - **Data do atendimento**
4. Clique em **Salvar**

### Acompanhamento

- **Status**: Pendente, Aprovado, Rejeitado
- **Valor aprovado** (pode ser diferente do solicitado)
- **Data de resposta** da operadora
- **Observações** do convênio

### Auto-Envio (Porto Seguro)

1. Configure a API Porto Seguro em **Configurações > Integrações**
2. Comando `claims:auto-file` envia claims pendentes automaticamente
3. O sistema recebe **webhook** com atualização de status via `POST /api/insurance/webhook`

## Certificado Veterinário Internacional (CVI)

### Emitir CVI

1. Acesse **Clínico > Atestados de Saúde**
2. Na aba **CVI**, clique em **Novo CVI**
3. Preencha:
   - **Pet**
   - **Destino**: País, cidade
   - **Meio de transporte**: Aéreo, Marítimo, Terrestre
   - **Data de embarque**
   - **CRMV do emissor**
   - **Checklist de requisitos**: Vacinas, exames, microchip, etc.
4. Clique em **Salvar**

### Requisitos Obrigatórios

- [ ] Microchip implantado e lido
- [ ] Vacina antirrábica em dia
- [ ] Exames sorológicos (se país exigir)
- [ ] Tratamento antiparasitário
- [ ] Atestado de saúde clínica
- [ ] Guia de transporte autorizada

### Validade
- CVI tem validade configurável (padrão: 10 dias para embarque)
- Após vencimento, novo CVI deve ser emitido
- Número do CVI segue padrão CRMV

## Permissões
- `convenio-claims.view/create/edit/delete` — Claims de convênio
- CVI reusa permissões de `health-certificates.*`

## Regras de Negócio
- Cada convênio pode ter tabela própria de valores
- Autorização prévia para procedimentos específicos
- Guias têm numeração sequencial por convênio
- Prazo para envio de faturamento conforme contrato
- Claims podem ser enviados automaticamente via API Porto Seguro
- CVI exige microchip obrigatório (verificação automática)
