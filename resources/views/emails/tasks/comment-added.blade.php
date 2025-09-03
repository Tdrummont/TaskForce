<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Comentário - Iron Force Tasks</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }
        .container {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #8b5cf6, #7c3aed, #6d28d9);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        .header h1 {
            color: #7c3aed;
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .header .subtitle {
            color: #6b7280;
            font-size: 18px;
            margin-top: 8px;
            font-weight: 500;
        }
        .greeting {
            font-size: 20px;
            color: #374151;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #f3e8ff, #ede9fe);
            border-radius: 12px;
            border-left: 4px solid #8b5cf6;
        }
        .comment-section {
            background: linear-gradient(135deg, #faf5ff, #f3e8ff);
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            border: 1px solid #e9d5ff;
        }
        .comment-section h3 {
            color: #7c3aed;
            margin-top: 0;
            font-size: 22px;
            border-bottom: 2px solid #e9d5ff;
            padding-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .comment-content {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #e9d5ff;
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.1);
        }
        .comment-author {
            font-weight: 600;
            color: #7c3aed;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .comment-text {
            color: #374151;
            font-size: 16px;
            line-height: 1.6;
            margin: 0;
        }
        .comment-time {
            color: #6b7280;
            font-size: 14px;
            margin-top: 15px;
            font-style: italic;
        }
        .task-details {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            border: 1px solid #e2e8f0;
        }
        .task-details h3 {
            color: #7c3aed;
            margin-top: 0;
            font-size: 22px;
            border-bottom: 2px solid #e9d5ff;
            padding-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            align-items: center;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #475569;
            font-size: 16px;
        }
        .detail-value {
            color: #1f2937;
            font-weight: 500;
            text-align: right;
            max-width: 60%;
        }
        .priority-high { 
            color: #dc2626; 
            font-weight: 700; 
            background: #fef2f2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .priority-medium { 
            color: #d97706; 
            font-weight: 700; 
            background: #fffbeb;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .priority-low { 
            color: #059669; 
            font-weight: 700; 
            background: #f0fdf4;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .status-pending { 
            color: #d97706; 
            font-weight: 700; 
            background: #fffbeb;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .status-in_progress { 
            color: #2563eb; 
            font-weight: 700; 
            background: #eff6ff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .status-completed { 
            color: #059669; 
            font-weight: 700; 
            background: #f0fdf4;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: #ffffff;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 18px;
            margin: 30px 0;
            text-align: center;
            box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.3);
            transition: all 0.3s ease;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(139, 92, 246, 0.4);
        }
        .next-steps {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border: 1px solid #93c5fd;
            border-radius: 16px;
            padding: 25px;
            margin: 30px 0;
        }
        .next-steps h4 {
            color: #1e40af;
            margin-top: 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .next-steps ul {
            color: #1e40af;
            margin: 15px 0;
            padding-left: 25px;
        }
        .next-steps li {
            margin: 10px 0;
            font-weight: 500;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .emoji {
            font-size: 20px;
            margin-right: 8px;
        }
        .collaboration-info {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #fcd34d;
            border-radius: 16px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        .collaboration-info p {
            color: #92400e;
            margin: 8px 0;
            font-weight: 600;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 Novo Comentário</h1>
            <div class="subtitle">Iron Force Tasks</div>
        </div>

        <div class="greeting">
            Olá <strong>{{ $user->name }}</strong>! 👋
        </div>

        <p style="font-size: 18px; color: #374151; margin-bottom: 30px;">
            Um novo comentário foi adicionado à tarefa <strong>"{{ $task->title }}"</strong>. 
            Mantenha-se atualizado com as discussões da equipe! 💭
        </p>

        <div class="comment-section">
            <h3><span class="emoji">💬</span>Comentário Adicionado</h3>
            
            <div class="comment-content">
                <div class="comment-author">
                    👤 <strong>{{ $comment->user->name }}</strong>
                </div>
                <div class="comment-text">
                    {{ $comment->content }}
                </div>
                <div class="comment-time">
                    📅 {{ $comment->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="task-details">
            <h3><span class="emoji">📋</span>Detalhes da Tarefa</h3>
            
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
            
            @if($task->due_date)
            <div class="detail-row">
                <span class="detail-label">Data de Vencimento:</span>
                <span class="detail-value">⏰ {{ $task->due_date->format('d/m/Y H:i') }}</span>
            </div>
            @endif
            
            @if($task->assigned_to)
            <div class="detail-row">
                <span class="detail-label">Responsável:</span>
                <span class="detail-value">👤 {{ $task->assigned_to->name }}</span>
            </div>
            @endif
        </div>

        <div class="next-steps">
            <h4><span class="emoji">🚀</span>Próximos Passos</h4>
            <ul>
                <li><strong>Revisar</strong> o comentário adicionado</li>
                <li><strong>Responder</strong> se necessário</li>
                <li><strong>Atualizar</strong> o status da tarefa se aplicável</li>
                <li><strong>Comunicar</strong> progresso para a equipe</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ $taskUrl }}" class="action-button">
                💬 Ver Tarefa e Comentários
            </a>
        </div>

        <div class="collaboration-info">
            <p>💡 <strong>Dica de Colaboração</strong></p>
            <p>Comentários ajudam a manter a equipe alinhada e documentam decisões importantes!</p>
        </div>

        <div class="footer">
            <p><strong>Atenciosamente,</strong></p>
            <p style="font-size: 18px; color: #7c3aed; font-weight: 600;">Equipe Iron Force Tasks</p>
            <p>Esta notificação foi enviada automaticamente pelo sistema.</p>
        </div>
    </div>
</body>
</html>
