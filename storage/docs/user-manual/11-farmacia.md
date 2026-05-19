# Farmácia

## Produtos

### Cadastro de Produto
1. Acesse **Estoque > Produtos**
2. Clique em **Novo**
3. Preencha:
   - **Nome** (obrigatório)
   - **SKU** (código interno)
   - **Código de barras**
   - **Categoria**
   - **Fabricante** / fornecedor padrão
   - **Preço de custo**
   - **Preço de venda**
   - **Unidade** (comprimido, ml, g, frasco)
   - **Estoque mínimo** (alerta)
   - **Controlado?** (substância controlada ANVISA)
4. Clique em **Salvar**

### Lotes e Validade
- Informe **lote** e **data de validade** ao receber produtos
- O sistema alerta produtos próximos ao vencimento
- Produtos vencidos são bloqueados para venda/uso

### Preços por Espécie
- Produtos podem ter preços diferenciados por espécie/porte
- Configure em **Estoque > Produtos > Editar > Preços por Espécie**

## Fornecedores
- Cadastre fornecedores com dados de contato
- Associe produtos ao fornecedor padrão
- Histórico de pedidos por fornecedor

## Categorias
- Classifique produtos por categoria (Medicamentos, Insumos, Rações, etc.)
- Categorias são configuradas em **Configurações > Categorias**

## Calculadora de Dosagem
1. Acesse o **Formulário de Fármacos** no menu Clínico
2. Selecione **espécie** e **fármaco**
3. Informe o **peso do animal**
4. O sistema calcula a dose em mg
5. Respeita a **dose máxima** configurada

## Regras de Negócio
- Produtos controlados exigem receituário ANVISA
- Preço de venda não pode ser menor que o de custo (alerta)
- Estoque mínimo gera notificação
- Lotes vencidos não podem ser utilizados
