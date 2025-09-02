# Sistema de Notificações de Tarefas - Configuração Completa

## 📋 Visão Geral

Este sistema implementa notificações em tempo real para atribuição de tarefas, incluindo:
- **WebSocket** via Pusher para notificações instantâneas
- **Email** via Laravel Mail para notificações persistentes
- **Frontend Vue 3** com TypeScript para interface de usuário

## 🔧 Configuração do Backend (Laravel)

### 1. Variáveis de Ambiente (.env)

```env
# Broadcasting
BROADCAST_DRIVER=pusher

# Pusher Configuration
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@yourapp.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Configuração do Pusher

1. Acesse [Pusher.com](https://pusher.com) e crie uma conta
2. Crie um novo app
3. Copie as credenciais para o arquivo `.env`
4. Configure as variáveis de ambiente

### 3. Estrutura de Arquivos Criados

```
app/
├── Events/
│   └── TaskAssigned.php          # Evento de broadcast
├── Mail/
│   └── TaskAssignedMail.php      # Classe de email
├── Broadcasting/
│   └── UserChannel.php           # Canal privado
└── Http/Controllers/
    └── TaskController.php        # Controlador atualizado

resources/
├── views/
│   └── emails/
│       └── tasks/
│           └── assigned.blade.php # Template de email
└── js/
    ├── Components/
    │   └── RealTimeNotifications.vue # Componente de notificações
    ├── types/
    │   └── global.d.ts           # Tipos TypeScript
    └── bootstrap.js              # Configuração do Echo

config/
└── broadcasting.php              # Configuração de broadcast
```

## 🚀 Configuração do Frontend (Vue 3 + TypeScript)

### 1. Dependências Instaladas

```bash
npm install --save laravel-echo pusher-js --legacy-peer-deps
```

### 2. Configuração do Laravel Echo

O arquivo `resources/js/bootstrap.js` já está configurado com:
- Laravel Echo
- Pusher como broadcaster
- Autenticação para canais privados

### 3. Componente de Notificações

O componente `RealTimeNotifications.vue` inclui:
- Toast notifications animadas
- Escuta eventos em tempo real
- Som de notificação
- Auto-remoção após 5 segundos
- Barra de progresso visual

## 📡 Como Funciona

### 1. Fluxo de Notificação

```
Usuário A atribui tarefa → Usuário B
           ↓
    TaskController.store()
           ↓
    Event::dispatch(TaskAssigned)
           ↓
    ┌─────────────────┬─────────────────┐
    │   WebSocket     │      Email      │
    │   (Tempo Real)  │   (Persistente) │
    └─────────────────┴─────────────────┘
           ↓               ↓
    Frontend recebe     Email enviado
    notificação         para usuário
```

### 2. Canais de Broadcast

- **Canal Privado**: `user.{id}` - Apenas o usuário específico pode acessar
- **Evento**: `task.assigned` - Disparado quando uma tarefa é atribuída
- **Dados**: Informações da tarefa, usuário que atribuiu, etc.

### 3. Segurança

- Canais privados requerem autenticação
- Usuários só podem acessar seus próprios canais
- Validação no backend antes do broadcast

## 🧪 Testando o Sistema

### 1. Iniciar Servidores

```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Compilação de assets
npm run dev

# Terminal 3: Queue para emails (opcional)
php artisan queue:work
```

### 2. Teste de Notificação

1. **Login** com dois usuários diferentes
2. **Criar tarefa** atribuída ao segundo usuário
3. **Verificar** se a notificação aparece em tempo real
4. **Verificar** se o email foi enviado

### 3. Debug

- **Console do navegador**: Logs do Echo e eventos
- **Logs do Laravel**: `storage/logs/laravel.log`
- **Telescope** (se instalado): Monitorar broadcasts

## 🔍 Troubleshooting

### Problemas Comuns

1. **Notificações não aparecem**
   - Verificar se o Pusher está configurado
   - Verificar console do navegador para erros
   - Verificar se o usuário está autenticado

2. **Emails não são enviados**
   - Verificar configuração do MAIL_MAILER
   - Verificar logs do Laravel
   - Testar com `php artisan tinker`

3. **Erros de CORS**
   - Verificar se o broadcasting está habilitado
   - Verificar se as rotas de broadcast estão registradas

### Logs Úteis

```bash
# Ver rotas de broadcast
php artisan route:list --name=broadcasting

# Ver configuração de broadcast
php artisan config:show broadcasting

# Testar evento
php artisan tinker
event(new App\Events\TaskAssigned($task, $user, $assignedTo));
```

## 📱 Personalização

### 1. Estilo das Notificações

Edite `RealTimeNotifications.vue` para:
- Mudar cores e estilos
- Ajustar posição (top, right, bottom, left)
- Modificar animações
- Adicionar sons personalizados

### 2. Template de Email

Edite `resources/views/emails/tasks/assigned.blade.php` para:
- Personalizar layout
- Adicionar logo da empresa
- Incluir informações adicionais
- Modificar call-to-action

### 3. Dados do Evento

Edite `TaskAssigned.php` para:
- Incluir mais informações
- Modificar formato dos dados
- Adicionar metadados personalizados

## 🚀 Próximos Passos

### Funcionalidades Adicionais

1. **Notificações Push** (Service Workers)
2. **Preferências de Usuário** (tipo de notificação)
3. **Histórico de Notificações** (banco de dados)
4. **Notificações em Lote** (múltiplas tarefas)
5. **Integração com Slack/Discord**

### Otimizações

1. **Queue para emails** (Redis/Database)
2. **Cache de usuários** (Redis)
3. **Rate limiting** para broadcasts
4. **Compressão** de dados WebSocket

## 📚 Recursos Adicionais

- [Laravel Broadcasting](https://laravel.com/docs/broadcasting)
- [Pusher Documentation](https://pusher.com/docs)
- [Laravel Echo](https://laravel.com/docs/broadcasting#client-side-installation)
- [Vue 3 Composition API](https://vuejs.org/guide/extras/composition-api-faq.html) 