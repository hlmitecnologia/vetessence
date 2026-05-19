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
