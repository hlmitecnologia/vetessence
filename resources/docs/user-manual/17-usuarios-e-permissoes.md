# Usuários e Permissões

## Gerenciamento de Usuários

### Criar Usuário
1. Acesse **Configurações > Usuários**
2. Clique em **Novo**
3. Preencha:
   - **Nome** e **E-mail** (obrigatórios)
   - **Senha** (mínimo 8 caracteres)
   - **Telefone**
   - **CRMV** (se veterinário)
   - **Filial** (home branch, ou global para administradores)
   - **Cargo**
4. Atribua **funções** (roles):
   - **Admin**: Acesso total ao sistema
   - **Branch Admin**: Administração por filial
   - **Veterinário**: Acesso clínico completo
   - **Secretária**: Agenda e cadastros
   - **Financeiro**: Módulo financeiro
   - **Estoque**: Farmácia e estoque
   - **Tutor**: Portal do tutor
5. Defina **permissões** específicas (opcional)
6. Clique em **Salvar**

### Editar Usuário
- Altere dados cadastrais
- Altere funções e permissões
- **Reenvie convite** por e-mail
- Desative/ative o usuário

### Notificações do Usuário
- Configure preferências de notificação
- Ativar/desativar notificações por e-mail
- Definir dias de lembrete de aniversário de pets

## Funções (Roles)
- **Admin**: Acesso irrestrito, auditoria, configurações globais
- **Branch Admin**: Gerencia filial, usuários da filial, relatórios
- **Veterinário**: Prontuários, prescrições, exames, cirurgias
- **Secretária**: Tutores, pets, agendamento, caixa
- **Financeiro**: Contas, fluxo de caixa, convênios, notas
- **Estoque**: Produtos, movimentações, pedidos, farmácia

## Permissões

### Por Módulo
- **Tutores/Pets**: view, create, edit, delete
- **Prontuários**: view, create, edit, delete, print
- **Prescrições**: view, create, edit, delete, print
- **Vacinas**: view, create, edit, delete, certificate
- **Exames**: view, request, launch-result, print
- **Cirurgias**: view, create, edit, delete
- **Internações**: view, create, edit, discharge
- **Agenda**: view, create, edit, delete, confirm
- **Estoque**: view, create, edit, delete, transfer, adjust
- **Financeiro**: view, create, edit, delete, reconcile, refund

### Permissões Especiais
- **Auditoria**: view-audit-logs
- **Relatórios**: view-reports, export-reports
- **Configurações**: manage-settings
- **Sistema**: system-update, docs-view
- **Dashboard Corporativo**: corporate-dashboard.view

### Gerenciar Permissões
1. Acesse **Configurações > Permissões**
2. Visualize lista de todas as permissões
3. Associe permissões a funções ou usuários específicos
4. Permissões concedidas individualmente sobressaem as da função

## Regras de Negócio
- Apenas Admin pode criar/editar outros Admins
- Usuários inativos não podem acessar o sistema
- Permissões são verificadas em cada ação (gates + middleware)
- Histórico de alterações de permissões é auditado
