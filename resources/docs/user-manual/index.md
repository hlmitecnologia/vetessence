# Manual do Usuário

Guia prático para utilização do VetEssence no dia a dia da clínica.

---

## Agenda

### Agendar Consulta
1. Acesse **Agenda > Novo** na sidebar
2. Selecione o **tutor** (ou cadastre novo)
3. Selecione o **pet**
4. Escolha **veterinário**, **data** e **horário**
5. Selecione o **tipo de consulta**
6. Clique em **Salvar**

### Visualizar Agenda
- Acesse **Agenda > Listar** para visão em tabela
- O **Calendário Visual** (FullCalendar) mostra consultas por dia/semana/mês
- Cores diferentes por veterinário

### Agendamento Online
- Tutores podem agendar via portal público
- As solicitações aparecem em **Agenda > Agendamentos Online**
- É preciso confirmar manualmente

---

## Prontuários

### Criar Prontuário
1. Acesse **Clínico > Prontuários**
2. Clique em **Novo**
3. Selecione o **pet** e **veterinário**
4. Preencha os campos SOAP:
   - **S** (Subjetivo): Queixa principal, histórico
   - **O** (Objetivo): Achados do exame físico
   - **A** (Avaliação): Diagnóstico, hipóteses
   - **P** (Plano): Tratamento, exames, retorno
5. Clique em **Salvar**

### Timeline do Paciente
- Acesse o **pet > Timeline** para ver todos eventos (consultas, vacinas, exames, cirurgias, internações) em ordem cronológica

---

## Vacinas

### Aplicar Vacina
1. Acesse **Clínico > Vacinas**
2. Clique em **Nova Vacina**
3. Selecione **pet**, **vacina**, **lote**, **veterinário**
4. O sistema deduz automaticamente do estoque (se configurado)
5. Gere o **Certificado de Vacina** em PDF (layout CFMV)

### Previsão de Vacinas a Vencer
- Acesse **Clínico > Previsão de Vacinas**
- Filtre por espécie e número de dias
- Visualize quais vacinas estão próximas do vencimento

---

## Farmácia e Estoque

### Produtos
- Cadastro com SKU, código de barras, preço custo/venda
- Controle de lote e validade
- Alertas de estoque baixo

### Movimentações
- **Entrada**: Compra de produtos
- **Saída**: Uso em procedimentos, vendas
- **Transferência**: Entre filiais
- **Ajuste**: Correção de inventário

### Pedidos de Compra
- Fluxo: Rascunho → Pedido → Recebimento
- Aprovação necessária para valores acima do limite

### Substâncias Controladas (ANVISA)
- Registro obrigatório de entrada e saída
- Relatórios mensais e anuais
- Exportação CSV

---

## Financeiro

### Faturas
- Geração automática ao finalizar consulta
- Múltiplas formas de pagamento
- Parcelamento

### Convênios
- Cadastro de convênios e planos
- Faturamento por guia
- Envio automático via webhook

### Relatórios
- Receita por período
- Contas a receber
- Comissões por veterinário
- Formas de pagamento

---

## Portal do Tutor

O portal permite que tutores acompanhem:
- **Prontuários** dos seus pets
- **Exames** e resultados
- **Receitas** emitidas
- **Faturas** e histórico de pagamentos
- **Agendamento** de consultas

Acesso em: `/portal`
