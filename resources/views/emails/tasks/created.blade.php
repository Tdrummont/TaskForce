<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <title>Nova Tarefa Criada - Iron Force Tasks</title>
  </head>
  <body style="margin:0;padding:0;background-color:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#333333;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:20px 0;">
      <tr>
        <td align="center">
          <table width="600" border="0" cellspacing="0" cellpadding="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
            
            <!-- Header -->
            <tr>
              <td align="center" bgcolor="#4f46e5" style="padding:24px;color:#ffffff;">
                            <!-- Título TaskForce -->
            <div style="margin-bottom:16px;">
              <div style="font-size:24px;font-weight:700;color:#ffffff;text-shadow:1px 1px 2px rgba(0,0,0,0.3);">TASKFORCE</div>
            </div>
                <h1 style="margin:0;font-size:22px;font-weight:600;">🎯 Nova Tarefa Criada</h1>
              </td>
            </tr>
            
            <!-- Content -->
            <tr>
              <td style="padding:24px;font-size:15px;line-height:1.6;">
                <p style="margin:0 0 16px 0;">Olá <strong>{{ $user->name }}</strong>,</p>
                <p style="margin:0 0 16px 0;">
                  Você criou uma nova tarefa no sistema Iron Force Tasks. Aqui estão os detalhes:
                </p>
                
                <!-- Task Details -->
                <div style="background:#f8fafc;border-radius:6px;padding:20px;margin:20px 0;border-left:4px solid #4f46e5;">
                  <h3 style="margin:0 0 16px 0;color:#4f46e5;font-size:18px;">📋 Detalhes da Tarefa</h3>
                  
                  <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
                    <tr>
                      <td style="padding:8px 0;width:30%;font-weight:600;color:#6b7280;">📝 Título:</td>
                      <td style="padding:8px 0;color:#111827;">{{ $task->title }}</td>
                    </tr>
                    <tr>
                      <td style="padding:8px 0;font-weight:600;color:#6b7280;">📄 Descrição:</td>
                      <td style="padding:8px 0;color:#111827;">{{ $task->description ?: 'Sem descrição' }}</td>
                    </tr>
                    <tr>
                      <td style="padding:8px 0;font-weight:600;color:#6b7280;">⚡ Prioridade:</td>
                      <td style="padding:8px 0;">
                        @switch($task->priority)
                          @case('high')
                            <span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#fbeaea;color:#b91c1c;font-weight:700;font-size:12px;">🔴 Alta</span>
                            @break
                          @case('medium')
                            <span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#fff4e8;color:#b45309;font-weight:700;font-size:12px;">🟡 Média</span>
                            @break
                          @case('low')
                            <span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#eefaf5;color:#047857;font-weight:700;font-size:12px;">🟢 Baixa</span>
                            @break
                          @default
                            {{ ucfirst($task->priority) }}
                        @endswitch
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:8px 0;font-weight:600;color:#6b7280;">📊 Status:</td>
                      <td style="padding:8px 0;">
                        @switch($task->status)
                          @case('pending')
                            <span style="display:inline-block;padding:4px 10px;border-radius:6px;background:#f3f6fa;color:#4b5563;font-weight:700;font-size:12px;">⏳ Pendente</span>
                            @break
                          @case('in_progress')
                            <span style="display:inline-block;padding:4px 10px;border-radius:6px;background:#eef3ff;color:#1d4ed8;font-weight:700;font-size:12px;">🚀 Em progresso</span>
                            @break
                          @case('completed')
                            <span style="display:inline-block;padding:4px 10px;border-radius:6px;background:#eefaf5;color:#047857;font-weight:700;font-size:12px;">✅ Concluída</span>
                            @break
                          @default
                            {{ ucfirst($task->status) }}
                        @endswitch
                      </td>
                    </tr>
                    @if($task->due_date)
                    <tr>
                      <td style="padding:8px 0;font-weight:600;color:#6b7280;">📅 Data de Vencimento:</td>
                      <td style="padding:8px 0;color:#111827;">{{ $task->due_date->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                    @if($task->estimated_hours)
                    <tr>
                      <td style="padding:8px 0;font-weight:600;color:#6b7280;">⏱️ Horas Estimadas:</td>
                      <td style="padding:8px 0;color:#111827;">{{ $task->estimated_hours }}h</td>
                    </tr>
                    @endif
                    @if($task->category)
                    <tr>
                      <td style="padding:8px 0;font-weight:600;color:#6b7280;">🏷️ Categoria:</td>
                      <td style="padding:8px 0;color:#111827;">{{ $task->category }}</td>
                    </tr>
                    @endif
                    @if($task->tags && count($task->tags) > 0)
                    <tr>
                      <td style="padding:8px 0;font-weight:600;color:#6b7280;">🏷️ Tags:</td>
                      <td style="padding:8px 0;color:#111827;">
                        @foreach($task->tags as $tag)
                          <span style="display:inline-block;padding:2px 8px;border-radius:12px;background:#e5e7eb;color:#374151;font-size:11px;margin:2px;">#{{ $tag }}</span>
                        @endforeach
                      </td>
                    </tr>
                    @endif
                  </table>
                </div>

                <!-- Creation Info -->
                <div style="background:#f0fdf4;border-radius:6px;padding:20px;margin:20px 0;border-left:4px solid #10b981;">
                  <h3 style="margin:0 0 16px 0;color:#166534;font-size:18px;">ℹ️ Informações de Criação</h3>
                  
                  <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
                    <tr>
                      <td style="padding:8px 0;width:30%;font-weight:600;color:#166534;">📅 Criada em:</td>
                      <td style="padding:8px 0;color:#111827;">{{ $task->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                      <td style="padding:8px 0;font-weight:600;color:#166534;">👤 Por:</td>
                      <td style="padding:8px 0;color:#111827;">{{ $creator->name }}</td>
                    </tr>
                  </table>
                </div>
                
                <!-- Botão -->
                <table border="0" cellspacing="0" cellpadding="0" style="margin:20px 0;">
                  <tr>
                    <td align="center">
                      <a href="{{ url('/tasks/' . $task->id) }}" target="_blank" 
                         style="background:#4f46e5;color:#ffffff;padding:12px 24px;text-decoration:none;border-radius:6px;font-size:15px;display:inline-block;">
                        📋 Ver Tarefa
                      </a>
                    </td>
                  </tr>
                </table>

                <!-- Tips -->
                <div style="background:#fef3c7;border-radius:6px;padding:20px;margin:20px 0;border-left:4px solid #f59e0b;">
                  <h4 style="margin:0 0 12px 0;color:#92400e;font-size:16px;">💡 Dica</h4>
                  <p style="margin:0;color:#92400e;font-size:14px;">
                    Você pode acompanhar o progresso desta tarefa diretamente no sistema e receber notificações sobre atualizações.
                  </p>
                </div>
                
                <p style="margin-top:20px;font-size:14px;color:#555555;">
                  Esta notificação foi enviada automaticamente pelo sistema.
                </p>
              </td>
            </tr>
            
            <!-- Footer -->
            <tr>
              <td align="center" style="font-size:12px;color:#888888;padding:16px;border-top:1px solid #e5e7eb;">
                © 2025 — Iron Force Tasks. Todos os direitos reservados.
              </td>
            </tr>
            
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
