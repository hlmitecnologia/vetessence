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
   - **Endereço**: CEP (auto-preenchimento via ViaCEP), logradouro, número, bairro, **cidade e estado com cascading select**
   - **Telefone**
   - **E-mail**
   - **Responsável**
   - **Dados Fiscais (NFSe)**:
     - **Código IBGE do município** (necessário para emissão de NFSe)
     - **Regime tributário**: MEI, Simples Nacional, Lucro Presumido
     - **Série da nota** (ex: 1, 2, ÚNICA)
   - **Ativa?**
4. Clique em **Salvar**

### Configurações da Filial
- Horário de funcionamento
- Dias de atendimento
- Espécies atendidas
- Impressão (logo, header, footer)
- Preços e taxas específicos

## Dashboard Corporativo

1. Acesse **Dashboard Corporativo** (permissão: `corporate-dashboard.view`)
2. Visualize indicadores consolidados de todas as filiais:
   - **Agendamentos**: Total do dia/semana/mês por filial
   - **Receita**: Faturamento total e por filial (gráfico Chart.js)
   - **Pets ativos**: Total de pets cadastrados por filial
   - **Faturamento mensal**: Gráfico comparativo mês a mês
   - **Top 5 filiais**: Ranking por faturamento
   - **Ocupação**: Taxa de ocupação de leitos por filial (se aplicável)
   - **Vacinas**: Aplicações realizadas por filial
3. Filtre por **período** (mês, trimestre, ano)
4. Apenas super-admin, admin, super-financial e branch-admin têm acesso

## Usuários por Filial
- Cada usuário tem uma **filial principal** (home branch) no campo `branch_id`
- Usuários com `branch_id = null` têm **acesso global** (super-admin, auditor, HR)
- Usuários Branch Admin gerenciam apenas sua filial
- Relatórios podem ser filtrados por filial
- Usuários só veem dados operacionais da própria filial (exceto acesso global)

## Transferência entre Filiais
- Estoque pode ser transferido entre filiais
- O movimento cria saída na origem e entrada no destino
- Histórico completo de transferências

## Regras de Negócio
- Cadastro de tutores e pets é global (compartilhado entre filiais)
- Financeiro é segregado por filial
- Usuários só veem dados da própria filial (exceto admin)
- Cada filial tem seu próprio plano de contas financeiro
