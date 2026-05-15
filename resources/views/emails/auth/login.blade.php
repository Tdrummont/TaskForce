<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Detectado - YggdraTask  Tasks</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #f59e0b 0%, #dc2626 100%);
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
            background: linear-gradient(90deg, #f59e0b, #dc2626, #7c2d12);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        .header h1 {
            color: #dc2626;
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #f59e0b, #dc2626);
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
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 12px;
            border-left: 4px solid #f59e0b;
        }
        .login-details {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            border: 1px solid #fecaca;
        }
        .login-details h3 {
            color: #dc2626;
            margin-top: 0;
            font-size: 22px;
            border-bottom: 2px solid #fecaca;
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
            border-bottom: 1px solid #fecaca;
            align-items: center;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #991b1b;
            font-size: 16px;
        }
        .detail-value {
            color: #1f2937;
            font-weight: 500;
            text-align: right;
            max-width: 60%;
            word-break: break-word;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b, #dc2626);
            color: #ffffff;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 18px;
            margin: 30px 0;
            text-align: center;
            box-shadow: 0 10px 15px -3px rgba(220, 38, 38, 0.3);
            transition: all 0.3s ease;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(220, 38, 38, 0.4);
        }
        .security-steps {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 1px solid #86efac;
            border-radius: 16px;
            padding: 25px;
            margin: 30px 0;
        }
        .security-steps h4 {
            color: #166534;
            margin-top: 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .security-steps ul {
            color: #166534;
            margin: 15px 0;
            padding-left: 25px;
        }
        .security-steps li {
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
        .warning-info {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #fcd34d;
            border-radius: 16px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        .warning-info p {
            color: #92400e;
            margin: 8px 0;
            font-weight: 600;
            font-size: 16px;
        }
        .emoji {
            font-size: 20px;
            margin-right: 8px;
        }
        .alert-badge {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Login Detectado</h1>
            <div class="subtitle">YggdraTasks - Sistema de Segurança</div>
        </div>

        <div style="text-align: center; margin-bottom: 30px;">
            <div class="alert-badge">⚠️ ALERTA DE SEGURANÇA</div>
        </div>

        <div class="greeting">
            Olá <strong>{{ $user->name }}</strong>! 👋
        </div>

        <p style="font-size: 18px; color: #374151; margin-bottom: 30px;">
            Detectamos um <strong>novo login na sua conta</strong>.
            Esta notificação é parte do nosso sistema de segurança para proteger sua conta. 🛡️
        </p>

        <div class="login-details">
            <h3><span class="emoji">📊</span>Detalhes do Login</h3>

            <div class="detail-row">
                <span class="detail-label">Usuário:</span>
                <span class="detail-value">{{ $user->name }} ({{ $user->email }})</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Data e Hora:</span>
                <span class="detail-value">⏰ {{ $loginTime->format('d/m/Y H:i:s') }}</span>
            </div>

            @if($ipAddress)
            <div class="detail-row">
                <span class="detail-label">Endereço IP:</span>
                <span class="detail-value">🌐 {{ $ipAddress }}</span>
            </div>
            @endif

            @if($userAgent)
            <div class="detail-row">
                <span class="detail-label">Navegador/Dispositivo:</span>
                <span class="detail-value">💻 {{ $userAgent }}</span>
            </div>
            @endif
        </div>

        <div class="security-steps">
            <h4><span class="emoji">🚨</span>Ações Recomendadas</h4>
            <ul>
                <li><strong>Verificar</strong> se foi você que fez o login</li>
                <li><strong>Alterar senha</strong> se não reconhecer o acesso</li>
                <li><strong>Verificar</strong> dispositivos conectados</li>
                <li><strong>Reportar</strong> atividade suspeita se necessário</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ $dashboardUrl }}" class="action-button">
                🎯 Acessar Dashboard
            </a>
        </div>

        <div class="security-steps">
            <h4><span class="emoji">🔒</span>Segurança da Conta</h4>
            <ul>
                <li><strong>Mantenha sua senha segura</strong> e única</li>
                <li><strong>Ative a autenticação de dois fatores</strong> se disponível</li>
                <li><strong>Não compartilhe suas credenciais</strong> com ninguém</li>
                <li><strong>Faça logout</strong> ao usar dispositivos públicos</li>
                <li><strong>Monitore</strong> regularmente suas atividades</li>
            </ul>
        </div>

        <div class="warning-info">
            <p>🚨 <strong>ATENÇÃO!</strong></p>
            <p>Se você não fez este login, entre em contato <strong>IMEDIATAMENTE</strong> com o suporte técnico!</p>
        </div>

        <div class="footer">
            <p><strong>Atenciosamente,</strong></p>
            <p style="font-size: 18px; color: #dc2626; font-weight: 600;">Equipe de Segurança - Iron Force Tasks</p>
            <p>Esta notificação foi enviada automaticamente pelo sistema de segurança.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #9ca3af;">
                🔒 Sua segurança é nossa prioridade. Esta notificação ajuda a proteger sua conta.
            </p>
        </div>
    </div>
</body>
</html>
