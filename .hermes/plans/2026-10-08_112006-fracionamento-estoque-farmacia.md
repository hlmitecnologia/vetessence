# Fracionamento e Consumo Parcial de Medicamentos — Plano de Implementação

> **Para o Hermes:** executar este plano somente após aprovação, usando TDD e validação E2E dos fluxos de estoque/farmácia.

**Objetivo:** permitir controlar medicamentos por quantidade fracionada e por lote, registrando consumo, devolução de sobra, perdas e reversões sem perder rastreabilidade.

**Arquitetura recomendada:** separar o cadastro do produto do saldo físico. O produto continuará sendo o catálogo/SKU; lotes e recipientes físicos passarão a controlar quantidade disponível por filial, unidade de medida e validade. Cada alteração será um movimento imutável, com referência ao atendimento, pet, prescrição, fatura ou operação de estoque.

**Stack:** Laravel 13, PHP 8.4, MySQL/MariaDB, Eloquent, Blade/Livewire, PHPUnit e Playwright/Selenium conforme os fluxos existentes.

---

## 1. Diagnóstico do estado atual

### Constatações no código e banco

1. `products.stock` é `INT` e representa um saldo único, sem filial e sem lote.
2. `products` possui somente um `batch_number`, `lot_number` e `expiration_date`; portanto, não suporta vários lotes do mesmo produto.
3. `stock_movements.quantity` e `balance_after` são `INT`; não suportam `2.5 ml`, `0.25 g` etc.
4. `StockMovement` tem lote/validade, mas não existe entidade de lote com saldo próprio.
5. `StockDeductionService` baixa sempre `1` unidade por vacinação:
   - `app/Services/StockDeductionService.php`
   - `app/Listeners/DeductStockListener.php`
6. `DeductStockOnPaid` baixa `InvoiceItem.quantity`, mas o model `InvoiceItem` ainda faz cast para inteiro, apesar de a coluna ser `DECIMAL(10,2)`.
7. `HospitalizationPrescription` guarda medicamento, dose e unidade como texto; não possui produto, lote, execução nem quantidade efetivamente administrada.
8. O registro de substâncias controladas já suporta decimais em `controlled_substances` e `controlled_substance_logs`, mas ainda não está vinculado ao lote/recipiente do estoque comum.
9. A entrada manual aceita quantidade decimal, mas o saldo do produto continua inteiro e a implementação precisa ser transacional para impedir divergências.
10. Existem problemas relacionados que devem ser corrigidos junto ou explicitamente isolados:
    - recebimento de pedido de compra atualiza `received_quantity`, mas não cria entrada nem incrementa estoque;
    - transferência cria movimentos, mas não ajusta o saldo global do produto;
    - o saldo global atual não consegue representar corretamente estoque por filial.

### Conclusão

Não é seguro apenas trocar `INT` por `DECIMAL` em `products.stock`. Isso permitiria fracionamento, mas continuaria misturando lotes, filiais, recipientes abertos e históricos incompatíveis. A unidade de controle deve ser o lote — e, quando necessário, o recipiente/frasco.

---

## 2. Decisões já aprovadas

As respostas recebidas definem o escopo inicial:

- fracionamento para **uso interno e venda ao tutor**;
- unidades iniciais: **ml, mg, g, comprimido e dose**;
- qualquer medicamento poderá ser marcado no cadastro como fracionável/rastreável;
- prazo informado no cadastro; quando o campo estiver vazio, o padrão operacional será **5 dias** após abertura/reconstituição;
- somente medicamentos marcados no cadastro exigirão fracionamento/rastreamento de recipiente;
- retirada para atendimento/internação será uma **reserva**, com notificação para a farmácia e comunicação entre veterinário e farmácia;
- estoque por filial deve ser implementado/corrigido nesta iniciativa;
- FEFO será automático, com notificações para Estoque e Farmácia;
- venda fracionada usará a tabela de preços do produto, com preço/custo para embalagem inteira e preço/custo para a unidade fracionada;
- aprovação de perdas, ajustes e reversões ficará inicialmente com Super Admin/Admin, mas será uma permissão configurável para outros perfis/usuários pelo mecanismo atual de permissões.

### Estado atual dos técnicos

O perfil `tecnico` atualmente é voltado à execução de tarefas clínicas sem prescrição. Possui `execution-maps.view` e `execution-maps.execute`, além de consultas operacionais, mas não possui autorização para prescrever ou administrar medicamentos. Essa regra será preservada: o técnico poderá visualizar a reserva e executar somente ações explicitamente autorizadas pelo fluxo atual, sem confirmar administração medicamentosa nem aprovar perdas/ajustes. A administração fracionada ficará com o veterinário ou outro perfil que receba essa permissão de forma explícita.


Não existe atualmente uma tela dedicada de cadastro de unidades de medida. O banco possui `products.unit`, mas o formulário Livewire e o `ProductController` não expõem nem validam adequadamente esse campo; além disso, o valor atual é unitário por produto, sem conversão, fracionamento ou prazo após abertura. O plano deve criar a configuração de unidade no cadastro do medicamento, preferencialmente com lista controlada para as cinco unidades iniciais e suporte a conversão/embalagem.

## 3. Perfis e permissões recomendados

A autorização deve separar solicitação clínica, execução física, administração e supervisão. Os nomes abaixo são novos permisos Spatie, não devem ser confundidos com os permisos genéricos atuais `stock.*`.

| Perfil | Pode fazer | Não deve fazer |
|---|---|---|
| **Veterinário** | Cadastrar prescrição; solicitar/reservar medicamento; informar quantidade, unidade, pet, atendimento e urgência; confirmar administração clínica; solicitar devolução ou descarte; consultar status e histórico do próprio atendimento | Alterar saldo manualmente, aprovar ajustes, editar lote/validade, confirmar recebimento de compra ou apagar movimentos |
| **Técnico** | Visualizar reservas; separar/entregar mediante autorização; registrar administração se o protocolo permitir; devolver sobra; informar perda/contaminação; consultar saldo e validade | Cadastrar produto, alterar regras de validade, ajustar estoque sem autorização ou aprovar própria perda |
| **Estoque/Farmácia** | Cadastrar medicamento e unidades; receber compras; criar lotes/recipientes; atender reservas; fazer devolução física; selecionar/substituir lote conforme FEFO; registrar perdas; transferir entre filiais; consultar relatórios | Confirmar administração clínica no lugar do responsável, exceto operação explicitamente autorizada |
| **Admin/Super Admin** | Todas as operações; configurar permissões, unidades, regras de abertura, limites e exceções; aprovar ajustes, perdas e reversões; auditar | — |
| **Super Financeiro/Financeiro** | Consultar disponibilidade e reflexos financeiros; faturar venda fracionada conforme regra fiscal; consultar custo e movimentação necessária à conciliação | Movimentar estoque físico, confirmar consumo clínico ou editar lote |
| **Auditor** | Somente leitura de reservas, consumos, devoluções, perdas, lotes, saldos e trilha de auditoria | Qualquer alteração |
| **Recepção** | Consultar disponibilidade e status de reserva; iniciar venda conforme permissão comercial, sem alterar estoque físico diretamente | Criar/alterar lote, separar medicamento clínico ou registrar descarte |

Permissões sugeridas:

- `medication-catalog.view/create/edit`
- `inventory-batches.view/create/edit`
- `inventory-containers.view/create/edit`
- `medication-reservations.view/create/cancel/fulfill`
- `medication-administrations.view/create/edit/reverse`
- `medication-returns.view/create/approve`
- `medication-losses.view/create/approve`
- `stock-movements.view/create/adjust/transfer`
- `stock-movements.approve`
- `fractional-sales.view/create`
- `inventory-reports.view`
- `inventory-settings.view/edit`

Recomendação de atribuição inicial:

- **Veterinário:** reservas, prescrições, administrações e solicitações de devolução/perda.
- **Técnico:** execução operacional de reservas, administrações delegadas e devoluções.
- **Estoque:** todas as operações físicas e de lotes, sem administração clínica.
- **Super Admin/Admin/Branch Admin:** todas conforme escopo global ou filial.
- **Auditor:** permissões `.view` e relatórios, sem `.create`, `.edit`, `.approve` ou `.delete`.
- **Financeiro:** apenas venda/faturamento e consultas necessárias.

## 4. Regras de negócio propostas

### 4.1 Unidades

Cada produto deve definir:

- **unidade de estoque:** unidade em que o saldo é controlado (`un`, `ml`, `g`, `mg`, `comprimido`, `dose`, `m`, etc.);
- **quantidade por embalagem:** por exemplo, `10 ml` por frasco;
- **unidade de dispensação/uso:** unidade usada no atendimento, normalmente igual à unidade de estoque;
- **fator de conversão**, quando a compra é em embalagem e o consumo em conteúdo.

Regra recomendada: converter a entrada para a unidade de estoque no momento do recebimento. Um frasco de 10 ml gera 10 ml disponíveis; a quantidade de frascos permanece registrada como informação de embalagem, não como saldo consumível.

### 4.2 Lote e recipiente

- Todo recebimento cria ou incrementa um **lote** com produto, filial, lote, validade e custo unitário.
- Para medicamentos líquidos, injetáveis, reconstituídos ou sujeitos a prazo após abertura, o lote terá **recipientes/frasco individuais**.
- Um frasco de 10 ml começa como `unopened`, com `10 ml` disponíveis.
- Ao usar 2 ml:
  - o recipiente passa a `opened`;
  - disponível passa a `8 ml`;
  - o lote continua identificável;
  - registra-se data/hora de abertura e, se aplicável, prazo de uso após abertura/reconstituição.
- Para itens que não exigem rastreamento por frasco, pode-se controlar apenas o saldo agregado do lote.

### 4.3 Consumo em atendimento

- A administração deve informar quantidade efetivamente utilizada, não apenas “uma unidade”.
- O sistema deve aplicar FEFO: primeiro o lote com validade mais próxima, respeitando filial e disponibilidade.
- O usuário pode selecionar explicitamente lote/frasco quando a rastreabilidade clínica ou regulatória exigir.
- A operação deve registrar produto, lote, recipiente, quantidade, unidade, pet, atendimento/prescrição, usuário, data e motivo.
- O estoque não deve ser baixado novamente ao editar a prescrição; edição deve gerar ajuste/reversão explícita.

### 4.4 Retirada temporária e devolução

Distinguir duas situações:

1. **Consumo definitivo:** saiu da farmácia e foi administrado, descartado ou perdido. Não retorna ao saldo.
2. **Retirada temporária:** foi separado para atendimento/internação, mas não foi usado. Deve ser uma reserva/transferência para localização operacional, não uma baixa definitiva.

Na devolução de sobra:

- informar o recipiente/lote original;
- registrar quantidade devolvida e condição (`lacrado`, `aberto`, `reconstituído`, `contaminado`);
- aumentar o saldo somente se o item puder retornar ao estoque;
- itens abertos/reconstituídos devem respeitar prazo de uso após abertura e condições de armazenamento;
- se não for reutilizável, registrar `loss/waste`, sem reinserir saldo.

### 4.5 Perdas, vencimento e reversões

- Derramamento, quebra, contaminação, vencimento ou descarte geram movimento de perda com motivo obrigatório.
- Cancelamentos não apagam movimentos: geram movimento compensatório vinculado ao movimento original.
- Toda movimentação deve ser idempotente por referência operacional, evitando baixa duplicada em retries ou eventos repetidos.
- Não permitir saldo negativo; usar transação e bloqueio de linha (`lockForUpdate`).

### 4.6 Substâncias controladas

- Manter o livro de `controlled_substance_logs` e suas regras de testemunha/retenção.
- Vincular cada entrada/saída/devolução/perda ao lote, recipiente e movimento de estoque.
- Administração parcial deve registrar quantidade decimal, pet, prescrição, responsável e testemunha quando exigido.
- Não reutilizar o fluxo comum sem preservar a trilha ANVISA.

### 4.7 Faturamento

Separar consumo clínico de venda:

- **uso interno em atendimento/internação:** baixa operacional, vinculada ao pet/procedimento;
- **venda ao tutor:** item faturado com quantidade e unidade fracionada somente se a regra fiscal/comercial permitir;
- a emissão fiscal deve continuar usando o produto e a quantidade comercial correta, sem confundir saldo clínico com quantidade de embalagens.

A decisão sobre vender fracionado ou somente consumir internamente deve ser aprovada antes da implementação do faturamento.

---

## 5. Modelo de dados proposto

### 5.1 `products`

Adicionar, via migration:

- `stock_unit` — unidade de controle;
- `dispensing_unit` — unidade apresentada no uso;
- `package_quantity` — conteúdo por embalagem;
- `fractional_cost_price` — custo por unidade fracionada;
- `fractional_sale_price` — preço de venda por unidade fracionada;
- `allows_fractional` — permite quantidade fracionada;
- `requires_container_tracking` — exige rastreamento de frasco/recipiente;
- `opened_use_days` — prazo após abertura, padrão 5 dias quando vazio;
- `reconstituted_use_days` — prazo após reconstituição, padrão 5 dias quando vazio;
- `is_controlled` ou vínculo explícito à substância controlada, se o código atual ainda não possuir essa relação.

`cost_price` e `sale_price` existentes representarão a embalagem/item comercial inteiro. Os dois campos fracionados representarão uma unidade da tabela de preço fracionada. A venda deverá informar a unidade usada e calcular o total sem misturar preço de embalagem com preço por ml/mg/comprimido/dose.

Manter `stock` apenas temporariamente como campo agregado/cache para compatibilidade, com fonte oficial passando a ser a soma dos saldos de lotes por filial.

### 5.2 `inventory_batches` ou `stock_batches`

Criar tabela para:

- `product_id`, `branch_id`;
- `batch_number`, `lot_number`, `expiration_date`;
- `quantity_received`, `quantity_available`;
- `stock_unit`, `package_quantity`, `package_count`;
- `unit_cost`;
- status (`active`, `expired`, `exhausted`, `quarantined`);
- timestamps e usuário de recebimento.

Índices: produto/filial/status, validade, lote e produto/filial/lote.

### 5.3 `inventory_containers`

Criar somente para produtos configurados com rastreamento de recipiente:

- `inventory_batch_id`;
- identificador do frasco/recipiente;
- `initial_quantity`, `available_quantity`, `unit`;
- status (`unopened`, `opened`, `empty`, `discarded`, `returned`);
- `opened_at`, `beyond_use_at`, `reconstituted_at`;
- localização operacional opcional;
- usuário responsável.

### 5.4 `stock_movements`

Evoluir para:

- `inventory_batch_id` e `inventory_container_id` nullable durante a migração, obrigatórios para novos movimentos de estoque;
- `quantity` e `balance_after` como `DECIMAL(14,4)`;
- `unit`;
- tipos normalizados: `entry`, `exit`, `consumption`, `reservation`, `return`, `loss`, `adjustment`, `transfer_out`, `transfer_in`, `reversal`;
- `reason_code` e `notes`;
- referência polimórfica ou campos de origem (`source_type`, `source_id`), além de chave idempotente;
- `performed_by`/`user_id` e filial.

Não excluir o histórico existente; fazer backfill para lotes legados sintéticos, marcados como `legacy` quando não houver lote confiável.

### 5.5 Registro de consumo/administração

Criar `medication_administrations` ou entidade equivalente para separar prescrição de execução:

- pet, atendimento/internação, prescrição;
- produto, lote, recipiente;
- quantidade prescrita e quantidade administrada;
- unidade, via, data/hora;
- usuário executor;
- status (`administered`, `partial`, `refused`, `cancelled`, `wasted`);
- motivo e observações;
- referências aos movimentos de saída, devolução e perda.

Isso permite que uma prescrição de 2 ml seja parcialmente administrada, recusada ou complementada sem alterar o texto original da prescrição.

---

## 6. Plano de implementação em fases

### Fase 0 — Decisões e inventário

**Objetivo:** fechar regras antes de alterar o schema.

- Confirmar unidades prioritárias (`ml`, `mg`, `g`, comprimido, dose, unidade).
- Confirmar se venda fracionada ao tutor será permitida ou se o fracionamento será somente clínico.
- Confirmar quais medicamentos exigem rastreamento por frasco.
- Confirmar regra de prazo após abertura/reconstituição.
- Levantar produtos/lotes existentes no banco `vetessence` e sua qualidade de preenchimento.
- Definir permissões: visualizar, movimentar, consumir, devolver, ajustar e autorizar perdas.

**Saída:** matriz aprovada de unidades, estados e permissões.

### Fase 1 — Domínio e migrations

**Testes primeiro:** testes de unidade para conversão de unidades, FEFO, arredondamento, saldo insuficiente e idempotência.

Arquivos prováveis:

- `database/migrations/*_add_fractional_inventory_fields_to_products.php`
- `database/migrations/*_create_inventory_batches_table.php`
- `database/migrations/*_create_inventory_containers_table.php`
- `database/migrations/*_create_medication_administrations_table.php`
- `database/migrations/*_alter_stock_movements_for_fractional_inventory.php`
- `app/Models/InventoryBatch.php`
- `app/Models/InventoryContainer.php`
- `app/Models/MedicationAdministration.php`
- `app/Models/Product.php`
- `app/Models/StockMovement.php`

Implementar serviços puros para:

- conversão e validação de unidades;
- seleção FEFO;
- cálculo de saldo;
- abertura/reconstituição de recipiente;
- reversão compensatória.

### Fase 2 — Serviço transacional de estoque

Substituir baixas diretas em `Product::decrement()` por um serviço único, por exemplo:

- `app/Services/Inventory/InventoryMovementService.php`
- `app/Services/Inventory/MedicationConsumptionService.php`
- `app/Services/Inventory/InventoryReturnService.php`

Todas as operações devem usar `DB::transaction()` e `lockForUpdate()` sobre lote/recipiente. O serviço deve atualizar movimentos, saldos, referências e o cache/agregado de `products.stock` durante a transição.

Corrigir também, no mesmo domínio:

- `app/Listeners/DeductStockOnPaid.php`;
- `app/Services/StockDeductionService.php`;
- `app/Http/Controllers/StockController.php`;
- `app/Http/Controllers/PurchaseOrderController.php`;
- `app/Services/StockForecastService.php`.

### Fase 3 — Vacinação e atendimento

- Trocar `Vaccination` de “produto = baixa fixa de 1” para quantidade/unidade/lote/recipiente.
- Adicionar quantidade efetivamente aplicada à vacinação.
- Permitir registrar sobra devolvida ou perda.
- Impedir dupla baixa ao editar uma vacinação.
- Manter lote informado no certificado e na auditoria.

Arquivos prováveis:

- `app/Http/Controllers/VaccinationController.php`
- `app/Models/Vaccination.php`
- `resources/views/vaccinations/create.blade.php`
- `resources/views/vaccinations/edit.blade.php`
- `app/Listeners/DeductStockListener.php`
- `tests/Feature/Controllers/VaccinationControllerTest.php`
- `tests/Feature/Integrations/VaccinationCycleTest.php`
- `tests/Unit/Services/StockDeductionServiceTest.php`

### Fase 4 — Internação, prescrição e execução

- Adicionar produto vinculado à prescrição hospitalar.
- Separar dose prescrita de administração realizada.
- Criar tela/fluxo para administrar, devolver sobra, registrar recusa e registrar perda.
- Permitir agrupar várias administrações do mesmo frasco e manter o saldo remanescente.
- Vincular o consumo ao mapa/tarefa de execução quando aplicável.

Arquivos prováveis:

- `app/Models/HospitalizationPrescription.php`
- `app/Http/Controllers/HospitalizationPrescriptionController.php`
- `app/Models/HospitalizationDailyRecord.php`
- `app/Models/ExecutionLog.php`
- `resources/views/hospitalization-prescriptions/*`
- testes Feature de internação e Unit do serviço de consumo.

### Fase 5 — Estoque, recebimento, devolução e transferências

- Fazer recebimento de pedido criar lotes e entrada efetiva.
- Fazer transferências movimentarem lote/recipiente entre filiais sem perder rastreabilidade.
- Criar telas de seleção de lote/recipiente e ações de devolução/perda/reversão.
- Aplicar FEFO e bloquear lotes vencidos ou fora do prazo após abertura.
- Corrigir o uso do saldo global versus saldo por filial.

Arquivos prováveis:

- `app/Http/Controllers/PurchaseOrderController.php`
- `app/Http/Controllers/StockController.php`
- `resources/views/stock/create.blade.php`
- `resources/views/stock/movements.blade.php`
- `resources/views/purchase-orders/*`
- `app/Services/StockForecastService.php`
- testes de estoque, compras e transferências.

### Fase 6 — Substâncias controladas e auditoria

- Vincular logs ANVISA a lote, recipiente, administração e movimento.
- Exigir motivo, prescrição e testemunha conforme o tipo de operação.
- Ajustar relatórios mensal/anual para quantidades fracionadas.
- Garantir retenção, auditoria e impossibilidade de apagar histórico sem fluxo autorizado.

Arquivos prováveis:

- `app/Http/Controllers/ControlledSubstanceLogController.php`
- `app/Models/ControlledSubstanceLog.php`
- `app/Models/ControlledSubstance.php`
- views e relatórios ANVISA
- testes de retenção, saldo e testemunha.

### Fase 7 — Migração de dados e compatibilidade

- Criar lotes sintéticos para estoque legado sem lote conhecido, com marcação `legacy` quando não houver lote confiável.
- Para cada produto/filial, gerar inicialmente um lote `LEGACY-SIN-LOTE` com a quantidade física reconciliada, validade desconhecida e alerta de rastreabilidade incompleta.
- Se a quantidade física ou a validade não puder ser confirmada, colocar o saldo em **quarentena**, impedindo consumo/venda até conferência e autorização do responsável.
- Não inventar lote, validade ou prazo de abertura ausente; exibir explicitamente “lote legado não informado”.
- Tornar lote, validade e unidade obrigatórios para novos recebimentos após o rollout.
- Reconciliar `products.stock` com a soma dos lotes por filial.
- Gerar relatório de inconsistências antes de ativar o novo fluxo.
- Manter fallback somente durante a migração; remover baixa direta depois da estabilização.

### Fase 8 — Interface, documentação e rollout

- Atualizar `resources/docs/user-manual/08-estoque.md`, `03-vacinas.md`, `06-internacoes.md` e `09-financeiro.md`.
- Atualizar manual técnico e diagramas de fluxo.
- Criar feature flag/configuração de rollout, se necessário.
- Executar primeiro em ambiente local, depois homologação, com backup e validação de reconciliação.
- Ativar por filial/produto somente após validar os saldos.

---

## 7. Cenários de aceitação

1. Frasco de 10 ml recebido: saldo do lote/frasco = 10 ml.
2. Atendimento usa 2 ml: saída = 2 ml, recipiente aberto, saldo = 8 ml.
3. Segundo atendimento usa 3 ml do mesmo frasco: saldo = 5 ml, dois consumos vinculados ao mesmo recipiente.
4. Sobra de 5 ml é devolvida: movimento de retorno, saldo volta a 5 ml sem duplicar estoque.
5. Sobra contaminada é descartada: movimento de perda, saldo permanece 0 ml no recipiente.
6. Frasco vencido ou fora do prazo após abertura não pode ser selecionado para consumo.
7. Dois usuários tentando consumir o mesmo saldo não podem gerar saldo negativo.
8. Retry do mesmo evento não cria segunda baixa.
9. Cancelamento gera reversão vinculada, sem apagar o movimento original.
10. Dois lotes do mesmo produto são consumidos em ordem FEFO.
11. Produto em duas filiais mantém saldos independentes.
12. Quantidades decimais aparecem corretamente em estoque, histórico, relatórios e auditoria.
13. Vacinação exige quantidade efetiva e não baixa automaticamente uma unidade fixa.
14. Prescrição hospitalar pode registrar quantidade prescrita diferente da administrada.
15. Substância controlada mantém livro ANVISA, lote/recipiente, responsável e testemunha.

---

## 8. Testes e validação

### PHPUnit

Criar ou ampliar:

- `tests/Unit/Services/Inventory/UnitConversionServiceTest.php`
- `tests/Unit/Services/Inventory/InventoryMovementServiceTest.php`
- `tests/Unit/Services/Inventory/FefoAllocationServiceTest.php`
- `tests/Feature/Controllers/StockControllerTest.php`
- `tests/Feature/Controllers/PurchaseOrderControllerTest.php`
- `tests/Feature/Controllers/VaccinationControllerTest.php`
- `tests/Feature/Controllers/HospitalizationPrescriptionControllerTest.php`
- testes de `ControlledSubstanceLogController` e relatórios.

Executar, em ordem:

```bash
php artisan migrate:fresh --seed --env=testing
php artisan test tests/Unit/Services/Inventory tests/Unit/Services/StockDeductionServiceTest.php
php artisan test tests/Feature/Controllers/StockControllerTest.php tests/Feature/Controllers/PurchaseOrderControllerTest.php
php artisan test tests/Feature/Controllers/VaccinationControllerTest.php
php artisan test tests/Feature/Controllers/HospitalizationPrescriptionControllerTest.php
php artisan test
```

### E2E

Criar fluxo real com usuário de estoque/veterinário:

1. cadastrar produto fracionável;
2. receber lote com embalagem de 10 ml;
3. iniciar atendimento/internação;
4. administrar 2 ml;
5. devolver 8 ml ou registrar nova administração;
6. conferir lote, recipiente, saldo, histórico e auditoria;
7. tentar consumo acima do saldo e lote vencido;
8. validar filial e permissões.

Usar os padrões existentes em `vetessence-playwright` e/ou `bin/treinamento.py`, sem declarar sucesso apenas por teste manual.

### Reconciliação

Antes e depois da migração:

- soma dos lotes por produto/filial;
- `products.stock` legado versus novo agregado;
- total de entradas menos saídas, devoluções e perdas;
- relatório de registros sem lote, sem filial ou com saldo negativo.

---

## 9. Riscos e decisões para aprovação

### Riscos

- Alterar o significado de `products.stock` pode afetar vendas, previsão e relatórios.
- Há dados legados sem lote ou unidade confiável.
- Consumo clínico e venda possuem regras fiscais diferentes.
- Recipientes abertos/reconstituídos exigem regras sanitárias parametrizáveis.
- Eventos de pagamento e procedimentos podem ser reprocessados.
- Substâncias controladas têm exigências adicionais de auditoria e retenção.

### Decisões aprovadas

1. O prazo após abertura/reconstituição vem do cadastro; se vazio, o padrão operacional é 5 dias.
2. Somente medicamentos marcados no cadastro usam o fluxo fracionado; o recipiente individual é configurável.
3. Produtos antigos sem lote são inventariados e reconciliados como `LEGACY-SIN-LOTE`; dados não confirmados ficam em quarentena.
4. O preço/custo inteiro permanece separado do preço/custo da unidade fracionada.
5. O Técnico mantém a regra atual e não recebe autorização automática para administração ou aprovação.
6. Super Admin/Admin aprovam perdas, ajustes e reversões inicialmente; o mecanismo de permissões continua configurável.

## 10. Critério de aprovação do plano

Plano aprovado e execução inicial realizada na release v1.2.0. A camada de domínio inclui lotes, recipientes, reservas, administrações, ledger decimal, idempotência e FEFO; o cadastro de produto, documentação e teste do consumo parcial foram atualizados. Antes do deploy em ambiente remoto, executar as migrations e a suíte de integração/E2E específica no ambiente de homologação.

### Estado da execução

- [x] Migrations versionadas para catálogo, lotes, recipientes, reservas, administrações e movimentos.
- [x] Serviço transacional de consumo/devolução parcial com validade pós-abertura padrão de 5 dias.
- [x] Campos e tela de cadastro para unidades, fracionamento, recipiente e preços.
- [x] Rotas de reserva, entrega, administração e cancelamento com notificação para Farmácia.
- [x] Teste automatizado do cenário 10 ml → consumo 2 ml → saldo 8 ml.
- [x] README, manual do usuário e manual técnico atualizados.
- [x] Comando local de relatório/reconciliação legado com quarentena, CSV confirmado e `LEGACY-SIN-LOTE`.
- [ ] Execução operacional da reconciliação física com inventário aprovado por filial.
- [x] Validação E2E local do fluxo de Estoque: 9 testes e 9 assertions aprovados após iniciar a aplicação com o banco Dusk correto.
- [ ] Suíte completa: ainda há 46 falhas em testes preexistentes/não relacionados, incluindo isolamento de banco, permissões e expectativas de redirects; não declarar aprovação global até corrigi-las.
- [ ] Aplicação operacional da reconciliação legada após conferência física.
- [ ] Suíte completa e validação E2E antes do deploy remoto.
