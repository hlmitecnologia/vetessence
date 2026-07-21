# Usuários e Permissões

## Gerenciamento de Usuários

### Criar Usuário
1. Acesse **Configurações > Usuários**
2. Clique em **Novo**
3. Preencha os dados gerais:
   - **Nome** e **E-mail** (obrigatórios)
   - **Senha** (mínimo 8 caracteres)
   - **Telefone**
   - **CRMV** (se veterinário)
   - **Filial** (home branch, ou global para administradores)
   - **Cargo**
4. Preencha os dados de RH (exibidos conforme permissão):
   - **Departamento** (ex.: Clínico, Administrativo, Farmácia)
   - **Cargo** (ex.: Veterinário Pleno, Recepcionista)
   - **Data de Admissão**
   - **Tipo de Contrato** (CLT, PJ, Estágio, Autônomo)
5. Atribua **funções** (roles):
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
| **Super Admin** | Acesso total irrestrito | 284 (todas) |
| **Admin** | Acesso total ao sistema | 284 (todas) |
| **Branch Admin** | Administração por filial | 284 (filial) |
| **Veterinário** | Acesso clínico completo | ~115 |
| **Técnico** | Execução de tarefas clínicas (sem prescrição) | ~7 |
| **Recepcionista** | Agenda, cadastros, caixa | ~32 |
| **Financeiro** | Módulo financeiro | ~18 |
| **Super Financeiro** | Financeiro global (multi-filial) | ~24 |
| **Estoque** | Farmácia e estoque | ~30 |
| **Recursos Humanos** | RH (departamentos, cargos, funcionários) | ~12 |
| **Tutor** | Portal do tutor | 4 |
| **Auditor** | Apenas leitura (todos .view) | ~80 |

### Descrição das Funções
- **Super Admin**: Acesso irrestrito, auditoria, configurações globais, auto-update
- **Admin**: Acesso irrestrito ao sistema (mesmo escopo do super-admin)
- **Branch Admin**: Gerencia filial específica, usuários da filial, relatórios locais
- **Veterinário**: Prontuários, prescrições, exames, cirurgias, vacinas, chat, triagem
- **Técnico**: Execução de tarefas (mapa de execução, internações), consulta de tutores/pets, notas de equipe
- **Recepcionista**: Tutores, pets, agendamento, caixa, triagem (criar), chat
- **Financeiro**: Contas, fluxo de caixa, convênios, NFSe (view+emit), comissões (view)
- **Super Financeiro**: Financeiro global, NFSe (view+emit+cancel), corporate dashboard
- **Estoque**: Produtos, movimentações, pedidos, farmácia, substâncias controladas
- **Auditor**: Apenas leitura de todos os módulos

### Criar / Editar Perfil
1. Acesse **Configurações > Perfis**
2. Clique em **Novo** ou clique em **Editar** em um perfil existente
3. Preencha os campos:
   - **Nome**: exibição do perfil (ex.: "Veterinário")
   - **Slug**: identificador único usado no sistema (ex.: `veterinario`). Apenas na criação.
   - **Descrição**: opcional, suporta formatação WYSIWYG
4. Selecione as **permissões** desejadas:
   - As permissões são agrupadas por módulo com **títulos em português**
   - Cada grupo possui um checkbox **"Marcar todos"** para seleção rápida
   - As permissões são exibidas em grid de colunas dentro de cards
5. Clique em **Salvar**
6. O sistema sincroniza automaticamente:
   - O perfil personalizado (`roles` table)
   - O perfil Spatie (`spatie_roles` table) usado por `@can()` e middlewares
   - As permissões na tabela pivot (`role_has_permissions`)

> O slug é usado como `name` do Spatie Role. Ao alterar o nome do perfil, o Spatie Role permanece vinculado pelo slug original.

### Validação no Formulário
- **Nome** e **Slug** são obrigatórios
- **Slug** deve ser único — duplicatas são rejeitadas com mensagem de erro
- Erros de validação aparecem com destaque vermelho (`is-invalid`) abaixo do campo

### Excluir Perfil
- Só é permitido se nenhum usuário estiver vinculado ao perfil
- Remove tanto o perfil customizado quanto o Spatie Role correspondente

## Permissões

O sistema possui **284 permissões** organizadas em **76 grupos** (ex.: Tutores, Prontuários, Estoque, etc.). As permissões seguem o padrão `{modulo}.{acao}`:

### Por Módulo
- **Tutores/Pets**: view, create, edit, delete
- **Usuários**: view, create, edit, delete
- **Prontuários**: view, create, edit, delete
- **Prescrições**: view, create, edit, delete
- **Vacinas**: view, create, edit, delete
- **Exames**: view, create, edit, delete
- **Laboratório**: view, create, edit, delete
- **Imagem**: view, create, edit, delete
- **Cirurgias**: view, create, edit, delete
- **Internações**: view, create, edit, delete
- **Mapas de Execução**: view, manage, execute
- **Anestesia**: view, create, edit, delete
- **Agenda**: view, create, edit, delete
- **Estoque**: view, create, edit, delete, transfer
- **Financeiro**: view, create, edit, delete
- **Faturas**: view, create, edit, delete
- **Pagamentos**: view, create, edit, delete
- **NFSe**: view, emit, cancel
- **NF-e**: view, emit, cancel
- **Config. NFSe**: edit
- **Config. NF-e**: edit
- **Comissões**: view, create, edit, delete
- **Conciliação Bancária**: view, create, edit, delete
- **Pedidos de Compra**: view, create, edit, delete, approve, receive
- **Triagem**: view, create, edit, delete
- **Chat**: view, create, edit, delete
- **Dietas**: view, create, edit, delete
- **Avaliação Pré-Anestésica**: view, create, edit, delete
- **Protocolos de Emergência**: view, create, edit, delete
- **Substâncias Controladas**: view, create, edit, delete
- **Teleconsulta**: view, create, edit, delete
- **Hospedagem**: view, create, edit, delete
- **Sessões de Terapia**: view, create, edit, delete
- **Planos de Tratamento**: view, create, edit, delete
- **Odontologia**: view, create, edit, delete
- **Termos de Consentimento**: view, create, edit, delete
- **Controle de Peso**: view, create, edit, delete
- **Atestados de Saúde**: view, create, edit, delete
- **Vacinação**: view, create, edit, delete
- **Lembretes de Vacina**: view, create, edit, delete
- **Protocolos de Vacina**: view, create, edit, delete
- **Controle de Parasitas**: view, create, edit, delete
- **Doenças Zoonóticas**: view, create, edit, delete
- **Guia de Convênio**: view, create, edit, delete
- **Encaminhamentos**: view, create, edit, delete
- **Registros de Óbito**: view, create, edit, delete
- **Modelos de Tosa**: view, create, edit, delete

### Permissões Especiais
- **Auditoria**: audit-logs.view, audit-logs.delete
- **Backups**: backups.view, backups.create, backups.delete
- **Configurações**: configuracoes.view, configuracoes.branding, configuracoes.llm
- **Sistema**: system-update, docs.view
- **Dashboard Corporativo**: corporate-dashboard.view
- **Branding**: branding
- **Equipamentos de Laboratório**: lab-equipment.view/create/edit/delete
- **Categorias de Produto**: categories.view/create/edit/delete
- **Fornecedores**: suppliers.view/create/edit/delete
- **Plantões**: schedules-on-call.view/create/edit/delete
- **Turnos**: vet-shifts.view/create/edit/delete
- **Escalas**: staff-schedules.view/create/edit/delete
- **Documentos**: docs.view

### Gerenciar Permissões
1. Acesse **Configurações > Perfis**
2. Crie ou edite um perfil para ver todas as permissões agrupadas por módulo
3. Cada grupo é exibido em um card com:
   - **Título em português** no card-header
   - Checkbox **"Marcar todos"** ao lado do título
   - Permissões listadas em grid no card-body
4. Marque/desmarque as permissões desejadas e salve
5. As permissões são sincronizadas com o Spatie Permission e ficam imediatamente ativas

### Sincronização entre Sistemas
O VetEssence utiliza dois sistemas de permissão em paralelo:
- **Custom Role** (`App\Models\Role`) — usado pela diretiva `@role()` no sidebar
- **Spatie Permission** — usado por `@can()`, `Gate::allows()` e middlewares

Ao criar/editar um perfil pela interface, ambos são sincronizados automaticamente. Para sincronização manual (ex.: após importar dados), execute:
```bash
php artisan roles:sync-spatie
```

## Regras de Negócio
- Apenas Admin pode criar/editar outros Admins
- Usuários inativos não podem acessar o sistema
- Permissões são verificadas em cada ação (gates + middleware)
- Permissões concedidas individualmente sobressaem às do perfil
- Novas permissões são adicionadas automaticamente via `PermissionSeeder`
