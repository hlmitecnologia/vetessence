# Multi-Filiais

## Estrutura
O VetEssence suporta múltiplas filiais (unidades) com:
- **Dados globais**: Tutores, pets, convênios, fornecedores, produtos
- **Dados por filial**: Estoque, agendamentos, financeiro, usuários

## Gerenciar Filiais

### Criar Filial
1. Acesse **Configurações > Filiais**
2. Clique em **Nova Filial**
3. Preencha:
   - **Nome** (obrigatório)
   - **CNPJ**
   - **Endereço**: CEP, logradouro, número, bairro, cidade, estado
   - **Telefone**
   - **E-mail**
   - **Responsável**
   - **Ativa?**
4. Clique em **Salvar**

### Configurações da Filial
- Horário de funcionamento
- Dias de atendimento
- Espécies atendidas
- Impressão (logo, header, footer)
- Preços e taxas específicos

## Dashboard Corporativo
- Acesse **Dashboard Corporativo**
- Visualize indicadores de todas as filiais:
  - Total de agendamentos (hoje/semana/mês)
  - Receita total e por filial
  - Pets ativos
  - Faturamento mensal (gráfico Chart.js)
  - Top 5 filiais por faturamento

## Usuários por Filial
- Cada usuário tem uma **filial principal** (home branch)
- Usuários Admin têm acesso global (home branch = null)
- Usuários Branch Admin gerenciam apenas sua filial
- Relatórios podem ser filtrados por filial

## Transferência entre Filiais
- Estoque pode ser transferido entre filiais
- O movimento cria saída na origem e entrada no destino
- Histórico completo de transferências

## Regras de Negócio
- Cadastro de tutores e pets é global (compartilhado entre filiais)
- Financeiro é segregado por filial
- Usuários só veem dados da própria filial (exceto admin)
- Cada filial tem seu próprio plano de contas financeiro
