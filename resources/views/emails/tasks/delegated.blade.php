<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarefa Delegada - Iron Force Tasks</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #6366f1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #6366f1;
            margin: 0;
            font-size: 28px;
        }
        .header .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin-top: 5px;
        }
        .greeting {
            font-size: 18px;
            color: #374151;
            margin-bottom: 25px;
        }
        .task-details {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .task-details h3 {
            color: #374151;
            margin-top: 0;
            border-bottom: 2px solid #d1d5db;
            padding-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-label {
            font-weight: 600;
            color: #4b5563;
        }
        .detail-value {
            color: #374151;
        }
        .priority-high { color: #dc2626; font-weight: 600; }
        .priority-medium { color: #d97706; font-weight: 600; }
        .priority-low { color: #059669; font-weight: 600; }
        .status-pending { color: #d97706; font-weight: 600; }
        .status-in_progress { color: #2563eb; font-weight: 600; }
        .status-completed { color: #059669; font-weight: 600; }
        .delegation-info {
            background-color: #dbeafe;
            border: 1px solid #93c5fd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .delegation-info h4 {
            color: #1e40af;
            margin-top: 0;
        }
        .delegation-info p {
            color: #1e3a8a;
            margin: 5px 0;
        }
        .action-button {
            display: inline-block;
            background-color: #6366f1;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .action-button:hover {
            background-color: #4f46e5;
        }
        .next-steps {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps h4 {
            color: #166534;
            margin-top: 0;
        }
        .next-steps ul {
            color: #166534;
            margin: 10px 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .contact-info {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .contact-info p {
            color: #92400e;
            margin: 5px 0;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Tarefa Delegada</h1>
            <div class="subtitle">Iron Force Tasks</div>
        </div>

        <div class="greeting">
            Olá <strong>{{ $delegatedTo->name }}</strong>!
        </div>

        <p>Uma tarefa foi <strong>delegada especificamente para você</strong>! Isso significa que você foi escolhido para executar esta tarefa.</p>

        <div class="delegation-info">
            <h4>📋 Informações da Delegação</h4>
            <p><strong>Delegada por:</strong> {{ $delegatedBy->name }}</p>
            <p><strong>Data da delegação:</strong> {{ now()->format('d/m/Y H:i') }}</p>
            <p><strong>Motivo:</strong> Você foi selecionado para executar esta tarefa</p>
        </div>

        <div class="task-details">
            <h3>📝 Detalhes da Tarefa</h3>
            
            <div class="detail-row">
                <span class="detail-label">Título:</span>
                <span class="detail-value">{{ $task->title }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Descrição:</span>
                <span class="detail-value">{{ $task->description ?: 'Não fornecida' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Prioridade:</span>
                <span class="detail-value priority-{{ $task->priority }}">
                    @switch($task->priority)
                        @case('high')
                            🔴 Alta
                            @break
                        @case('medium')
                            🟡 Média
                            @break
                        @case('low')
                            🟢 Baixa
                            @break
                        @default
                            ⚪ {{ ucfirst($task->priority) }}
                    @endswitch
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value status-{{ $task->status }}">
                    @switch($task->status)
                        @case('pending')
                            🟡 Pendente
                            @break
                        @case('in_progress')
                            🔵 Em Progresso
                            @break
                        @case('completed')
                            🟢 Concluída
                            @break
                        @default
                            ⚪ {{ ucfirst($task->status) }}
                    @endswitch
                </span>
            </div>
            
            @if($task->category)
            <div class="detail-row">
                <span class="detail-label">Categoria:</span>
                <span class="detail-value">🏷️ {{ $task->category }}</span>
            </div>
            @endif
            
            @if($task->due_date)
            <div class="detail-row">
                <span class="detail-label">Data de Vencimento:</span>
                <span class="detail-value">⏰ {{ $task->due_date->format('d/m/Y H:i') }}</span>
            </div>
            @endif
            
            @if($task->estimated_hours)
            <div class="detail-row">
                <span class="detail-label">Horas Estimadas:</span>
                <span class="detail-value">⏱️ {{ $task->estimated_hours }}h</span>
            </div>
            @endif
        </div>

        <div class="next-steps">
            <h4>🚀 Próximos Passos</h4>
            <ul>
                <li>Acesse o sistema para ver os detalhes completos da tarefa</li>
                <li>Atualize o status conforme o progresso</li>
                <li>Entre em contato com {{ $delegatedBy->name }} se tiver dúvidas</li>
                <li>Mantenha o status atualizado para melhor acompanhamento</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('tasks.index') }}" class="action-button">
                📋 Ver Tarefa Completa
            </a>
        </div>

        <div class="contact-info">
            <p>💡 <strong>Precisa de ajuda?</strong></p>
            <p>Entre em contato com {{ $delegatedBy->name }}</p>
        </div>

        <div class="footer">
            <p><strong>Atenciosamente,</strong></p>
            <p>Equipe Iron Force Tasks</p>
            <p>Este é um email automático, não responda diretamente.</p>
        </div>
    </div>
</body>
</html> 