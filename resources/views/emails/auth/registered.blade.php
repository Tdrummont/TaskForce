<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <title>Bem-vindo ao Task Force</title>
  </head>
  <body style="margin:0;padding:0;background-color:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#333333;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:20px 0;">
      <tr>
        <td align="center">
          <table width="600" border="0" cellspacing="0" cellpadding="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
            
            <!-- Header -->
            <tr>
              <td align="center" bgcolor="#059669" style="padding:24px;color:#ffffff;">
                <!-- Título TaskForce -->
                <div style="margin-bottom:16px;">
                  <div style="font-size:24px;font-weight:700;color:#ffffff;text-shadow:1px 1px 2px rgba(0,0,0,0.3);">TASKFORCE</div>
                </div>
                <h1 style="margin:0;font-size:22px;font-weight:600;">🎉 Bem-vindo ao Task Force!</h1>
              </td>
            </tr>
            
            <!-- Content -->
            <tr>
              <td style="padding:24px;font-size:15px;line-height:1.6;">
                <div style="text-align:center;margin-bottom:20px;">
                  <span style="display:inline-block;background:#059669;color:white;padding:8px 16px;border-radius:20px;font-weight:700;font-size:14px;">CADASTRO REALIZADO</span>
                </div>
                
                <p style="margin:0 0 16px 0;font-size:16px;">
                  Olá <strong>{{ $user->name }}</strong>!
                </p>
                
                <p style="margin:0 0 16px 0;">
                  Seu cadastro no <strong>Task Force</strong> foi realizado com sucesso! Estamos muito felizes em tê-lo(a) conosco.
                </p>
                
                <div style="background:#f0f9ff;border-left:4px solid #0ea5e9;padding:16px;margin:20px 0;border-radius:4px;">
                  <p style="margin:0;font-weight:600;color:#0c4a6e;">📧 Email cadastrado:</p>
                  <p style="margin:4px 0 0 0;color:#0c4a6e;">{{ $user->email }}</p>
                </div>
                
                <h3 style="color:#374151;margin:24px 0 16px 0;font-size:18px;">🚀 O que você pode fazer agora:</h3>
                
                <ul style="margin:0 0 20px 0;padding-left:20px;color:#4b5563;">
                  <li style="margin-bottom:8px;">Criar e gerenciar suas tarefas</li>
                  <li style="margin-bottom:8px;">Atribuir tarefas para outros membros da equipe</li>
                  <li style="margin-bottom:8px;">Acompanhar o progresso dos projetos</li>
                  <li style="margin-bottom:8px;">Receber notificações sobre atualizações</li>
                  <li style="margin-bottom:8px;">Colaborar com sua equipe de forma eficiente</li>
                </ul>
                
                <div style="text-align:center;margin:24px 0;">
                  <a href="{{ route('tasks.index', ['locale' => app()->getLocale()]) }}" 
                     style="display:inline-block;background:#059669;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:6px;font-weight:600;font-size:16px;">
                    🎯 Acessar Minhas Tarefas
                  </a>
                </div>
                
                <div style="background:#fef3c7;border:1px solid #f59e0b;padding:16px;margin:20px 0;border-radius:6px;">
                  <p style="margin:0;color:#92400e;font-weight:600;">💡 Dica:</p>
                  <p style="margin:4px 0 0 0;color:#92400e;">
                    Mantenha seu perfil sempre atualizado e configure suas preferências de notificação para receber alertas importantes.
                  </p>
                </div>
                
                <p style="margin:20px 0 0 0;color:#6b7280;font-size:14px;">
                  Se você não criou esta conta, por favor ignore este email ou entre em contato conosco.
                </p>
              </td>
            </tr>
            
            <!-- Footer -->
            <tr>
              <td bgcolor="#f9fafb" style="padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
                <p style="margin:0 0 8px 0;color:#6b7280;font-size:14px;">
                  <strong>Task Force</strong> - Sistema de Gerenciamento de Tarefas
                </p>
                <p style="margin:0;color:#9ca3af;font-size:12px;">
                  Este é um email automático, por favor não responda.
                </p>
                <div style="margin-top:12px;">
                  <a href="{{ route('tasks.index', ['locale' => app()->getLocale()]) }}" style="color:#059669;text-decoration:none;font-size:12px;margin:0 8px;">Início</a>
                  <span style="color:#d1d5db;">|</span>
                  <a href="#" style="color:#059669;text-decoration:none;font-size:12px;margin:0 8px;">Suporte</a>
                  <span style="color:#d1d5db;">|</span>
                  <a href="#" style="color:#059669;text-decoration:none;font-size:12px;margin:0 8px;">Privacidade</a>
                </div>
              </td>
            </tr>
            
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
