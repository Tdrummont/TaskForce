# 🚀 TaskForce - Sistema de Gerenciamento de Tarefas

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-2.x-blue.svg)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC.svg)](https://tailwindcss.com)

## LINK DO VIDEO DE APRESENTAÇÃO DO SISTEMA
https://drive.google.com/drive/u/1/folders/1eJM90U87wXfCHhIf92VttS_jwhiRuvV0



## 📋 **Descrição**

**TaskForce** é um sistema completo de gerenciamento de tarefas desenvolvido com Laravel 10, Vue.js 3 e Inertia.js. O sistema oferece uma interface moderna e intuitiva para gerenciar projetos, com funcionalidades avançadas como drag & drop, notificações em tempo real, e integração com Google OAuth.

## ✨ **Funcionalidades Principais**

### 🎯 **Gestão de Tarefas**
- ✅ **Kanban Board** com drag & drop
- ✅ **Sistema de prioridades** (Baixa, Média, Alta)
- ✅ **Categorização** e tags
- ✅ **Prazos** com alertas automáticos
- ✅ **Subtarefas** e dependências
- ✅ **Anexos** e comentários

### 🔐 **Autenticação e Segurança**
- ✅ **Login tradicional** com email/senha
- ✅ **Google OAuth** integrado
- ✅ **Verificação de email**
- ✅ **Sistema de permissões** baseado em roles
- ✅ **Proteção CSRF** e validações

### 📱 **Interface Moderna**
- ✅ **Design responsivo** para todos os dispositivos
- ✅ **Tema escuro/claro** (em desenvolvimento)
- ✅ **Animações suaves** e feedback visual
- ✅ **Atalhos de teclado** para produtividade
- ✅ **Interface intuitiva** com UX otimizada

### 🔔 **Notificações e Comunicação**
- ✅ **Notificações em tempo real** via WebSockets
- ✅ **Emails automáticos** para mudanças importantes
- ✅ **Sistema de alertas** para prazos
- ✅ **Histórico de atividades** completo

### 📊 **Relatórios e Analytics**
- ✅ **Dashboard** com métricas em tempo real
- ✅ **Relatórios personalizáveis** (PDF/CSV)
- ✅ **Gráficos** de produtividade
- ✅ **Exportação de dados** para análise

## 🛠️ **Tecnologias Utilizadas**

### **Backend**
- **Laravel 10** - Framework PHP robusto e elegante
- **MySQL/PostgreSQL** - Banco de dados relacional
- **Redis** - Cache e sessões
- **Pusher** - WebSockets para notificações em tempo real
- **Laravel Sanctum** - Autenticação API

### **Frontend**
- **Vue.js 3** - Framework JavaScript progressivo
- **Inertia.js** - SPA sem complexidade
- **Tailwind CSS** - Framework CSS utilitário
- **Vue Draggable** - Drag & drop funcional
- **Alpine.js** - Interatividade leve

### **DevOps & Ferramentas**
- **Vite** - Build tool rápida
- **Laravel Mix** - Compilação de assets
- **PHPUnit** - Testes automatizados
- **Docker** - Containerização (opcional)

## 🚀 **Instalação e Configuração**

### **Pré-requisitos**
- PHP 8.1+
- Composer 2.0+
- Node.js 16+
- SQLite + ou PostgreSQL 13+
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

### **6. Configure o Google OAuth (opcional)**
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

### **Testes manuais**
- Teste o drag & drop no Kanban
- Verifique notificações em tempo real
- Teste login com Google
- Valide responsividade mobile

## 📁 **Estrutura do Projeto**

```
taskforce/
├── app/
│   ├── Http/Controllers/    # Controllers da aplicação
│   ├── Models/             # Modelos Eloquent
│   ├── Events/             # Eventos do sistema
│   ├── Notifications/      # Notificações
│   └── Services/           # Serviços de negócio
├── resources/
│   ├── js/                 # Componentes Vue.js
│   ├── css/                # Estilos Tailwind
│   └── views/              # Templates Blade
├── database/
│   ├── migrations/         # Migrações do banco
│   ├── seeders/            # Dados iniciais
│   └── factories/          # Fábricas para testes
├── routes/                  # Definição de rotas
├── config/                  # Configurações
└── tests/                   # Testes automatizados
```

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

### **3. Monitoramento**
- **Logs** estruturados
- **Métricas** de performance
- **Alertas** automáticos
- **Backup** automático

## 🤝 **Contribuindo**

### **1. Fork o projeto**
### **2. Crie uma branch**
```bash
git checkout -b feature/nova-funcionalidade
```
### **3. Commit suas mudanças**
```bash
git commit -m 'Adiciona nova funcionalidade'
```
### **4. Push para a branch**
```bash
git push origin feature/nova-funcionalidade
```
### **5. Abra um Pull Request**

## 📝 **Licença**

Este projeto está sob a licença **MIT**. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 **Autor**

**Thalita Drummont]** - [tdrummontt@gmail.com](mailto:tdrummontt@gmail.com)

- **GitHub**: [@tdrummontt](https://github.com/tdrummontt)
- **LinkedIn**: [Thalita Drummont](https://www.linkedin.com/in/thalita-s-costa

## 📞 **Suporte**

- **Issues**: [GitHub Issues](https://github.com/tdrummontt/taskforce/issues)
- **Email**: [tdrummontt@gmail.com](mailto:tdrummontt@gmail.com)
- **Documentação**: [Wiki do Projeto](https://github.com/tdrummontt/taskforce/wiki)
