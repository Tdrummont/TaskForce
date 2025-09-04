<!DOCTYPE html>
<html lang="pt-BR" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Tarefa Delegada • Iron Force Tasks</title>

  <!-- Força Arial no Outlook -->
  <!--[if mso]>
  <style>* { font-family: Arial, sans-serif !important; }</style>
  <![endif]-->

  <style>
    /* Responsivo básico para Gmail iOS/Android e Apple Mail */
    @media (max-width:600px){
      .container{ width:100% !important; }
      .px-24{ padding-left:16px !important; padding-right:16px !important; }
      .py-24{ padding-top:16px !important; padding-bottom:16px !important; }
      .h1{ font-size:22px !important; line-height:1.3 !important; }
      .text{ font-size:15px !important; }
      .btn{ display:block !important; width:100% !important; }
    }
    /* Dark mode amigável (iOS/Android) */
    @media (prefers-color-scheme: dark) {
      .bg-body { background-color:#0b0b0c !important; }
      .bg-card { background-color:#161618 !important; }
      .h1, .text, .muted { color:#e6e6e6 !important; }
      .divider { border-color:#2a2a2a !important; }
      .chip-low   { background:#0e1f1a !important; color:#8cf0c4 !important; }
      .chip-med   { background:#2a1e0f !important; color:#ffd79a !important; }
      .chip-high  { background:#2a0f0f !important; color:#ffb3b3 !important; }
    }

    /* Neutraliza auto-link do iOS (telefones/datas) */
    a[x-apple-data-detectors], 
    u + #body a, 
    #MessageViewBody a {
      color: inherit !important;
      text-decoration: none !important;
      font-size: inherit !important;
      font-family: inherit !important;
      font-weight: inherit !important;
      line-height: inherit !important;
    }

    /* Outlook.com width fix */
    .ExternalClass { width:100%; }
    .ExternalClass, .ExternalClass * { line-height:100%; }
  </style>
</head>

<body id="body" class="bg-body" style="margin:0; padding:0; background-color:#f5f7fb;">
  <!-- Preheader invisível -->
  <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
    Uma nova tarefa foi delegada para você. Veja os detalhes e prossiga com os próximos passos.&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
  </div>

  <!-- Wrapper -->
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f5f7fb; border-collapse:collapse;">
    <tr>
      <td align="center" style="padding:24px;">
        <!-- Card -->
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" class="container bg-card" style="width:600px; max-width:600px; background-color:#ffffff; border-radius:14px; overflow:hidden;">
          <!-- Cabeçalho -->
          <tr>
            <td class="px-24 py-24" style="padding:20px 24px; border-bottom:1px solid #eef2f7;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td align="left" style="font-family:Arial,Helvetica,sans-serif; color:#0f172a; font-size:14px; font-weight:700;">
                    <img src="https://via.placeholder.com/120x40/4f46e5/ffffff?text=IRON+FORCE" alt="Iron Force Tasks" width="120" style="display:block; border:0;">
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
            <td class="px-24" style="padding:24px 24px 0;">
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
            <td class="px-24" style="padding:16px 24px 0;">
              <p class="text" style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:16px; line-height:1.6; color:#1f2937;">
                Você foi designado(a) para conduzir esta tarefa. Use o botão abaixo para acessá-la e atualizar o andamento.
              </p>
            </td>
          </tr>

          <!-- Bloco de contexto -->
          <tr>
            <td class="px-24" style="padding:20px 24px 0;">
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
          <tr>
            <td class="px-24" style="padding:16px 24px 0;">
              <hr class="divider" style="border:0; border-top:1px solid #eef2f7; margin:0;">
            </td>
          </tr>

          <!-- Detalhes da tarefa -->
          <tr>
            <td class="px-24" style="padding:16px 24px 0;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569; width:35%;"><strong>Título</strong></td>
                  <td align="right" style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;">{{ $task->title }}</td>
                </tr>
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Descrição</strong></td>
                  <td align="right" style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;">{{ $task->description ?: 'Não fornecida' }}</td>
                </tr>
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Prioridade</strong></td>
                  <td align="right" style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px;">
                    <!-- Chips simples (cores inline p/ Gmail) -->
                    @switch($task->priority)
                      @case('high')
                        <span class="chip-high" style="display:inline-block; padding:4px 10px; border-radius:999px; background-color:#fee2e2; color:#b91c1c; font-weight:700;">Alta</span>
                        @break
                      @case('medium')
                        <span class="chip-med" style="display:inline-block; padding:4px 10px; border-radius:999px; background-color:#fff7ed; color:#b45309; font-weight:700;">Média</span>
                        @break
                      @case('low')
                        <span class="chip-low" style="display:inline-block; padding:4px 10px; border-radius:999px; background-color:#ecfdf5; color:#047857; font-weight:700;">Baixa</span>
                        @break
                      @default
                        <span style="color:#475569;">{{ ucfirst($task->priority) }}</span>
                    @endswitch
                  </td>
                </tr>
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Status</strong></td>
                  <td align="right" style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px;">
                    @switch($task->status)
                      @case('pending')     <span style="display:inline-block; padding:4px 10px; border-radius:6px; background-color:#f1f5f9; color:#475569; font-weight:700;">Pendente</span> @break
                      @case('in_progress') <span style="display:inline-block; padding:4px 10px; border-radius:6px; background-color:#eff6ff; color:#1d4ed8; font-weight:700;">Em progresso</span> @break
                      @case('completed')   <span style="display:inline-block; padding:4px 10px; border-radius:6px; background-color:#ecfdf5; color:#047857; font-weight:700;">Concluída</span> @break
                      @default             <span style="color:#475569;">{{ ucfirst($task->status) }}</span>
                    @endswitch
                  </td>
                </tr>
                @if($task->category)
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Categoria</strong></td>
                  <td align="right" style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;">{{ $task->category }}</td>
                </tr>
                @endif
                @if($task->due_date)
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Vencimento</strong></td>
                  <td align="right" style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;">⏰ {{ $task->due_date->format('d/m/Y H:i') }}</td>
                </tr>
                @endif
                @if($task->estimated_hours)
                <tr>
                  <td style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#475569;"><strong>Estimativa</strong></td>
                  <td align="right" style="padding:8px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#0f172a;">⏱️ {{ $task->estimated_hours }}h</td>
                </tr>
                @endif
              </table>
            </td>
          </tr>

          <!-- CTA (botão bulletproof, 44px alto) -->
          <tr>
            <td align="center" style="padding:24px;">
              <!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" arcsize="12%" fillcolor="#4f46e5" stroked="f" style="height:48px;v-text-anchor:middle;width:280px;">
                <v:textbox inset="0,0,0,0">
                  <center style="color:#ffffff; font-family:Arial,sans-serif; font-size:16px; font-weight:700;">
                    Ver tarefa no sistema
                  </center>
                </v:textbox>
              </v:roundrect>
              <![endif]-->
              <!--[if !mso]><!-- -->
              <a href="{{ config('app.url') }}/pt/tasks"
                 class="btn"
                 style="background-color:#4f46e5; border-radius:10px; color:#ffffff; display:inline-block; font-family:Arial,Helvetica,sans-serif; font-size:16px; font-weight:700; line-height:48px; text-align:center; text-decoration:none; width:280px; -webkit-text-size-adjust:none;">
                Ver tarefa no sistema
              </a>
              <!--<![endif]-->
              <p class="muted" style="margin:10px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#64748b;">
                Caso o botão não funcione, copie e cole no navegador: {{ config('app.url') }}/pt/tasks
              </p>
            </td>
          </tr>

          <!-- Rodapé -->
          <tr>
            <td class="px-24" style="padding:12px 24px 24px; border-top:1px solid #eef2f7;">
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
