# 🚀 TaskForce - Sistema de Gerenciamento de Tarefas

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-2.x-blue.svg)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC.svg)](https://tailwindcss.com)

## 📋 **Descrição**

**TaskForce** é um sistema completo de gerenciamento de tarefas desenvolvido com Laravel 10, Vue.js 3 e Inertia.js. O sistema oferece uma interface moderna e intuitiva para gerenciar projetos, com funcionalidades avançadas como drag & drop, notificações em tempo real, e integração com Google OAuth.

## ✨ **Funcionalidades Implementadas**

### 🎯 **Gestão de Tarefas**
- ✅ **CRUD Completo** - Criação, edição, visualização e exclusão de tarefas
- ✅ **Sistema de Prioridades** - Baixa, Média e Alta
- ✅ **Categorização** - Organização por categorias
- ✅ **Prazos e Datas** - Sistema de vencimento com alertas
- ✅ **Status de Tarefas** - Pending, In Progress, Completed

### 🔐 **Sistema de Autenticação**
- ✅ **Login Tradicional** - Email e senha
- ✅ **Google OAuth** - Integração completa com Google
- ✅ **Proteção de Rotas** - Middleware de autenticação
- ✅ **Sessões Seguras** - Gerenciamento de sessões

### 📱 **Interface Moderna**
- ✅ **Design Responsivo** - Adaptável a todos os dispositivos
- ✅ **Interface Intuitiva** - UX otimizada
- ✅ **Animações Suaves** - Feedback visual
- ✅ **Tema Consistente** - Design system unificado

### 🔔 **Sistema de Notificações**
- ✅ **Notificações em Tempo Real** - Via WebSockets
- ✅ **Emails Automáticos** - Para mudanças importantes
- ✅ **Sistema de Alertas** - Para prazos e atualizações
- ✅ **Histórico de Atividades** - Log completo de ações

### 🎨 **Interface Kanban**
- ✅ **Board Visual** - Organização por colunas
- ✅ **Drag & Drop** - Movimentação intuitiva
- ✅ **Filtros Avançados** - Por status, prioridade, categoria
- ✅ **Busca Inteligente** - Pesquisa em tempo real

### 📊 **Dashboard e Analytics**
- ✅ **Métricas em Tempo Real** - KPIs principais
- ✅ **Gráficos de Produtividade** - Visualização de dados
- ✅ **Relatórios** - Exportação de informações
- ✅ **Estatísticas** - Análise de performance

### 🗓️ **Sistema de Feriados**
- ✅ **Detecção Automática** - Verificação de feriados nacionais
- ✅ **API Integrada** - Consulta em tempo real
- ✅ **Notificações Visuais** - Snackbar informativo
- ✅ **Fallback Local** - Feriados nacionais fixos

## 🛠️ **Tecnologias Utilizadas**

### **Backend**
- **Laravel 10** - Framework PHP robusto e elegante
- **MySQL/PostgreSQL** - Banco de dados relacional
- **Redis** - Cache e sessões
- **Pusher** - WebSockets para notificações
- **Laravel Sanctum** - Autenticação API

### **Frontend**
- **Vue.js 3** - Framework JavaScript progressivo
- **Inertia.js** - SPA sem complexidade
- **Tailwind CSS** - Framework CSS utilitário
- **TypeScript** - Tipagem estática
- **Vite** - Build tool moderna

### **Integrações**
- **Google OAuth** - Autenticação social
- **API de Feriados** - Invertexto API
- **Sistema de Emails** - SMTP configurável
- **WebSockets** - Notificações em tempo real

## 🚀 **Instalação e Configuração**

### **Pré-requisitos**
- PHP 8.1+
- Composer 2.0+
- Node.js 16+
- MySQL 8.0+ ou PostgreSQL 13+
- Redis 6.0+

### **1. Clone o repositório**
```bash
git clone https://github.com/tdrummontt/taskforce.git
cd taskforce
```

### **2. Instale as dependências**
```bash
# Backend
composer install

# Frontend
npm install
```

### **3. Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

### **4. Configure o banco de dados**
```env
DB_CONNECTION=sqlite
# ou
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskforce
DB_USERNAME=root
DB_PASSWORD=
```

### **5. Execute as migrações**
```bash
php artisan migrate
php artisan db:seed
```

### **6. Configure o Google OAuth**
```env
GOOGLE_CLIENT_ID=seu_client_id
GOOGLE_CLIENT_SECRET=seu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8001/auth/google/callback
```

### **7. Inicie o servidor**
```bash
# Backend
php artisan serve

# Frontend (em outro terminal)
npm run dev
```

## 📱 **Como Usar**

### **1. Acesse o sistema**
```
http://localhost:8001
```

### **2. Faça login**
- Use suas credenciais ou
- Faça login com Google

### **3. Crie sua primeira tarefa**
- Clique em "+ Nova Tarefa"
- Preencha os campos obrigatórios
- Defina prioridade e prazo

### **4. Use o Kanban Board**
- Arraste tarefas entre colunas
- Mude status com drag & drop
- Organize por prioridade

### **5. Gerencie notificações**
- Receba alertas em tempo real
- Configure preferências de email
- Acompanhe histórico de atividades

## 🧪 **Testes**

### **Executar testes automatizados**
```bash
# Testes PHP
php artisan test

# Testes específicos
php artisan test --filter=TaskTest
```

## 📁 **Estrutura do Projeto**

```
taskforce/
├── app/                    # Aplicação Laravel completa
│   ├── Http/Controllers/   # Controllers da aplicação
│   ├── Models/            # Modelos Eloquent
│   ├── Events/            # Eventos do sistema
│   ├── Notifications/     # Notificações
│   └── Services/          # Serviços de negócio
├── screenshots/           # Prints de tela das funcionalidades
├── video/                 # Vídeo de demonstração
│   └── demonstracao.mp4   # Vídeo explicativo (máx. 8 min)
├── curriculo/             # Documentos pessoais
│   ├── curriculo.pdf      # Currículo atualizado
│   └── programathor.txt   # Link do perfil Programathor
├── resources/             # Recursos da aplicação
│   ├── js/               # Componentes Vue.js
│   ├── css/              # Estilos Tailwind
│   └── views/            # Templates Blade
├── database/             # Banco de dados
│   ├── migrations/       # Migrações
│   ├── seeders/          # Dados iniciais
│   └── factories/        # Fábricas para testes
├── routes/               # Definição de rotas
├── config/               # Configurações
└── tests/                # Testes automatizados
```

## 🎥 **Demonstração**

### **Vídeo de Demonstração**
- **Localização**: `/video/demonstracao.mp4`
- **Duração**: Máximo 8 minutos
- **Conteúdo**: Explicação completa de todas as funcionalidades

### **Screenshots**
- **Localização**: `/screenshots/`
- **Conteúdo**: Prints de tela das principais funcionalidades
- **Formato**: PNG/JPG de alta qualidade

## 🔧 **Configurações Avançadas**

### **WebSockets (Pusher)**
```env
PUSHER_APP_ID=seu_app_id
PUSHER_APP_KEY=sua_app_key
PUSHER_APP_SECRET=seu_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=sua_cluster
```

### **Email (Gmail)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_senha_app
MAIL_ENCRYPTION=tls
```

### **Cache Redis**
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 🚀 **Deploy em Produção**

### **1. Configurações de produção**
```bash
# Otimizar para produção
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **2. Configurar servidor web**
- **Nginx** ou **Apache**
- **SSL/HTTPS** obrigatório
- **Compressão gzip** habilitada
- **Cache de headers** configurado

## 📝 **Licença**

Este projeto está sob a licença **MIT**. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 **Desenvolvedora**

**Thalita Drummont** - [tdrummontt@gmail.com](mailto:tdrummontt@gmail.com)

- **GitHub**: [@tdrummontt](https://github.com/tdrummontt)
- **LinkedIn**: [Thalita Drummont](https://www.linkedin.com/in/thalita-s-costa)
- **Programathor**: [Perfil](https://programathor.com.br/users/thalita-drummont)

## 📞 **Contato**

- **Email**: [tdrummontt@gmail.com](mailto:tdrummontt@gmail.com)
- **GitHub Issues**: [Reportar problemas](https://github.com/tdrummontt/taskforce/issues)

---

**Desenvolvido com ❤️ por Thalita Drummont**