# Agendamento de Consultas

## Solicitar Nova Consulta

1. Acesse **Agendamento > Nova Consulta**
2. Selecione o **Pet** que será atendido

> **Preenchimento automático:** Se você acessou "Nova Consulta" a partir da ficha de um pet (clicando em **Agendar**), o pet já vem selecionado. Basta escolher o tipo, data e veterinário.
3. Escolha o **Tipo de atendimento** (consulta, retorno, vacina, exame, cirurgia)
4. Selecione uma **data** — o sistema mostra em tempo real quais veterinários estão disponíveis naquele dia

### Disponibilidade em Tempo Real

Ao escolher a data, o sistema exibe:

- **Veterinários disponíveis** — apenas os que têm turno (`is_vet_shift = true`) e horários livres na data e unidade selecionadas
- **Slots livres** — para cada veterinário, são mostrados apenas os horários que não conflitam com consultas já agendadas nem com folgas aprovadas
- **Datas com vaga** — no calendário, as datas que possuem ao menos um slot livre para o veterinário escolhido ficam destacadas

5. Selecione o **veterinário** desejado
6. Escolha o **horário** disponível
7. Preencha as **observações** (ex: "vomitar há 2 dias", "revisão pós-cirúrgica")
8. Clique em **Agendar**

### Exemplo

```
Data: 15/06/2026
Veterinários disponíveis:
  🩺 Dr. Carlos (CRMV 12345) — Horários: 08:00, 08:30, 09:00, 10:30, 11:00
  🩺 Dra. Ana (CRMV 67890)   — Horários: 13:00, 13:30, 14:00
```

> **Nota**: Se nenhum veterinário estiver disponível na data escolhida, o sistema informa e sugere selecionar outra data.

## Confirmação

Após agendar:

1. A consulta aparece com status **Agendada**
2. Você recebe uma **confirmação** por WhatsApp/SMS/E-mail (de acordo com suas preferências de notificação)
3. Caso a clínica altere o turno do veterinário e seu horário não esteja mais disponível, a consulta é **cancelada automaticamente** com aviso

## Consultas Agendadas

- Acesse **Agendamento > Minhas Consultas**
- Veja todas as consultas futuras e passadas
- Cada consulta mostra: **pet**, **data**, **horário**, **veterinário**, **status**

### Status da Consulta

| Status | Significado |
|--------|-------------|
| **Agendada** | Horário reservado! |
| **Confirmada** | Clínica confirmou presença |
| **Realizada** | Atendimento concluído |
| **Cancelada** | Consulta cancelada (por você ou pela clínica) |

## Reagendar

- Você pode reagendar com até **24h de antecedência**
- Basta acessar a consulta e clicar em **Reagendar**
- O sistema mostra novamente a disponibilidade em tempo real

## Cancelar

- **Com mais de 24h**: cancele sem custos
- **Menos de 24h**: a clínica pode cobrar taxa de cancelamento
- Para cancelar: acesse a consulta e clique em **Cancelar**

## Regras Importantes

- Chegue **15 minutos antes** do horário agendado
- Se atrasar mais de 15 minutos, a clínica pode remarcar
- Em caso de emergência, ligue diretamente para a clínica
- O agendamento online é apenas para consultas eletivas
- Se a clínica alterar a escala do veterinário, a consulta pode ser cancelada automaticamente — você receberá uma notificação
