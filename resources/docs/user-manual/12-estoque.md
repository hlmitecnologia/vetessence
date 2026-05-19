# Estoque

## Movimentações
- **Entrada**: Compra, devolução, ajuste positivo
- **Saída**: Venda, uso em procedimento, perda, ajuste negativo
- **Transferência**: Entre filiais

### Registrar Entrada
1. Acesse **Estoque > Movimentações**
2. Clique em **Nova Movimentação**
3. Selecione **tipo: Entrada**
4. Escolha o **produto**, informe **quantidade**, **lote**, **validade**
5. Selecione a **filial** de destino
6. Clique em **Salvar**

### Transferência entre Filiais
1. Acesse **Estoque > Transferir**
2. Selecione:
   - **Produto**
   - **Quantidade**
   - **Filial de origem**
   - **Filial de destino**
3. Clique em **Transferir**
4. O sistema cria duas movimentações: saída na origem + entrada no destino

### Ajuste de Estoque
- Utilize para correção de inventário
- Informe o **motivo** do ajuste
- O sistema registra o usuário responsável

## Alerta de Estoque Baixo
- Produtos com quantidade abaixo do mínimo configurado são destacados
- Notificação no dashboard
- Alerta de vencimento para lotes próximos do fim

## Pedidos de Compra

### Fluxo Completo
1. **Rascunho**: Crie o pedido sem impacto no estoque
2. **Pedido**: Confirme o pedido ao fornecedor
3. **Recebimento**: Dê entrada dos produtos no estoque
4. **Conciliação**: Confira valores e quantidades

### Criar Pedido
1. Acesse **Estoque > Pedidos de Compra**
2. Clique em **Novo**
3. Selecione **fornecedor** e **filial**
4. Adicione itens (produto + quantidade + preço)
5. O total é calculado automaticamente
6. Clique em **Salvar**

### Aprovação
- Pedidos acima do limite configurado exigem aprovação
- Admin ou branch-admin pode aprovar
- Pedido aprovado pode ser enviado ao fornecedor

### Recebimento
- Ao receber, informe quantidades reais
- O sistema dá entrada no estoque automaticamente
- Divergências são registradas para conciliação

## Scanner de Código de Barras
1. Acesse **Estoque > Scanner**
2. Aponte a câmera para o código de barras
3. O sistema busca o produto automaticamente
4. Exibe informações: nome, estoque, preço, lote

## Substâncias Controladas (ANVISA)
- Registro obrigatório de entrada e saída
- Relatórios mensais e anuais
- Exportação CSV para envio à ANVISA
- Produtos marcados como "controlado" têm movimentação auditada

## Regras de Negócio
- Transferência cria registro de auditoria completo
- Estoque negativo não é permitido
- Apenas admin e estoque podem fazer ajustes
- Substâncias controladas têm rastreamento lote-a-lote
