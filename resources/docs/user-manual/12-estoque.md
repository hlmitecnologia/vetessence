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

## Lotes e Validade
- Produtos podem ter **lote** e **data de validade** associados
- Ao dar entrada, informe lote e validade por lote
- **Alerta de vencimento**: produtos próximos ao vencimento (30/60/90 dias)
- Comando `products:alert-expiry` notifica sobre lotes próximos ao vencimento
- Lotes vencidos são bloqueados para movimentação de saída

## Alerta de Estoque Baixo
- Produtos com quantidade abaixo do mínimo configurado são destacados
- Notificação no dashboard
- Alerta de vencimento para lotes próximos do fim

## Pedidos de Compra

### Fluxo Completo
1. **Rascunho**: Crie o pedido sem impacto no estoque
2. **Pedido**: Confirme o pedido ao fornecedor
3. **Aprovação**: Pedidos acima do limite exigem aprovação
4. **Recebimento**: Dê entrada dos produtos no estoque
5. **Conciliação**: Confira valores e quantidades

### Criar Pedido
1. Acesse **Estoque > Pedidos de Compra**
2. Clique em **Novo**
3. Selecione **fornecedor** e **filial**
4. Adicione itens (produto + quantidade + preço unitário)
5. O total é calculado automaticamente
6. O sistema gera um número de pedido sequencial automático
7. Clique em **Salvar**

### Status do Pedido
| Status | Descrição |
|--------|-----------|
| **Draft** | Rascunho, sem impacto no estoque |
| **Ordered** | Confirmado ao fornecedor |
| **Partial** | Recebimento parcial |
| **Received** | Totalmente recebido |
| **Cancelled** | Cancelado |

### Aprovação
- Pedidos acima do limite configurado exigem aprovação
- Admin ou branch-admin pode aprovar
- Pedido aprovado pode ser enviado ao fornecedor
- Pedido rejeitado volta para rascunho com justificativa

### Recebimento
1. Acesse o pedido com status **Ordered** ou **Partial**
2. Clique em **Receber**
3. Informe **quantidades recebidas** por item (pode ser parcial)
4. O sistema dá entrada no estoque automaticamente
5. Divergências de quantidade/preço são registradas para conciliação
6. Lotes e validades são informados no recebimento

### Conciliação
- Compare valores pedidos vs recebidos
- Registre ajustes (diferença de preço, desconto, frete)
- Conciliação concluída = pedido finalizado

## Scanner de Código de Barras
1. Acesse **Estoque > Scanner**
2. Aponte a câmera para o código de barras (via html5-qrcode)
3. O sistema busca o produto automaticamente
4. Exibe informações: nome, estoque, preço, lote
5. Útil para conferência de inventário e recebimento de pedidos

## Substâncias Controladas (ANVISA)

### Registro Obrigatório
- Produtos marcados como **"Controlado"** têm movimentação auditada
- Registro de **entrada** (compra, devolução, ajuste)
- Registro de **saída** (venda, uso clínico, perda)
- Rastreamento **lote-a-lote** obrigatório

### Relatórios ANVISA
1. Acesse **Estoque > Substâncias Controladas**
2. Relatórios:
   - **Movimentação mensal**: Entradas e saídas do mês
   - **Balanço anual**: Saldo por produto por ano
   - **Exportação CSV**: Formato ANVISA padronizado
3. Relatório inclui: lote, validade, quantidade, data, responsável

### Regras Específicas
- Produtos controlados só podem ser movimentados por usuários autorizados
- Prescrições de substâncias controladas seguem Portaria 344/98
- Receituário ANVISA azul (A1, A2, A3) ou amarelo (B1, B2)
- Relatórios mensais e anuais para envio à ANVISA

## Transferência entre Filiais

1. Acesse **Estoque > Transferir**
2. Selecione:
   - **Produto**
   - **Quantidade**
   - **Filial de origem**
   - **Filial de destino**
3. Clique em **Transferir**
4. O sistema cria duas movimentações: saída na origem + entrada no destino
5. Permissão específica: `stock.transfer`
6. Transferências têm registro de auditoria completo

## Regras de Negócio
- Transferência cria registro de auditoria completo
- Estoque negativo não é permitido
- Apenas admin, estoque e super-financial podem fazer ajustes
- Substâncias controladas têm rastreamento lote-a-lote
- Transferência só pode ser feita entre filiais do mesmo grupo
