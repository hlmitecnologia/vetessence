# Prescrições

## Emitir Receita
1. Acesse **Clínico > Prescrições**
2. Clique em **Nova Prescrição**
3. Selecione o **pet** e o **prontuário** vinculado
4. Adicione os medicamentos:
   - **Medicamento** (selecionar do formulário ou digitar)
   - **Dosagem**
   - **Frequência** (ex: 8/8h, 12/12h)
   - **Duração** (dias)
   - **Via de administração**
   - **Quantidade total**
5. Adicione observações e orientações
6. Clique em **Salvar**

## Impressão
1. Acesse a prescrição
2. Clique em **Imprimir**
3. O sistema gera PDF formatado com:
   - Cabeçalho da clínica
   - Dados do pet e tutor
   - Medicamentos prescritos
   - Data e assinatura do veterinário
   - QR code para verificação

## Verificação por QR Code
- Cada receita possui um **hash único**
- O hash é codificado em QR Code no PDF
- Escaneie o QR code ou acesse `/r/{hash}`
- A página pública exibe:
  - Dados da prescrição
  - Validade
  - Status (válida/cancelada)

## Regras de Negócio
- Prescrições de substâncias controladas seguem regras ANVISA
- A validade da receita é configurável por tipo de medicamento
- O QR code de verificação é público (rate-limited: 10 req/min)
- Apenas veterinários podem emitir prescrições
