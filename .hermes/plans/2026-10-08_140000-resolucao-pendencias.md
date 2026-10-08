# Plano de Resolução das Pendências — VetEssence

> **Para o Hermes:** executar somente após aprovação explícita do Product Owner. Usar TDD, diagnóstico baseado em evidências e validação local completa antes de qualquer deploy remoto.

**Objetivo:** concluir a reconciliação legada simulada, eliminar as falhas conhecidas da suíte, ampliar a validação E2E dos fluxos de estoque/Farmácia e preparar uma nova release somente após todos os gates locais serem aprovados.

**Restrição confirmada:** a base local de desenvolvimento e testes continuará sendo `vetessence`, conforme os valores existentes no `.env`. Não criar ou configurar outro banco. Nenhum dado fictício será enviado ao servidor remoto.

**Estado atual conhecido:**

- Branch `main` sincronizada com `origin/main` no commit `23962c9`.
- Correção da coluna `branches.ie` validada: `BranchControllerTest` com 9 testes e 17 assertions.
- O comando `inventory:reconcile-legacy --json` ainda encontra `0 saldo(s) analisado(s)` porque não há candidatos fictícios aplicados.
- O fluxo E2E de Estoque passou com 9 testes e 9 assertions.
- A suíte global ainda não está aprovada; o último inventário conhecido registrou 46 falhas no grupo de controllers.

---

## Gate 0 — Baseline e proteção do estado atual

**Objetivo:** registrar o ponto de partida antes de qualquer nova alteração.

1. Confirmar branch, commit, remote, working tree e migrations aplicadas.
2. Registrar a saída atual dos grupos já aprovados:
   - `BranchControllerTest`;
   - `ReconcileLegacyInventoryTest`;
   - `ProductControllerTest`;
   - `FractionalInventoryServiceTest`;
   - `DeductStockOnPaidTest`;
   - `EstoqueFlowTest`.
3. Executar `git diff --check` e revisar ausência de credenciais em diff, logs e documentação.
4. Não alterar `.env`, `.env.testing` ou apontamento para a base `vetessence`.

**Saída:** relatório de baseline com contagens exatas e working tree limpa.

---

## Fase 1 — Reconciliação legada simulada na base `vetessence`

**Objetivo:** validar o comando com dados fictícios controlados, sem representar inventário real.

### 1.1 Criar fixture local controlada

Criar uma fixture/seed exclusiva para a simulação, com identificadores claramente marcados como teste:

- produto legado com `products.stock > 0`;
- unidade `ml`;
- filial A válida;
- produto sem `inventory_batches`;
- segundo produto sem saldo;
- cenário de filial B para validar isolamento;
- cenário divergente para relatório/quarentena.

A fixture deverá registrar os IDs criados e permitir limpeza explícita ao final. Não usar dados de produção nem alterar saldos reais não relacionados ao teste.

### 1.2 Validar relatório sem persistência

Executar:

```bash
php artisan inventory:reconcile-legacy --json
```

Verificar:

- candidatos identificados;
- quantidade derivada do saldo legado;
- filial correta;
- ausência de escrita no banco;
- JSON válido e relatório de inconsistências.

### 1.3 Validar aplicação em quarentena

Executar `--apply` para o cenário sem confirmação física e verificar:

- criação de `LEGACY-SIN-LOTE`;
- `is_legacy = true`;
- status `quarantined`;
- unidade preservada;
- nenhuma validade, custo ou lote real inventado;
- nenhum saldo duplicado.

### 1.4 Validar confirmação física via CSV

Criar CSV temporário com:

```text
product_id,branch_id,quantity,unit,expiration_date
```

Executar:

```bash
php artisan inventory:reconcile-legacy --confirm-file=/caminho/reconciliacao.csv --apply --json
```

Verificar:

- quantidade, unidade e validade confirmadas;
- lote criado conforme contrato;
- estado permitido para consumo somente quando os dados obrigatórios estiverem completos;
- dados inválidos rejeitados sem alteração parcial.

### 1.5 Casos de segurança e idempotência

Testar:

- segunda execução não duplica lote nem saldo;
- filial A não aparece na filial B;
- quantidade zero/negativa é rejeitada;
- produto inexistente é relatado;
- filial inexistente é relatada;
- unidade ausente é colocada em quarentena ou rejeitada conforme contrato;
- produto sem saldo não gera lote;
- `products.stock` permanece coerente com o agregado definido.

### 1.6 Testes automatizados

Ampliar `tests/Feature/Commands/ReconcileLegacyInventoryTest.php` com todos os casos acima. Executar o teste antes e depois de cada correção, mantendo os dados fictícios isolados e removíveis.

**Gate da fase:** relatório, aplicação, CSV, quarentena, filial, idempotência e casos inválidos aprovados com leitura posterior do banco.

---

## Fase 2 — Classificação e correção das falhas da suíte

**Objetivo:** reproduzir as 46 falhas e corrigir causa, não apenas expectativas.

### 2.1 Reproduzir por grupos pequenos

Executar os grupos individualmente, capturando a saída completa:

```bash
php artisan test tests/Feature/Controllers --no-ansi
php artisan test tests/Feature/Modules --no-ansi
php artisan test tests/Feature/Integrations --no-ansi
php artisan test tests/Feature --no-ansi
php artisan test tests/Unit --no-ansi
```

Quando necessário, executar cada teste falho isoladamente para separar falha real de interferência entre testes.

Criar uma matriz com:

- arquivo e método;
- mensagem exata;
- categoria;
- causa identificada;
- correção necessária;
- teste de regressão;
- status.

### 2.2 Redirects e contratos HTTP

Para cada teste que espera `200` e recebe `302`:

1. ler rota, middleware, controller e view;
2. decidir se o redirect é comportamento intencional ou regressão;
3. atualizar o teste somente quando o contrato atual estiver correto;
4. corrigir controller somente quando a rota deveria renderizar a resposta esperada.

Não alterar expectativas para esconder falha de autorização.

### 2.3 Permissões e layouts

Investigar permissões ausentes como `nfe.view`, `docs.view` e `stock.transfer`:

- comparar nomes usados nos layouts, middleware, seeders e testes;
- corrigir divergências de nomenclatura;
- garantir que a preparação do teste carregue as permissões necessárias sem depender de dados residuais;
- limpar cache do Spatie Permission no setup apropriado;
- não conceder permissões amplas a perfis que não deveriam recebê-las.

Adicionar teste de regressão para cada permissão ausente que cause erro 500.

### 2.4 Isolamento, factories e chaves únicas

Corrigir falhas de:

- foreign keys com filiais inexistentes;
- números fixos duplicados;
- dados compartilhados entre testes;
- factories com valores estáticos;
- consultas sem escopo ao registro criado pelo teste.

Preservar o mecanismo de transação existente e não introduzir banco alternativo. Quando um teste precisa de persistência entre requests, documentar explicitamente a razão.

### 2.5 Contratos de dados desatualizados

Revisar casos como:

- `ControlledSubstanceLogControllerTest` versus `substance_id`;
- `SystemUpdateControllerTest` versus armazenamento criptografado;
- validações de senha/token;
- notificações e configurações de provider.

Atualizar testes para o contrato de segurança vigente ou corrigir o código quando houver regressão comprovada.

### 2.6 Integrações externas

Separar testes locais de testes que dependem de APIs externas:

- usar fakes/mocks locais nos testes de controller e serviço;
- manter testes de homologação claramente identificados;
- impedir chamadas externas durante a suíte local;
- nunca incluir credenciais nos testes, fixtures ou logs.

**Gate da fase:** todos os testes dos grupos Feature/Controllers, Feature/Modules, Feature/Integrations e Unit passam, ou cada exceção residual está documentada e aprovada explicitamente. Nenhuma falha pode ser ocultada por skip, xfail ou enfraquecimento de assertion.

---

## Fase 3 — Cobertura E2E dos fluxos pendentes

**Objetivo:** validar o comportamento real via navegador local, além dos testes HTTP/unitários.

Manter aplicação local disponível em `APP_URL` configurado e usar o ChromeDriver compatível com o Chrome instalado.

Criar ou ampliar fluxos E2E para:

1. cadastro de produto fracionável;
2. recebimento de lote e recipiente;
3. FEFO entre dois lotes com validades diferentes;
4. consumo parcial em atendimento;
5. abertura de recipiente e prazo pós-abertura;
6. reserva pelo veterinário;
7. separação/entrega pela Farmácia;
8. administração parcial;
9. devolução de sobra reutilizável;
10. perda por contaminação/vencimento;
11. reversão sem apagar o movimento original;
12. tentativa de consumo acima do saldo;
13. isolamento por filial;
14. permissões de veterinário, técnico, Farmácia e administrador;
15. auditoria e histórico do ledger.

Cada fluxo deve verificar visualmente o resultado e também consultar o banco/ledger quando apropriado. Falhas de servidor, driver e aplicação devem ser classificadas separadamente.

**Gate da fase:** todos os fluxos autorizados passam em execução local real, com evidência de navegador e assertions de estado.

---

## Fase 4 — Reconciliação e consistência do estoque

**Objetivo:** provar que o novo ledger e o cache legado não divergem.

Executar consultas e assertions para:

- soma dos lotes por produto/filial;
- saldo dos recipientes;
- entradas menos consumos, devoluções e perdas;
- `products.stock` como cache/agregado;
- ausência de saldo negativo;
- movimentos idempotentes;
- lotes em quarentena bloqueados para consumo/venda;
- lotes vencidos ou fora do prazo pós-abertura bloqueados.

Registrar diferenças encontradas e corrigir somente com migration/serviço versionado. Não ajustar diretamente a base remota.

**Gate da fase:** reconciliação matemática aprovada e relatório de inconsistências vazio para os dados simulados aprovados.

---

## Fase 5 — Documentação, plano e revisão de segurança

Atualizar, se necessário:

- manual do usuário;
- manual técnico;
- diagramas de medicamento fracionado, Farmácia, venda e legado;
- instruções do comando de reconciliação;
- plano de implementação com resultados reais e pendências restantes.

Revisar:

- migrations idempotentes e reversíveis quando possível;
- ausência de secrets, tokens e URLs com credenciais;
- permissões mínimas;
- validação de entradas CSV;
- ausência de SQL inseguro;
- nenhuma alteração remota fora do processo aprovado.

**Gate da fase:** diff revisado, `git diff --check` aprovado, documentação coerente com o comportamento real e nenhum segredo detectado.

---

## Fase 6 — Suíte completa e aprovação local

Executar em ordem:

```bash
php artisan test tests/Unit --no-ansi
php artisan test tests/Feature/Commands --no-ansi
php artisan test tests/Feature/Controllers --no-ansi
php artisan test tests/Feature/Modules --no-ansi
php artisan test tests/Feature/Integrations --no-ansi
php artisan test tests/Feature --no-ansi
php artisan test --exclude-group=browser --no-ansi
```

Depois executar os fluxos Dusk/E2E completos com a aplicação local ativa.

Registrar:

- total de testes;
- assertions;
- aprovados;
- ignorados, se houver;
- falhas;
- tempo de execução;
- comandos exatos;
- limitações de infraestrutura.

Timeout não será considerado aprovação.

**Gate da fase:** suíte completa e E2E concluídos sem falhas não justificadas.

---

## Fase 7 — Commit, release e deploy controlado

Somente após todos os gates locais:

1. revisar diff final e working tree;
2. executar testes finais no commit candidato;
3. criar commit versionado;
4. incrementar a versão após aprovação dos testes;
5. criar nova tag posterior à `v1.2.1`;
6. fazer push da branch e da tag;
7. verificar SHA local/remoto e metadata da release;
8. somente então executar migration/deploy remoto aprovado;
9. verificar remotamente a aplicação e a migration, sem substituir os testes locais;
10. registrar resultado, limitações e plano de rollback.

Nenhum deploy remoto será feito enquanto a suíte global ou os gates E2E permanecerem pendentes.

---

## Critérios finais de conclusão

O plano será considerado concluído somente quando:

- [ ] reconciliação legada simulada executada na base `vetessence`;
- [ ] quarentena, CSV confirmado, filial e idempotência validados;
- [ ] as 46 falhas reproduzidas e resolvidas ou formalmente aprovadas como exceções;
- [ ] suíte completa aprovada sem timeout;
- [ ] E2E de estoque, Farmácia, reservas, administração, devolução, perdas, reversões e FEFO aprovado;
- [ ] consistência do ledger e `products.stock` comprovada;
- [ ] documentação e diagramas atualizados;
- [ ] diff e segurança revisados;
- [ ] nova release criada somente após os gates locais;
- [ ] deploy remoto verificado após a validação local.

## Aprovação solicitada

A execução deve começar pelo **Gate 0** e pela **Fase 1**, usando exclusivamente a base local `vetessence`. A Fase 7 permanece bloqueada até a aprovação de todos os gates anteriores.
