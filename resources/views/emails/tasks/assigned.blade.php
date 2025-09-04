<!DOCTYPE html>
<html lang="pt-BR" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width"><meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Tarefa Atribuída • Iron Force Tasks</title>
  <!--[if mso]><style>* { font-family: Arial, sans-serif !important; }</style><![endif]-->
  <style>
    /* Mobile-first para Gmail iOS/Android e Apple Mail */
    @media (max-width:600px){
      .container{ width:100% !important; }
      .px{ padding-left:20px !important; padding-right:20px !important; }
      .py{ padding-top:20px !important; padding-bottom:20px !important; }
      .h1{ font-size:22px !important; line-height:1.35 !important; }
      .lead{ font-size:15px !important; }
      .btn{ display:block !important; width:100% !important; }
    }
    /* Dark mode "amigável" */
    @media (prefers-color-scheme: dark){
      .bg-body { background:#0b0b0c !important; }
      .bg-card { background:#151517 !important; }
      .text, .muted, .h1 { color:#e6e6e6 !important; }
      .rule { border-color:#2a2a2a !important; }
      .kv-label { color:#a8b2c1 !important; }
    }
    /* Neutraliza autolink do iOS */
    a[x-apple-data-detectors], u + #body a, #MessageViewBody a {
      color: inherit !important; text-decoration: none !important;
      font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important;
    }
    .ExternalClass { width:100%; } .ExternalClass, .ExternalClass * { line-height:100%; }
  </style>
</head>
<body id="body" class="bg-body" style="margin:0; padding:0; background:#f6f8fb;">
  <!-- Preheader invisível -->
  <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
    Uma nova tarefa foi atribuída para você. Acesse os detalhes e prossiga com os próximos passos.&nbsp;&zwnj;&nbsp;&zwnj;
  </div>

  <!-- Wrapper -->
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f6f8fb; border-collapse:collapse;">
    <tr>
      <td align="center" style="padding:28px;">
        <!-- Card -->
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" class="container bg-card" style="width:600px; max-width:600px; background:#ffffff; border-radius:16px; overflow:hidden;">
          <!-- Top bar discreta -->
          <tr><td style="height:4px; background:#4338ca;"></td></tr>

          <!-- Cabeçalho -->
          <tr>
            <td class="px py" style="padding:24px 28px; border-bottom:1px solid #eef1f6;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td align="left" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; color:#0f172a; font-size:14px; font-weight:700;">
                    <!-- Opcional: logo -->
                    <!-- <img src="https://SEU-LOGO.png" width="130" alt="Iron Force Tasks" style="display:block; border:0;"> -->
                    Iron Force Tasks
                  </td>
                  <td align="right" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; color:#6b7280; font-size:12px;">
                    {{ now()->format('d/m/Y H:i') }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Título + resumo -->
          <tr>
            <td class="px" style="padding:28px;">
              <h1 class="h1" style="margin:0 0 8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-weight:700; font-size:24px; line-height:1.3; color:#0f172a;">
                Tarefa atribuída
              </h1>
              <p class="lead" style="margin:0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:16px; line-height:1.6; color:#374151;">
                Olá <strong style="color:#111827;">{{ $assignedTo->name }}</strong>, você foi designado(a) para conduzir a atividade abaixo.
              </p>
            </td>
          </tr>

          <!-- Contexto (quem atribuiu / ref) -->
          <tr>
            <td class="px" style="padding:0 28px 6px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f9fbfd; border:1px solid #e7ecf3; border-radius:12px;">
                <tr>
                  <td style="padding:14px 16px;">
                    <table role="presentation" width="100%">
                      <tr>
                        <td class="text" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px; color:#374151;">
                          <strong>Atribuído por</strong> {{ $assignedBy->name }}
                        </td>
                        <td align="right" class="text" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px; color:#374151;">
                          <strong>Ref.</strong> #{{ $task->id }}
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" style="padding-top:6px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:13px; color:#4b5563;">
                          Finalidade: direcionar a execução com prioridade e clareza de responsabilidade.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Regra -->
          <tr><td class="px" style="padding:18px 28px 0;"><hr class="rule" style="border:0; border-top:1px solid #eef1f6; margin:0;"></td></tr>

          <!-- Detalhes (kv list) -->
          <tr>
            <td class="px" style="padding:18px 28px 0;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td class="kv-label" style="padding:8px 0; width:36%; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px; color:#6b7280;">Título</td>
                  <td align="right" class="text" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:15px; color:#111827;">{{ $task->title }}</td>
                </tr>
                <tr>
                  <td class="kv-label" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px; color:#6b7280;">Descrição</td>
                  <td align="right" class="text" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:15px; color:#111827;">{{ $task->description ?: '—' }}</td>
                </tr>
                <tr>
                  <td class="kv-label" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px; color:#6b7280;">Prioridade</td>
                  <td align="right" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px;">
                    @switch($task->priority)
                      @case('high')   <span style="display:inline-block; padding:4px 10px; border-radius:999px; background:#fbeaea; color:#b91c1c; font-weight:700;">Alta</span> @break
                      @case('medium') <span style="display:inline-block; padding:4px 10px; border-radius:999px; background:#fff4e8; color:#b45309; font-weight:700;">Média</span> @break
                      @case('low')    <span style="display:inline-block; padding:4px 10px; border-radius:999px; background:#eefaf5; color:#047857; font-weight:700;">Baixa</span> @break
                      @default        <span style="color:#6b7280;">{{ ucfirst($task->priority) }}</span>
                    @endswitch
                  </td>
                </tr>
                <tr>
                  <td class="kv-label" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px; color:#6b7280;">Status</td>
                  <td align="right" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px;">
                    @switch($task->status)
                      @case('pending')     <span style="display:inline-block; padding:4px 10px; border-radius:6px; background:#f3f6fa; color:#4b5563; font-weight:700;">Pendente</span> @break
                      @case('in_progress') <span style="display:inline-block; padding:4px 10px; border-radius:6px; background:#eef3ff; color:#1d4ed8; font-weight:700;">Em progresso</span> @break
                      @case('completed')   <span style="display:inline-block; padding:4px 10px; border-radius:6px; background:#eefaf5; color:#047857; font-weight:700;">Concluída</span> @break
                      @default             <span style="color:#6b7280;">{{ ucfirst($task->status) }}</span>
                    @endswitch
                  </td>
                </tr>
                @if($task->category)
                <tr>
                  <td class="kv-label" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px; color:#6b7280;">Categoria</td>
                  <td align="right" class="text" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:15px; color:#111827;">{{ $task->category }}</td>
                </tr>
                @endif
                @if($task->due_date)
                <tr>
                  <td class="kv-label" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px; color:#6b7280;">Vencimento</td>
                  <td align="right" class="text" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:15px; color:#111827;">{{ $task->due_date->format('d/m/Y H:i') }}</td>
                </tr>
                @endif
                @if($task->estimated_hours)
                <tr>
                  <td class="kv-label" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:14px; color:#6b7280;">Estimativa</td>
                  <td align="right" class="text" style="padding:8px 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:15px; color:#111827;">{{ $task->estimated_hours }}h</td>
                </tr>
                @endif
              </table>
            </td>
          </tr>

          <!-- CTA -->
          <tr>
            <td align="center" style="padding:28px;">
              <!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" arcsize="10%" fillcolor="#4338ca" strokecolor="#4338ca" strokeweight="1px" style="height:48px;v-text-anchor:middle;width:280px;">
                <v:textbox inset="0,0,0,0">
                  <center style="color:#ffffff; font-family:Arial, sans-serif; font-size:16px; font-weight:700;">
                    Abrir tarefa
                  </center>
                </v:textbox>
              </v:roundrect>
              <![endif]-->
              <!--[if !mso]><!-- -->
              <a href="{{ config('app.url') }}/pt/tasks"
                 class="btn"
                 aria-label="Abrir tarefa no sistema"
                 style="background:#4338ca; border-radius:10px; color:#ffffff; display:inline-block; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:16px; font-weight:700; line-height:48px; text-align:center; text-decoration:none; width:280px; -webkit-text-size-adjust:none;">
                 Abrir tarefa
              </a>
              <!--<![endif]-->
              <p class="muted" style="margin:10px 0 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:12px; color:#6b7280;">
                Se o botão não funcionar, acesse: {{ config('app.url') }}/pt/tasks
              </p>
            </td>
          </tr>

          <!-- Rodapé -->
          <tr>
            <td class="px py" style="padding:16px 28px 28px; border-top:1px solid #eef1f6;">
              <table role="presentation" width="100%">
                <tr>
                  <td style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:12px; color:#6b7280;">
                    Atenciosamente,<br>
                    <span style="color:#4338ca; font-weight:700;">Equipe Iron Force Tasks</span>
                  </td>
                  <td align="right" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:12px; color:#9ca3af;">
                    <a href="{{ config('app.url') }}/pt/suporte" style="color:#6b7280; text-decoration:none;">Suporte</a>
                  </td>
                </tr>
              </table>
              <p class="muted" style="margin:10px 0 0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; font-size:11px; color:#9ca3af;">
                Este é um e-mail automático. Não responda a esta mensagem.
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