# Prontuários

## Criar Prontuário (SOAP)
1. Acesse **Clínico > Prontuários**
2. Clique em **Novo**
3. Selecione o **pet** e o **veterinário**
4. Preencha os campos SOAP:
   - **S** (Subjetivo): Queixa principal, histórico da doença atual,anamnese
   - **O** (Objetivo): Achados do exame físico (temperatura, FC, FR, mucosas, linfonodos, palpação, ausculta)
   - **A** (Avaliação): Diagnóstico presuntivo ou definitivo, hipóteses diagnósticas, CID veterinário
   - **P** (Plano): Tratamento prescrito, exames complementares solicitados, retorno, orientações
5. Clique em **Salvar**

## Planos de Tratamento
1. Acesse **Clínico > Planos de Tratamento**
2. Defina:
   - **Medicamentos** com dose, frequência, duração
   - **Procedimentos** agendados
   - **Exames** de acompanhamento
   - **Dieta** e cuidados
3. Acompanhe a evolução diária

### Fluxo de Aprovação
1. O veterinário cria o plano com status `pending`
2. Sistema disponibiliza para o tutor via **Portal do Tutor**
3. Tutor pode **aprovar** ou **rejeitar** o orçamento
4. Se rejeitado, veterinário registra **motivo da rejeição** e ajusta
5. Plano aprovado segue para execução
- Notificação é enviada ao veterinário quando tutor aprova/rejeita

## Dietas Prescritas (Diet Plans)

### Prescrever Dieta

1. Acesse **Clínico > Dietas Prescritas**
2. Clique em **Nova Dieta**
3. Preencha:
   - **Pet**
   - **Tipo de dieta**: Renal, Hepática, Urinária, Gastrointestinal, Hipoalergênica, Obesidade, Other
   - **Marca** e **Produto** (ração específica)
   - **Quantidade diária**
   - **Duração** (dias)
   - **Instruções** de preparo e administração
4. Clique em **Salvar**

## Termos de Consentimento

### Criar Termo
1. Acesse **Clínico > Termos de Consentimento**
2. Clique em **Novo Termo**
3. Selecione **tipo**: Cirurgia, Anestesia, Internação, Exame, Procedimento
4. Texto do termo é pré-preenchido com template configurável
5. Tutor assina digitalmente (ou presencialmente com registro)
6. Termo assinado fica vinculado ao prontuário do pet

### Templates
- Configure textos padrão em **Configurações > Templates de Consentimento**
- Crie templates por tipo de procedimento
- Texto inclui variáveis como nome do pet, tutor, procedimento, riscos

## Anexos
- É possível anexar arquivos ao prontuário:
  - Imagens (radiografias, ultrassons)
  - Documentos PDF
  - Resultados de exames
- Os anexos ficam disponíveis no histórico do pet

## Histórico
- Todos os prontuários do pet são listados em ordem cronológica
- A **Timeline do Paciente** unifica prontuários com outros eventos
- Busque por diagnóstico, medicamento ou data

## Regras de Negócio
- Apenas veterinários podem criar/editar prontuários
- O prontuário não pode ser excluído após 24h (auditoria)
- O tutor tem acesso apenas de leitura via portal
- Alterações são registradas na trilha de auditoria
