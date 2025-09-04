<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarefa Editada - {{ $task->title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #3b82f6;
            margin: 0;
            font-size: 24px;
        }
        .task-info {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        .task-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
        }
        .task-details {
            margin: 15px 0;
        }
        .task-details strong {
            color: #374151;
        }
        .changes-section {
            background-color: #fef3c7;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
        }
        .changes-section h3 {
            color: #92400e;
            margin-top: 0;
        }
        .change-item {
            margin: 10px 0;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 5px;
            border: 1px solid #fbbf24;
        }
        .change-field {
            font-weight: bold;
            color: #92400e;
        }
        .change-old {
            color: #dc2626;
            text-decoration: line-through;
        }
        .change-new {
            color: #059669;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-in_progress { background-color: #dbeafe; color: #1e40af; }
        .status-completed { background-color: #d1fae5; color: #065f46; }
        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .priority-low { background-color: #d1fae5; color: #065f46; }
        .priority-medium { background-color: #fef3c7; color: #92400e; }
        .priority-high { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Tarefa Editada</h1>
        </div>

        <p>Olá <strong>{{ $recipient->name }}</strong>!</p>
        
        <p>A tarefa <strong>"{{ $task->title }}"</strong> foi editada por <strong>{{ $editedBy->name }}</strong>.</p>

        <div class="task-info">
            <div class="task-title">{{ $task->title }}</div>
            <div class="task-details">
                <strong>Descrição:</strong> {{ $task->description ?: 'Sem descrição' }}<br>
                <strong>Status:</strong> 
                <span class="status-badge status-{{ $task->status }}">
                    @switch($task->status)
                        @case('pending') Pendente @break
                        @case('in_progress') Em Progresso @break
                        @case('completed') Concluída @break
                        @default {{ $task->status }}
                    @endswitch
                </span><br>
                <strong>Prioridade:</strong> 
                <span class="priority-badge priority-{{ $task->priority }}">
                    @switch($task->priority)
                        @case('low') Baixa @break
                        @case('medium') Média @break
                        @case('high') Alta @break
                        @default {{ $task->priority }}
                    @endswitch
                </span><br>
                <strong>Data de vencimento:</strong> 
                {{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Não definida' }}<br>
                @if($task->assignedTo)
                    <strong>Responsável:</strong> {{ $task->assignedTo->name }}<br>
                @endif
            </div>
        </div>

        @if(!empty($changes))
        <div class="changes-section">
            <h3>🔄 Alterações Realizadas</h3>
            @foreach($changes as $field => $change)
            <div class="change-item">
                <span class="change-field">{{ ucfirst($field) }}:</span>
                <span class="change-old">{{ $change['old'] }}</span> 
                → 
                <span class="change-new">{{ $change['new'] }}</span>
            </div>
            @endforeach
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ url("/pt/tasks/{$task->id}") }}" class="button">
                Ver Tarefa
            </a>
        </div>

        <div class="footer">
            <p>Este é um email automático do sistema TaskForce.</p>
            <p>Se você não esperava receber este email, pode ignorá-lo com segurança.</p>
        </div>
    </div>
</body>
</html>
