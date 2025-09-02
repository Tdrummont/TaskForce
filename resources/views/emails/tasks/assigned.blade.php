@component('mail::message')
# Nova Tarefa Atribuída

Olá **{{ $assignedTo->name }}**,

Você recebeu uma nova tarefa atribuída por **{{ $assignedBy->name }}**.

## Detalhes da Tarefa

**Título:** {{ $task->title }}

**Descrição:** {{ $task->description }}

**Prioridade:** {{ ucfirst($task->priority) }}

**Status:** {{ ucfirst($task->status) }}

@if($task->due_date)
**Data de Vencimento:** {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y H:i') }}
@endif

@if($task->tags)
**Tags:** {{ $task->tags }}
@endif

@component('mail::button', ['url' => $taskUrl, 'color' => 'primary'])
Ver Tarefa
@endcomponent

## Ações Necessárias

1. **Revisar** os detalhes da tarefa
2. **Atualizar** o status conforme necessário
3. **Adicionar** comentários ou anexos se precisar
4. **Comunicar** progresso para a equipe

Se você tiver alguma dúvida sobre esta tarefa, entre em contato com **{{ $assignedBy->name }}** ou responda a este email.

Obrigado,<br>
{{ config('app.name') }}

---
*Esta notificação foi enviada automaticamente pelo sistema de tarefas.*
@endcomponent 