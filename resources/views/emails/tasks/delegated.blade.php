<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Tarefa Delegada • Iron Force Tasks</title>
  <!-- Preheader: aparece como prévia no inbox -->
  <meta name="x-preheader" content="Uma nova tarefa foi delegada para você. Veja os detalhes e prossiga com os próximos passos.">
  <style>
    /* Ajustes responsivos leves (suportados por Gmail iOS/Android e Apple Mail) */
    @media (max-width:600px){
      .container{ width:100% !important; }
      .px-24{ padding-left:16px !important; padding-right:16px !important; }
      .py-24{ padding-top:16px !important; padding-bottom:16px !important; }
      .h1{ font-size:22px !important; }
      .text{ font-size:15px !important; }
      .btn{ display:block !important; width:100% !important; }
    }
    /* Suaviza dark mode agressivo em alguns clientes */
    @media (prefers-color-scheme: dark) {
      .bg-body { background-color:#0b0b0c !important; }
      .bg-card { background-color:#161618 !important; }
      .text, .muted { color:#e6e6e6 !important; }
      .divider { border-color:#2a2a2a !important; }
    }
  </style>
</head>
<body class="bg-body" style="margin:0; padding:0; background-color:#f5f7fb;">
  <!-- Preheader invisível -->
  <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
    Uma nova tarefa foi delegada para você. Veja os detalhes e prossiga com os próximos passos.
  </div>

  <!-- Wrapper -->
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f5f7fb;">
    <tr>
      <td align="center" style="padding:24px;">
        <!-- Card -->
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" class="container bg-card" style="width:600px; max-width:600px; background-color:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 6px 24px rgba(16,24,40,0.06);">
          <!-- Cabeçalho com logo/nome -->
          <tr>
            <td style="padding:20px 24px; border-bottom:1px solid #eef2f7;" class="px-24 py-24">
              <table role="presentation" width="100%">
                <tr>
                  <td align="left" style="font-family:Arial,Helvetica,sans-serif; color:#0f172a; font-size:14px; font-weight:700;">
                    <!-- Se tiver logo, substitua a linha abaixo por uma <img> -->
                    Iron Force Tasks
                  </td>
                  <td align="right" style="font-family:Arial,Helvetica,sans-serif; color:#64748b; font-size:12px;">
                    {{ now()->format('d/m/Y H:i') }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Título -->
          <tr>
            <td style="padding:24px 24px 0;" class="px-24">
              <h1 class="h1" style="margin:0; font-family:Arial,Helvetica,sans-serif; font-weight:700; font-size:24px; line-height:1.25; color:#0f172a;">
                🔄 Tarefa delegada para você
              </h1>
              <p class="muted" style="margin:8px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:13px; color:#64748b;">
                Olá <strong style="color:#0f172a;">{{ $delegatedTo->name }}</strong>, confira os detalhes abaixo.
              </p>
            </td>
          </tr>

          <!-- Mensagem principal -->
          <tr>
            <td style="padding:16px 24px 0;" class="px-24">
              <p class="text" style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:16px; line-height:1.6; color:#1f2937;">
                Você foi designado(a) para conduzir esta tarefa. Utilize o botão abaixo para acessá-la e atualizar o andamento.
              </p>
            </td>
          </tr>

          <!-- Bloco de contexto (quem delegou) -->
          <tr>
            <td style="padding:20px 24px 0;" class="px-24">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8fafc; border:1px solid #e5e7eb; border-radius:10px;">
                <tr>
                  <td style="padding:14px 16px;">
                    <table role="presentation" width="100%">
                      <tr>
                        <td style="font-family:Arial,Helvetica,sans-serif; font-size:13px; color:#334155;">
                          <strong>Delegado por:</strong> {{ $delegatedBy->name }}
                        </td>
                        <td align="right" style="font-family:Arial,Helvetica,sans-serif; font-size:13px; color:#334155;">
                          <strong>Referência:</strong> #{{ $task->id }}
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" style="padding-top:6px; font-family:Arial,Helvetica,sans-serif; font-size:13px; color:#334155;">
                          <strong>Motivo:</strong> Alocação direcionada para execução desta atividade.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Divisor -->
          <tr><td style="padding:16px 24px 0;" class="px-24"><hr class="divider" style="border:0; border-top:1px solid #eef2f7; margin:0;"></td></tr>

          <!-- Detalhes da tarefa -->
          <tr>
            <td style="padding:16px 24px 0;" class="px-24">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569; width:35%;"><strong>Título</strong></td>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;" align="right">{{ $task->title }}</td>
                </tr>
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Descrição</strong></td>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;" align="right">{{ $task->description ?: 'Não fornecida' }}</td>
                </tr>
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Prioridade</strong></td>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;" align="right">
                    @switch($task->priority)
                      @case('high') <span style="display:inline-block; padding:4px 10px; border-radius:999px; background-color:#fee2e2; color:#b91c1c; font-weight:700;">Alta</span> @break
                      @case('medium') <span style="display:inline-block; padding:4px 10px; border-radius:999px; background-color:#fff7ed; color:#b45309; font-weight:700;">Média</span> @break
                      @case('low') <span style="display:inline-block; padding:4px 10px; border-radius:999px; background-color:#ecfdf5; color:#047857; font-weight:700;">Baixa</span> @break
                      @default <span style="color:#475569;">{{ ucfirst($task->priority) }}</span>
                    @endswitch
                  </td>
                </tr>
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Status</strong></td>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;" align="right">
                    @switch($task->status)
                      @case('pending') <span style="display:inline-block; padding:4px 10px; border-radius:6px; background-color:#f1f5f9; color:#475569; font-weight:700;">Pendente</span> @break
                      @case('in_progress') <span style="display:inline-block; padding:4px 10px; border-radius:6px; background-color:#eff6ff; color:#1d4ed8; font-weight:700;">Em progresso</span> @break
                      @case('completed') <span style="display:inline-block; padding:4px 10px; border-radius:6px; background-color:#ecfdf5; color:#047857; font-weight:700;">Concluída</span> @break
                      @default <span style="color:#475569;">{{ ucfirst($task->status) }}</span>
                    @endswitch
                  </td>
                </tr>
                @if($task->category)
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Categoria</strong></td>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;" align="right">{{ $task->category }}</td>
                </tr>
                @endif
                @if($task->due_date)
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Vencimento</strong></td>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;" align="right">⏰ {{ $task->due_date->format('d/m/Y H:i') }}</td>
                </tr>
                @endif
                @if($task->estimated_hours)
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Estimativa</strong></td>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;" align="right">⏱️ {{ $task->estimated_hours }}h</td>
                </tr>
                @endif
              </table>
            </td>
          </tr>

          <!-- CTA -->
          <tr>
            <td align="center" style="padding:24px;">
              <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td bgcolor="#4f46e5" style="border-radius:10px;">
                    <a href="{{ route('tasks.index') }}"
                       class="btn"
                       style="display:inline-block; padding:14px 22px; font-family:Arial,Helvetica,sans-serif; font-size:16px; color:#ffffff; text-decoration:none; border-radius:10px; background-color:#4f46e5;">
                      Ver tarefa no sistema
                    </a>
                  </td>
                </tr>
              </table>
              <!-- <p class="muted" style="margin:10px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#64748b;">
                Caso o botão não funcione, copie e cole no navegador: {{ route('tasks.index') }}
              </p> -->
            </td>
          </tr>

          <!-- Rodapé -->
          <tr>
            <td style="padding:12px 24px 24px; border-top:1px solid #eef2f7;" class="px-24">
              <p class="muted" style="margin:8px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#64748b;">
                Atenciosamente,<br>
                <span style="color:#4f46e5; font-weight:700;">Equipe Iron Force Tasks</span>
              </p>
              <p class="muted" style="margin:8px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:11px; color:#94a3b8;">
                Este é um e-mail automático, não responda.
              </p>
            </td>
          </tr>
        </table>
        <!-- /Card -->
      </td>
    </tr>
  </table>
</body>
</html>
