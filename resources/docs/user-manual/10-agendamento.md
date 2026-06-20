# Agendamento

## Calendário Visual
1. Acesse **Agenda > Calendário**
2. Visualize compromissos do dia/semana/mês
3. Filtre por:
   - **Profissional**
   - **Filial**
   - **Tipo** (consulta, cirurgia, retorno, exames)
   - **Status** (confirmado, pendente, realizado, cancelado)
4. Navegue entre datas com os controles do calendário

## Agendar Compromisso
1. Clique no horário desejado no calendário ou em **Novo**
2. Preencha:
   - **Pet** (selecione ou cadastre rapidamente)
   - **Tutor**
   - **Tipo** de atendimento
   - **Profissional**
   - **Data e horário**
   - **Duração** estimada (padrão: 30min)
   - **Observações**
3. Selecione **status**:
   - **Agendado**: Slot reservado
   - **Confirmado**: Tutor confirmou presença
   - **Em andamento**: Atendimento iniciou
4. Clique em **Salvar**

### Agendamento Rápido
- Na ficha do pet, clique em **Agendar**
- Os dados do pet e tutor são preenchidos automaticamente
- Abre modal rápido sem sair da tela do pet

### Agendamento Recorrente
- Agende retornos automáticos de uma só vez
- Ex: curativo a cada 48h por 5 vezes, reavaliação em 7 dias
- Defina **frequência** (diária, semanal, quinzenal, mensal, customizada) e **número de repetições**
- O sistema gera todos os compromissos automaticamente
- Comando `appointments:generate-recurring` gera em lote

## Agendamento Online (Portal do Tutor)

### Para o Tutor
1. Acesse o **Portal do Tutor**
2. Clique em **Agendar Consulta**
3. Selecione **pet**, **tipo de serviço**, **profissional** e **horário**
4. Confirme o agendamento
5. Receba confirmação por WhatsApp/SMS/E-mail

### Para a Clínica
- Agendamentos online aparecem como **pendentes**
- Recepcionista confirma ou reagenda conforme disponibilidade
- Limite de agendamentos online por dia (configurável)

## Calendário Visual (FullCalendar)
1. Acesse **Agenda > Calendário**
2. Visualização: **Dia**, **Semana**, **Mês**
3. Compromissos coloridos por tipo/profissional
4. **Arraste e solte** para reagendar
5. **Clique** em horário vazio para novo agendamento
6. **Filtros**: Profissional, filial, tipo, status

## Controle de Conflitos
- O sistema alerta se o profissional já tem compromisso no horário
- Alerta de horário de almoço/intervalo
- Limite de agendamentos por período (configurável)
- Bloqueio de horários (folga, feriado, reunião)

## Lembretes Automáticos
- **24h antes**: Aviso por WhatsApp/SMS/E-mail (comando `appointments:remind` agendado diariamente às 18h)
- O tutor pode **confirmar** ou **solicitar reagendamento** pelo link da notificação
- Lembretes de **2h antes** podem ser configurados manualmente (não são automáticos)

## Reagendamento e Cancelamento
- Arraste o compromisso para novo horário no calendário (drag & drop)
- Cancelamento registra **motivo**
- Cancelamento com < 2h pode gerar taxa
- Histórico de alterações mantido
- Tutor pode reagendar via portal com até 24h de antecedência

## Relatórios
- **Taxa de comparecimento** por profissional
- **Faturamento previsto** x realizado
- **Horários mais ocupados**
- **Lista de espera** (pacientes que aguardam desistência)

## Painel de Senhas
- Configure chamada de senhas na recepção
- Tela de TV mostrando fila de atendimento
- Integração com som ambiente

## Escala de Veterinários

### Turno de Veterinário (`is_vet_shift`)

Ao criar ou editar uma escala na tela **Agenda > Escalas de Trabalho**, o campo **Turno de Veterinário** indica que aquele turno é elegível para atendimento clínico (consultas, exames, etc.). Apenas turnos marcados como veterinário aparecem no Portal do Tutor para agendamento online.

- **Ativado**: O profissional está disponível para consultas agendadas pelo portal
- **Desativado**: O turno é exclusivo para outras atividades (administrativo, recepção, etc.)

### Tela de Plantões de Veterinários

Acesse **Agenda > Plantões** (sidebar) para visualizar **apenas** os turnos marcados como veterinário. A tela exibe:

- Nome do veterinário e CRMV
- Data, horário de início e término
- Unidade (filial)
- Tipo de turno (Regular, Manhã, Tarde, Noturno)
- Indicador de plantão (badge vermelho)

A partir dessa tela é possível criar nova escala ou editar/excluir plantões existentes.

## Cancelamento Automático de Consultas

Quando um turno de veterinário é **alterado** (data, horário) ou **excluído**, o sistema verifica automaticamente se as consultas já agendadas para aquele veterinário naquela data ainda são viáveis.

- **Slot ainda disponível**: a consulta permanece agendada (se o horário ainda estiver dentro de outro turno do mesmo veterinário)
- **Slot não disponível**: a consulta é cancelada automaticamente com o motivo: *"Cancelado automaticamente: alteração na escala do veterinário."*

Esse comportamento garante que o tutor não tenha uma consulta agendada para um horário em que o veterinário não estará mais disponível.

## Regras de Negócio
- Cancelamento com menos de 2h de antecedência pode gerar taxa
- Profissionais podem definir horários de bloqueio (folga, feriado)
- O agendamento não sobrepõe horários de profissionais
- Tutor pode reagendar via portal com até 24h de antecedência
- Alteração ou exclusão de turno de veterinário pode cancelar consultas agendadas automaticamente

---

## Diagrama do Processo

![Agendamento e Consulta](../diagrams/16-fluxo-agendamento.svg)
*Clique na imagem para ampliar. Diagrama de Atividades UML com raias — retângulos = atividades, losangos = decisão, setas = fluxo entre atividades, raias = atores.*
