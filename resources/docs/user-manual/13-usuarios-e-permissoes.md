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

| Função | Descrição | Permissões Aprox. |
|--------|-----------|-------------------|
| **Super Admin** | Acesso total irrestrito | ~160 (todas) |
| **Admin** | Acesso total ao sistema | ~160 (todas) |
| **Branch Admin** | Administração por filial | ~160 (filial) |
| **Veterinário** | Acesso clínico completo | ~105 |
| **Recepcionista** | Agenda, cadastros, caixa | ~22 |
| **Financeiro** | Módulo financeiro | ~14 |
| **Super Financeiro** | Financeiro global (multi-filial) | ~19 |
| **Estoque** | Farmácia e estoque | ~25 |
| **Recursos Humanos** | RH (departamentos, cargos, funcionários) | ~10 |
| **Tutor** | Portal do tutor (sem permissões admin) | 0 |
| **Auditor** | Apenas leitura (todos .view + .delete) | ~80+ |

### Descrição das Funções
- **Super Admin**: Acesso irrestrito, auditoria, configurações globais, auto-update
- **Admin**: Acesso irrestrito ao sistema (mesmo escopo do super-admin)
- **Branch Admin**: Gerencia filial específica, usuários da filial, relatórios locais
- **Veterinário**: Prontuários, prescrições, exames, cirurgias, vacinas, chat, triagem
- **Recepcionista**: Tutores, pets, agendamento, caixa, triagem (criar), chat
- **Financeiro**: Contas, fluxo de caixa, convênios, NFSe (view+emit), comissões (view)
- **Super Financeiro**: Financeiro global, NFSe (view+emit+cancel), corporate dashboard
- **Estoque**: Produtos, movimentações, pedidos, farmácia, substâncias controladas
- **Auditor**: Apenas leitura de todos os módulos + permissão de exclusão

## Permissões

### Por Módulo
- **Tutores/Pets**: view, create, edit, delete
- **Prontuários**: view, create, edit, delete, print
- **Prescrições**: view, create, edit, delete, print
- **Vacinas**: view, create, edit, delete, certificate
- **Exames**: view, request, launch-result, print
- **Laboratório**: view, request, launch-result
- **Imagem**: view, request, launch-result
- **Cirurgias**: view, create, edit, delete
- **Internações**: view, create, edit, discharge
- **Anestesia**: view, create, edit, delete
- **Agenda**: view, create, edit, delete, confirm
- **Estoque**: view, create, edit, delete, transfer, adjust
- **Financeiro**: view, create, edit, delete, reconcile, refund
- **NFSe**: view, emit, cancel
- **NFSe Config**: edit
- **Gateway de Pagamento**: gateway-pagamento.view, gateway-pagamento.create, gateway-pagamento.edit, gateway-pagamento.delete
- **Configurações de Notificação**: notification-config.edit
- **Comissões**: view, pay
- **Conciliação Bancária**: view, reconcile
- **Pedidos de Compra**: view, create, edit, delete, approve, receive
- **Triagem**: view, create, edit, delete
- **Chat**: view, create, edit, delete
- **Dietas**: view, create, edit, delete
- **Avaliação Pré-Anestésica**: view, create, edit, delete
- **Protocolos de Emergência**: view, create, edit, delete
- **Substâncias Controladas**: view, create, edit, delete
- **Claims de Convênio**: view, create, edit, delete
- **Teleconsulta**: view, create

### Permissões Especiais
- **Auditoria**: view-audit-logs
- **Relatórios**: view-reports, export-reports
- **Configurações**: configuracoes.view
- **Sistema**: system-update, docs.view
- **Dashboard Corporativo**: corporate-dashboard.view
- **Branding**: branding
- **Equipamentos de Laboratório**: lab-equipment.view/create/edit/delete
- **Categorias de Produto**: categories.view/create/edit/delete
- **Fornecedores**: suppliers.view/create/edit/delete

### Gerenciar Permissões
1. Acesse **Configurações > Permissões**
2. Visualize lista de todas as permissões (170+)
3. Associe permissões a funções ou usuários específicos
4. Permissões concedidas individualmente sobressaem as da função
5. Novas permissões são adicionadas automaticamente via `PermissionSeeder`

## Regras de Negócio
- Apenas Admin pode criar/editar outros Admins
- Usuários inativos não podem acessar o sistema
- Permissões são verificadas em cada ação (gates + middleware)
- Histórico de alterações de permissões é auditado
