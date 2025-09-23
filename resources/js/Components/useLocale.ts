import { usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

type Locale = 'pt' | 'en' | 'es'

const messages: Record<Locale, Record<string, string>> = {
  en: {
    // Welcome
    'app.title': 'Task System',
    'app.subtitle': 'Organize your tasks simply and practically.',
    'auth.login': 'Login',
    'auth.register': 'Register',

    // Auth (login page)
    'auth.title': 'Task System',
    'auth.email': 'Email',
    'auth.password': 'Password',
    'auth.remember': 'Remember me',
    'auth.forgot': 'Forgot your password?',
    'auth.sign_in': 'Sign in',
    'auth.or': 'or',
    'auth.sign_in_with_google': 'Sign in with Google',
    'auth.no_account': "Don't have an account?",
    'auth.register_here': 'Register',
    'auth.signing_in': 'Signing in...',
    'auth.authenticating': 'Authenticating with Google',
    'auth.please_wait': 'Please wait while we process your login...',

    // Common
    'common.refresh': 'Refresh',
    'common.days': 'days',

    // Dashboard
    'dashboard.title': 'Dashboard - Metrics & KPIs',
    'dashboard.week_productivity': 'Weekly Productivity',
    'dashboard.productivity_streak': 'Productivity Streak',
    'dashboard.overdue_tasks': 'Overdue Tasks',
    'dashboard.avg_time': 'Average Time',
    'dashboard.daily_goal': 'Daily Goal vs Achieved',
    'dashboard.weekly_goal': 'Weekly Goal vs Achieved',
    'dashboard.progress': 'Progress',
    'dashboard.of_daily_goal': '% of daily goal',
    'dashboard.of_weekly_goal': '% of weekly goal',
    'dashboard.last_7_days': 'Productivity in the Last 7 Days',
    'dashboard.overdue_highlight': 'Overdue Tasks',
    'dashboard.mark_completed': 'Mark as Completed',
    'dashboard.status_breakdown': 'Task Status',
    'dashboard.by_priority': 'By Priority',
    'dashboard.top_categories': 'Top Productive Categories',
    'dashboard.recent_tasks': 'Recent Tasks',
    'dashboard.no_recent_tasks': 'No recent tasks.',
    'dashboard.upcoming_tasks': 'Upcoming Tasks',
    'dashboard.no_upcoming_tasks': 'No upcoming tasks.',
    'dashboard.due_on': 'Due on',
    'dashboard.days_overdue_suffix': 'days overdue',

    // Reports
    'reports.title': 'Reports & Statistics',
    'reports.header': 'Reports',
    'reports.subtitle': 'Data analysis and statistics',
    'reports.realtime': 'Data updated in real time',
    'reports.last_update': 'Last update',
    'reports.tasks': 'tasks',
    'reports.productivity': 'productivity',
    'reports.overview': 'General Statistics',
    'reports.total_tasks': 'Total Tasks',
    'reports.completed': 'Completed',
    'reports.productivity_rate': 'Productivity Rate',
    'reports.avg_time': 'Average Time',
    'reports.productivity_30d': 'Productivity (Last 30 days)',
    'reports.by_status': 'Tasks by Status',
    'reports.completion_time': 'Completion Time',
    'reports.avg': 'Average',
    'reports.median': 'Median',
    'reports.fastest': 'Fastest',
    'reports.slowest': 'Slowest',
    'reports.weekly': 'Weekly Report',
    'reports.monthly': 'Monthly Report',
    'reports.period': 'Period',
    'reports.completion_rate': 'Completion Rate',
    'reports.daily_activity': 'Daily Activity',
    'reports.created': 'Created',
    'reports.tasks_count': 'Tasks Count',
    'reports.growth': 'Growth',
    'reports.avg_per_day': 'Average per Day',
    'reports.activity_history': 'Activity History',
    'reports.no_activity': 'No activity recorded.',

    // Status
    'status.pending': 'Pending',
    'status.in_progress': 'In Progress',
    'status.completed': 'Completed',

    // Priority
    'priority.low': 'Low',
    'priority.medium': 'Medium',
    'priority.high': 'High',

    // Tasks (Index.vue)
    'tasks.my_tasks': 'My Tasks',
    'tasks.create.page_title': 'New Task',
    'tasks.create.header': 'Create New Task',
    'tasks.edit.page_title': 'Edit Task',
    'tasks.form.title': 'Title',
    'tasks.form.required': '*',
    'tasks.form.title_ph': 'Enter task title',
    'tasks.form.description': 'Description',
    'tasks.form.description_ph': 'Describe the task...',
    'tasks.form.start_date': 'Start Date',
    'tasks.form.end_date': 'Due Date',
    'tasks.form.status': 'Status',
    'tasks.form.priority': 'Priority',
    'tasks.form.category': 'Category',
    'tasks.form.category_ph': 'Select a category',
    'tasks.form.estimated_hours': 'Estimated Hours',
    'tasks.form.estimated_hours_ph': 'Ex: 8',
    'tasks.form.parent_task': 'Parent Task',
    'tasks.form.parent_task_none': 'None (main task)',
    'tasks.form.assignee': 'Assignee',
    'tasks.form.assignee_none': 'No one',
    'tasks.form.actions.clear': 'Clear',
    'tasks.form.actions.cancel': 'Cancel',
            'tasks.form.actions.create': 'Create Task',
        'tasks.form.actions.update': 'Update',
        'tasks.backup': 'Backup',
        // Navbar & header
        'navbar.subtitle': 'Task Management',
        'navbar.tasks': 'Tasks',
        'navbar.reports': 'Reports',
        'navbar.new_task': 'New Task',
        'navbar.search_ph': 'Search tasks...',
        'navbar.profile': 'Profile',
        'navbar.logout': 'Logout',
        // Notifications
        'notifications.title': 'Notifications',
        'notifications.mark_all': 'Mark all as read',
        'notifications.view_all': 'View all notifications',
        'notifications.login_detected': 'Login detected for',
        'notifications.task_delegated': "Task '{task}' was delegated to you by {user}",
        'notifications.none': 'No notifications',
    'tasks.restore': 'Restore',
    'tasks.delete_all': 'Delete All',

    'tasks.search_placeholder': 'Search tasks by title, description or tags...',
    'filters.all_status': 'All Status',
    'filters.all_priorities': 'All Priorities',
    'filters.all_categories': 'All Categories',
    'filters.clear': 'Clear',
    'filters.clear_filters': 'Clear filters',
    'categories.work': 'Work',
    'categories.personal': 'Personal',
    'categories.study': 'Study',
    'categories.health': 'Health',
    'categories.finance': 'Finance',
    'categories.leisure': 'Leisure',
    
    // Task Categories
    'categories.development': 'Development',
    'categories.design': 'Design',
    'categories.marketing': 'Marketing',
    'categories.sales': 'Sales',
    'categories.support': 'Support',
    'categories.administrative': 'Administrative',
    'categories.financial': 'Financial',
    'categories.human_resources': 'Human Resources',
    'categories.operations': 'Operations',
    'categories.quality': 'Quality',
    'categories.research': 'Research',
    'categories.training': 'Training',
    'categories.maintenance': 'Maintenance',
    'categories.infrastructure': 'Infrastructure',
    'categories.security': 'Security',
    
    'tasks.found': 'task(s) found',
    'tasks.for_query': 'for',
    'tasks.organize': 'Organize Tasks',
    'tasks.drag_help': 'Drag tasks between columns to organize them easily',
    'tasks.list_title': 'Task List',
    'tasks.none_found': 'No tasks found.',
    'tasks.status': 'Status',
    'tasks.priority': 'Priority',
    'tasks.due': 'Due',
    'tasks.created_by': 'Created by',
    'tasks.you': 'You',
    'tasks.assigned_to': 'Assigned to',
    'tasks.edit': 'Edit',
    'tasks.delete': 'Delete',
    'tasks.restore_backup': 'Restore Backup',
    'tasks.select_backup_file': 'Select the backup file (.json)',
    'common.cancel': 'Cancel',
    'common.attention': 'Attention!',
    'tasks.cannot_undo': 'This action cannot be undone',
    'tasks.delete_all_warn1': 'All tasks will be permanently deleted',
    'tasks.delete_all_warn2': 'This action cannot be undone',
    'tasks.delete_all_warn3': 'Activity logs will be kept',
    'tasks.delete_all_warn4': 'We recommend doing a backup first',
    'tasks.confirm_delete_all': 'Are you sure you want to permanently delete ALL tasks?',
    'tasks.confirm_delete': 'Are you sure you want to delete this task?',
    'common.error_try_again': 'Error. Please try again.',
    'tasks.restore_error': 'Error restoring backup',
    'tasks.update_status_error': 'Error updating task status',

    // Due labels
    'due.completed': 'Completed',
    'due.overdue': 'Overdue',
    'due.today': 'Due today',
    'due.soon': 'Due soon',
    'due.normal': 'Normal',
    'due.tomorrow': 'Tomorrow',
    'due.next_week': 'Next Week',

    // Quick Task
    'quick_task.title': 'New Quick Task',
    'quick.new_quick_task': 'New Quick Task',
    'quick.today': 'Today',
    'quick.tomorrow': 'Tomorrow',
    'quick.next_week': 'Next Week',
    'quick.create_task': 'Create Task',

    // Form
    'form.title': 'Title',
    'form.description': 'Description',
    'form.priority': 'Priority',
    'form.category': 'Category',
    'form.due_date': 'Due Date',
    
    // Task Form
    'task.title_label': 'Title',
    'task.description_label': 'Description',
    'task.priority_label': 'Priority',
    'task.category_label': 'Category',
    'task.due_date_label': 'Due Date',
    'task.title_placeholder': 'Enter task title',
    'task.description_quick_placeholder': 'Briefly describe the task...',
    'task.category_placeholder': 'Select a category',

    // Placeholders
    'form.placeholder.title': 'Enter the task title',
    'form.placeholder.description_quick': 'Briefly describe the task...',
    'form.select_category': 'Select a category',

    // Actions
    'actions.cancel': 'Cancel',
    'actions.create_task': 'Create Task',
    'actions.creating': 'Creating...',
    
    // Common
    'common.saving': 'Saving...',

    // Floating Action Button
    'fab.new_task': 'New Task',
    'fab.quick_task': 'Quick Task',
    'fab.view_tasks': 'View Tasks',

    // Toasts / Alerts
    'toast.task_created': 'Task created successfully!',
    'toast.task_updated': 'Task updated successfully!',
    'toast.task_deleted': 'Task deleted successfully!',
    'toast.task_deleted_all': 'All tasks deleted successfully!',
    'toast.task_restored': 'Backup restored successfully!',
    'toast.error': 'Something went wrong. Please try again.',

    // Validation
    'validation.required': 'This field is required.',
    'validation.email': 'Please enter a valid email address.',
    'validation.min': 'The value is too short.',
    'validation.max': 'The value is too long.',
    'validation.date': 'Please enter a valid date.',
    'validation.number': 'Please enter a valid number.',
    'validation.confirmed': 'The confirmation does not match.',
    'validation.unique': 'This value has already been taken.',
    'validation.password': 'Password must contain at least 8 characters, including one uppercase letter, one number and one special character.',
    'validation.invalid_priority': 'Please select a valid priority.',
    'validation.invalid_status': 'Please select a valid status.',
    'validation.invalid_category': 'Please select a valid category.',
    'validation.file_too_large': 'The file is too large.',
    'validation.invalid_file_type': 'Invalid file type.',

    // Holiday alerts
    'holiday.alert.title': 'Heads up!',
    'holiday.alert.body': 'The selected due date is a holiday: {name}.',
    'holiday.alert.generic': 'The selected due date is a holiday.',
    'holiday.state.label': 'State (UF)',
    
    // Holidays
    'holidays.alert': 'Holiday Alert',
    'holidays.on_date': 'The selected date is a holiday:',
  },
  pt: {
    // Welcome
    'app.title': 'Gerenciador de Tarefas',
    'app.subtitle': 'Organize suas tarefas de forma simples e prática.',
    'auth.login': 'Login',
    'auth.register': 'Registrar',

    // Auth (login page)
    'auth.title': 'Gerenciador de Tarefas',
    'auth.email': 'E-mail',
    'auth.password': 'Senha',
    'auth.remember': 'Lembrar-me',
    'auth.forgot': 'Esqueceu sua senha?',
    'auth.sign_in': 'Entrar',
    'auth.or': 'ou',
    'auth.sign_in_with_google': 'Entrar com Google',
    'auth.no_account': 'Não tem conta?',
    'auth.register_here': 'Registre-se',
    'auth.signing_in': 'Entrando...',
    'auth.authenticating': 'Autenticando com Google',
    'auth.please_wait': 'Aguarde enquanto processamos seu login...',

    // Common
    'common.refresh': 'Recarregar',
    'common.days': 'dias',

    // Dashboard
    'dashboard.title': 'Dashboard - Métricas e KPIs',
    'dashboard.week_productivity': 'Produtividade Semanal',
    'dashboard.productivity_streak': 'Streak de Produtividade',
    'dashboard.overdue_tasks': 'Tarefas em Atraso',
    'dashboard.avg_time': 'Tempo Médio',
    'dashboard.daily_goal': 'Meta Diária vs Realizado',
    'dashboard.weekly_goal': 'Meta Semanal vs Realizado',
    'dashboard.progress': 'Progresso',
    'dashboard.of_daily_goal': '% da meta diária',
    'dashboard.of_weekly_goal': '% da meta semanal',
    'dashboard.last_7_days': 'Produtividade dos Últimos 7 Dias',
    'dashboard.overdue_highlight': 'Tarefas em Atraso',
    'dashboard.mark_completed': 'Marcar como Concluída',
    'dashboard.status_breakdown': 'Status das Tarefas',
    'dashboard.by_priority': 'Por Prioridade',
    'dashboard.top_categories': 'Categorias Mais Produtivas',
    'dashboard.recent_tasks': 'Tarefas Recentes',
    'dashboard.no_recent_tasks': 'Nenhuma tarefa recente.',
    'dashboard.upcoming_tasks': 'Próximas Tarefas',
    'dashboard.no_upcoming_tasks': 'Nenhuma tarefa próxima.',
    'dashboard.due_on': 'Vence em',
    'dashboard.days_overdue_suffix': 'dias de atraso',

    // Reports
    'reports.title': 'Relatórios e Estatísticas',
    'reports.header': 'Relatórios',
    'reports.subtitle': 'Análise de dados e estatísticas',
    'reports.realtime': 'Dados atualizados em tempo real',
    'reports.last_update': 'Última atualização',
    'reports.tasks': 'tarefas',
    'reports.productivity': 'produtividade',
    'reports.overview': 'Estatísticas Gerais',
    'reports.total_tasks': 'Total de Tarefas',
    'reports.completed': 'Concluídas',
    'reports.productivity_rate': 'Taxa de Produtividade',
    'reports.avg_time': 'Tempo Médio',
    'reports.productivity_30d': 'Produtividade (Últimos 30 dias)',
    'reports.by_status': 'Tarefas por Status',
    'reports.completion_time': 'Tempo de Conclusão',
    'reports.avg': 'Tempo Médio',
    'reports.median': 'Tempo Mediano',
    'reports.fastest': 'Mais Rápida',
    'reports.slowest': 'Mais Lenta',
    'reports.weekly': 'Relatório Semanal',
    'reports.monthly': 'Relatório Mensal',
    'reports.period': 'Período',
    'reports.completion_rate': 'Taxa de Conclusão',
    'reports.daily_activity': 'Atividade Diária',
    'reports.created': 'Criadas',
    'reports.tasks_count': 'Quantidade de Tarefas',
    'reports.growth': 'Crescimento',
    'reports.avg_per_day': 'Média por Dia',
    'reports.activity_history': 'Histórico de Atividades',
    'reports.no_activity': 'Nenhuma atividade registrada.',

    // Status
    'status.pending': 'Pendente',
    'status.in_progress': 'Em Progresso',
    'status.completed': 'Concluída',

    // Priority
    'priority.low': 'Baixa',
    'priority.medium': 'Média',
    'priority.high': 'Alta',

    // Tasks (Index.vue)
    'tasks.my_tasks': 'Minhas Tarefas',
    'tasks.create.page_title': 'Nova Tarefa',
    'tasks.create.header': 'Criar Nova Tarefa',
    'tasks.edit.page_title': 'Editar Tarefa',
    'tasks.form.title': 'Título',
    'tasks.form.required': '*',
    'tasks.form.title_ph': 'Digite o título da tarefa',
    'tasks.form.description': 'Descrição',
    'tasks.form.description_ph': 'Descreva a tarefa...',
    'tasks.form.start_date': 'Data de Início',
    'tasks.form.end_date': 'Data de Vencimento',
    'tasks.form.status': 'Status',
    'tasks.form.priority': 'Prioridade',
    'tasks.form.category': 'Categoria',
    'tasks.form.category_ph': 'Selecione uma categoria',
    'tasks.form.estimated_hours': 'Horas Estimadas',
    'tasks.form.estimated_hours_ph': 'Ex: 8',
    'tasks.form.parent_task': 'Tarefa Pai',
    'tasks.form.parent_task_none': 'Nenhuma (tarefa principal)',
    'tasks.form.assignee': 'Atribuir para',
    'tasks.form.assignee_none': 'Ninguém',
    'tasks.form.actions.clear': 'Limpar',
    'tasks.form.actions.cancel': 'Cancelar',
            'tasks.form.actions.create': 'Criar Tarefa',
        'tasks.form.actions.update': 'Atualizar',
        'tasks.backup': 'Backup',
        // Navbar & header
        'navbar.subtitle': 'Gerenciamento de Tarefas',
        'navbar.tasks': 'Tarefas',
        'navbar.reports': 'Relatórios',
        'navbar.new_task': 'Nova Tarefa',
        'navbar.search_ph': 'Pesquisar tarefas...',
        'navbar.profile': 'Perfil',
        'navbar.logout': 'Sair',
        // Notifications
        'notifications.title': 'Notificações',
        'notifications.mark_all': 'Marcar todas como lidas',
        'notifications.view_all': 'Ver todas as notificações',
        'notifications.login_detected': 'Login detectado para',
        'notifications.task_delegated': "Tarefa '{task}' foi delegada para você por {user}",
        'notifications.none': 'Nenhuma notificação',
    'tasks.restore': 'Restaurar',
    'tasks.delete_all': 'Apagar Todas',

    'tasks.search_placeholder': 'Pesquisar tarefas por título, descrição ou tags...',
    'filters.all_status': 'Todos os Status',
    'filters.all_priorities': 'Todas as Prioridades',
    'filters.all_categories': 'Todas as Categorias',
    'filters.clear': 'Limpar',
    'filters.clear_filters': 'Limpar filtros',
    'categories.work': 'Trabalho',
    'categories.personal': 'Pessoal',
    'categories.study': 'Estudo',
    'categories.health': 'Saúde',
    'categories.finance': 'Finanças',
    'categories.leisure': 'Lazer',
    
    // Task Categories
    'categories.development': 'Desenvolvimento',
    'categories.design': 'Design',
    'categories.marketing': 'Marketing',
    'categories.sales': 'Vendas',
    'categories.support': 'Suporte',
    'categories.administrative': 'Administrativo',
    'categories.financial': 'Financeiro',
    'categories.human_resources': 'Recursos Humanos',
    'categories.operations': 'Operações',
    'categories.quality': 'Qualidade',
    'categories.research': 'Pesquisa',
    'categories.training': 'Treinamento',
    'categories.maintenance': 'Manutenção',
    'categories.infrastructure': 'Infraestrutura',
    'categories.security': 'Segurança',
    
    'tasks.found': 'tarefa(s) encontrada(s)',
    'tasks.for_query': 'para',
    'tasks.organize': 'Organizar Tarefas',
    'tasks.drag_help': 'Arraste as tarefas entre as colunas para organizá-las facilmente',
    'tasks.list_title': 'Lista de Tarefas',
    'tasks.none_found': 'Nenhuma tarefa encontrada.',
    'tasks.status': 'Status',
    'tasks.priority': 'Prioridade',
    'tasks.due': 'Vencimento',
    'tasks.created_by': 'Criado por',
    'tasks.you': 'Você',
    'tasks.assigned_to': 'Atribuído a',
    'tasks.edit': 'Editar',
    'tasks.delete': 'Excluir',
    'tasks.restore_backup': 'Restaurar Backup',
    'tasks.select_backup_file': 'Selecione o arquivo de backup (.json)',
    'common.cancel': 'Cancelar',
    'common.attention': 'Atenção!',
    'tasks.cannot_undo': 'Esta ação não pode ser desfeita',
    'tasks.delete_all_warn1': 'Todas as tarefas serão excluídas permanentemente',
    'tasks.delete_all_warn2': 'Esta ação não pode ser desfeita',
    'tasks.delete_all_warn3': 'Os logs de atividade serão mantidos',
    'tasks.delete_all_warn4': 'Recomendamos fazer um backup antes',
    'tasks.confirm_delete_all': 'Tem certeza que deseja excluir TODAS as tarefas permanentemente?',
    'tasks.confirm_delete': 'Tem certeza que deseja excluir esta tarefa?',
    'common.error_try_again': 'Erro. Tente novamente.',
    'tasks.restore_error': 'Erro ao restaurar backup',
    'tasks.update_status_error': 'Erro ao atualizar status da tarefa',

    // Due labels
    'due.completed': 'Concluída',
    'due.overdue': 'Vencida',
    'due.today': 'Vence hoje',
    'due.soon': 'Vence em breve',
    'due.normal': 'Normal',
    'due.tomorrow': 'Amanhã',
    'due.next_week': 'Próxima Semana',

    // Quick Task
    'quick_task.title': 'Nova Tarefa Rápida',
    'quick.new_quick_task': 'Nova Tarefa Rápida',
    'quick.today': 'Hoje',
    'quick.tomorrow': 'Amanhã',
    'quick.next_week': 'Próxima Semana',
    'quick.create_task': 'Criar Tarefa',

    // Form
    'form.title': 'Título',
    'form.description': 'Descrição',
    'form.priority': 'Prioridade',
    'form.category': 'Categoria',
    'form.due_date': 'Data de Vencimento',
    
    // Task Form
    'task.title_label': 'Título',
    'task.description_label': 'Descrição',
    'task.priority_label': 'Prioridade',
    'task.category_label': 'Categoria',
    'task.due_date_label': 'Data de Vencimento',
    'task.title_placeholder': 'Digite o título da tarefa',
    'task.description_quick_placeholder': 'Descreva brevemente a tarefa...',
    'task.category_placeholder': 'Selecione uma categoria',

    // Placeholders
    'form.placeholder.title': 'Digite o título da tarefa',
    'form.placeholder.description_quick': 'Descreva brevemente a tarefa...',
    'form.select_category': 'Selecione uma categoria',

    // Actions
    'actions.cancel': 'Cancelar',
    'actions.create_task': 'Criar Tarefa',
    'actions.creating': 'Criando...',
    
    // Common
    'common.saving': 'Salvando...',

    // Floating Action Button
    'fab.new_task': 'Nova Tarefa',
    'fab.quick_task': 'Tarefa Rápida',
    'fab.view_tasks': 'Ver Tarefas',

    // Toasts / Alerts
    'toast.task_created': 'Tarefa criada com sucesso!',
    'toast.task_updated': 'Tarefa atualizada com sucesso!',
    'toast.task_deleted': 'Tarefa excluída com sucesso!',
    'toast.task_deleted_all': 'Todas as tarefas foram excluídas com sucesso!',
    'toast.task_restored': 'Backup restaurado com sucesso!',
    'toast.error': 'Algo deu errado. Tente novamente.',

    // Validation
    'validation.required': 'Este campo é obrigatório.',
    'validation.email': 'Por favor, insira um e-mail válido.',
    'validation.min': 'O valor é muito curto.',
    'validation.max': 'O valor é muito longo.',
    'validation.date': 'Por favor, insira uma data válida.',
    'validation.number': 'Por favor, insira um número válido.',
    'validation.confirmed': 'A confirmação não corresponde.',
    'validation.unique': 'Este valor já foi utilizado.',
    'validation.password': 'A senha deve conter pelo menos 8 caracteres, incluindo uma letra maiúscula, um número e um caractere especial.',
    'validation.invalid_priority': 'Por favor, selecione uma prioridade válida.',
    'validation.invalid_status': 'Por favor, selecione um status válido.',
    'validation.invalid_category': 'Por favor, selecione uma categoria válida.',
    'validation.file_too_large': 'O arquivo é muito grande.',
    'validation.invalid_file_type': 'Tipo de arquivo inválido.',

    // Holiday alerts
    'holiday.alert.title': 'Atenção!',
    'holiday.alert.body': 'A data de vencimento selecionada é feriado: {name}.',
    'holiday.alert.generic': 'A data de vencimento selecionada é feriado.',
    'holiday.state.label': 'Estado (UF)',
    
    // Holidays
    'holidays.alert': 'Alerta de Feriado',
    'holidays.on_date': 'A data selecionada é feriado:',
  },
  es: {
    // Welcome
    'app.title': 'Sistema de Tareas',
    'app.subtitle': 'Organiza tus tareas de forma simple y práctica.',
    'auth.login': 'Iniciar Sesión',
    'auth.register': 'Registrarse',

    // Auth (login page)
    'auth.title': 'Sistema de Tareas',
    'auth.email': 'Correo',
    'auth.password': 'Contraseña',
    'auth.remember': 'Recordarme',
    'auth.forgot': '¿Olvidaste tu contraseña?',
    'auth.sign_in': 'Iniciar Sesión',
    'auth.or': 'o',
    'auth.sign_in_with_google': 'Iniciar sesión con Google',
    'auth.no_account': '¿No tienes cuenta?',
    'auth.register_here': 'Regístrate',
    'auth.signing_in': 'Iniciando sesión...',
    'auth.authenticating': 'Autenticando con Google',
    'auth.please_wait': 'Por favor espera mientras procesamos tu inicio de sesión...',

    // Common
    'common.refresh': 'Actualizar',
    'common.days': 'días',

    // Dashboard
    'dashboard.title': 'Panel - Métricas y KPIs',
    'dashboard.week_productivity': 'Productividad Semanal',
    'dashboard.productivity_streak': 'Racha de Productividad',
    'dashboard.overdue_tasks': 'Tareas Vencidas',
    'dashboard.avg_time': 'Tiempo Promedio',
    'dashboard.daily_goal': 'Meta Diaria vs Logrado',
    'dashboard.weekly_goal': 'Meta Semanal vs Logrado',
    'dashboard.progress': 'Progreso',
    'dashboard.of_daily_goal': '% de la meta diaria',
    'dashboard.of_weekly_goal': '% de la meta semanal',
    'dashboard.last_7_days': 'Productividad de los Últimos 7 Días',
    'dashboard.overdue_highlight': 'Tareas Vencidas',
    'dashboard.mark_completed': 'Marcar como Completada',
    'dashboard.status_breakdown': 'Estado de las Tareas',
    'dashboard.by_priority': 'Por Prioridad',
    'dashboard.top_categories': 'Categorías Más Productivas',
    'dashboard.recent_tasks': 'Tareas Recientes',
    'dashboard.no_recent_tasks': 'No hay tareas recientes.',
    'dashboard.upcoming_tasks': 'Próximas Tareas',
    'dashboard.no_upcoming_tasks': 'No hay tareas próximas.',
    'dashboard.due_on': 'Vence el',
    'dashboard.days_overdue_suffix': 'días de retraso',

    // Reports
    'reports.title': 'Reportes y Estadísticas',
    'reports.header': 'Reportes',
    'reports.subtitle': 'Análisis de datos y estadísticas',
    'reports.realtime': 'Datos actualizados en tiempo real',
    'reports.last_update': 'Última actualización',
    'reports.tasks': 'tareas',
    'reports.productivity': 'productividad',
    'reports.overview': 'Estadísticas Generales',
    'reports.total_tasks': 'Total de Tareas',
    'reports.completed': 'Completadas',
    'reports.productivity_rate': 'Tasa de Productividad',
    'reports.avg_time': 'Tiempo Promedio',
    'reports.productivity_30d': 'Productividad (Últimos 30 días)',
    'reports.by_status': 'Tareas por Estado',
    'reports.completion_time': 'Tiempo de Finalización',
    'reports.avg': 'Promedio',
    'reports.median': 'Mediana',
    'reports.fastest': 'Más Rápida',
    'reports.slowest': 'Más Lenta',
    'reports.weekly': 'Reporte Semanal',
    'reports.monthly': 'Reporte Mensual',
    'reports.period': 'Período',
    'reports.completion_rate': 'Tasa de Finalización',
    'reports.daily_activity': 'Actividad Diaria',
    'reports.created': 'Creadas',
    'reports.tasks_count': 'Cantidad de Tareas',
    'reports.growth': 'Crecimiento',
    'reports.avg_per_day': 'Promedio por Día',
    'reports.activity_history': 'Historial de Actividades',
    'reports.no_activity': 'No hay actividad registrada.',

    // Status
    'status.pending': 'Pendiente',
    'status.in_progress': 'En Progreso',
    'status.completed': 'Completada',

    // Priority
    'priority.low': 'Baja',
    'priority.medium': 'Media',
    'priority.high': 'Alta',

    // Tasks (Index.vue)
    'tasks.my_tasks': 'Mis Tareas',
    'tasks.create.page_title': 'Nueva Tarea',
    'tasks.create.header': 'Crear Nueva Tarea',
    'tasks.edit.page_title': 'Editar Tarea',
    'tasks.form.title': 'Título',
    'tasks.form.required': '*',
    'tasks.form.title_ph': 'Ingresa el título de la tarea',
    'tasks.form.description': 'Descripción',
    'tasks.form.description_ph': 'Describe la tarea...',
    'tasks.form.start_date': 'Fecha de Inicio',
    'tasks.form.end_date': 'Fecha de Vencimiento',
    'tasks.form.status': 'Estado',
    'tasks.form.priority': 'Prioridad',
    'tasks.form.category': 'Categoría',
    'tasks.form.category_ph': 'Selecciona una categoría',
    'tasks.form.estimated_hours': 'Horas Estimadas',
    'tasks.form.estimated_hours_ph': 'Ej: 8',
    'tasks.form.parent_task': 'Tarea Padre',
    'tasks.form.parent_task_none': 'Ninguna (tarea principal)',
    'tasks.form.assignee': 'Asignar a',
    'tasks.form.assignee_none': 'Nadie',
    'tasks.form.actions.clear': 'Limpiar',
    'tasks.form.actions.cancel': 'Cancelar',
    'tasks.form.actions.create': 'Crear Tarea',
    'tasks.form.actions.update': 'Actualizar',
    'tasks.backup': 'Respaldo',
    // Navbar & header
    'navbar.subtitle': 'Gestión de Tareas',
    'navbar.tasks': 'Tareas',
    'navbar.reports': 'Reportes',
    'navbar.new_task': 'Nueva Tarea',
    'navbar.search_ph': 'Buscar tareas...',
    'navbar.profile': 'Perfil',
    'navbar.logout': 'Cerrar Sesión',
    // Notifications
    'notifications.title': 'Notificaciones',
    'notifications.mark_all': 'Marcar todas como leídas',
    'notifications.view_all': 'Ver todas las notificaciones',
    'notifications.login_detected': 'Inicio de sesión detectado para',
    'notifications.task_delegated': "La tarea '{task}' fue delegada a ti por {user}",
    'notifications.none': 'No hay notificaciones',
    'tasks.restore': 'Restaurar',
    'tasks.delete_all': 'Eliminar Todas',

    'tasks.search_placeholder': 'Buscar tareas por título, descripción o etiquetas...',
    'filters.all_status': 'Todos los Estados',
    'filters.all_priorities': 'Todas las Prioridades',
    'filters.all_categories': 'Todas las Categorías',
    'filters.clear': 'Limpiar',
    'filters.clear_filters': 'Limpiar filtros',
    'categories.work': 'Trabajo',
    'categories.personal': 'Personal',
    'categories.study': 'Estudio',
    'categories.health': 'Salud',
    'categories.finance': 'Finanzas',
    'categories.leisure': 'Ocio',
    
    // Task Categories
    'categories.development': 'Desarrollo',
    'categories.design': 'Diseño',
    'categories.marketing': 'Marketing',
    'categories.sales': 'Ventas',
    'categories.support': 'Soporte',
    'categories.administrative': 'Administrativo',
    'categories.financial': 'Financiero',
    'categories.human_resources': 'Recursos Humanos',
    'categories.operations': 'Operaciones',
    'categories.quality': 'Calidad',
    'categories.research': 'Investigación',
    'categories.training': 'Capacitación',
    'categories.maintenance': 'Mantenimiento',
    'categories.infrastructure': 'Infraestructura',
    'categories.security': 'Seguridad',
    
    'tasks.found': 'tarea(s) encontrada(s)',
    'tasks.for_query': 'para',
    'tasks.organize': 'Organizar Tareas',
    'tasks.drag_help': 'Arrastra las tareas entre columnas para organizarlas fácilmente',
    'tasks.list_title': 'Lista de Tareas',
    'tasks.none_found': 'No se encontraron tareas.',
    'tasks.status': 'Estado',
    'tasks.priority': 'Prioridad',
    'tasks.due': 'Vencimiento',
    'tasks.created_by': 'Creado por',
    'tasks.you': 'Tú',
    'tasks.assigned_to': 'Asignado a',
    'tasks.edit': 'Editar',
    'tasks.delete': 'Eliminar',
    'tasks.restore_backup': 'Restaurar Respaldo',
    'tasks.select_backup_file': 'Selecciona el archivo de respaldo (.json)',
    'common.cancel': 'Cancelar',
    'common.attention': '¡Atención!',
    'tasks.cannot_undo': 'Esta acción no se puede deshacer',
    'tasks.delete_all_warn1': 'Todas las tareas serán eliminadas permanentemente',
    'tasks.delete_all_warn2': 'Esta acción no se puede deshacer',
    'tasks.delete_all_warn3': 'Los registros de actividad se mantendrán',
    'tasks.delete_all_warn4': 'Recomendamos hacer un respaldo primero',
    'tasks.confirm_delete_all': '¿Estás seguro de que quieres eliminar TODAS las tareas permanentemente?',
    'tasks.confirm_delete': '¿Estás seguro de que quieres eliminar esta tarea?',
    'common.error_try_again': 'Error. Por favor intenta de nuevo.',
    'tasks.restore_error': 'Error al restaurar respaldo',
    'tasks.update_status_error': 'Error al actualizar estado de la tarea',

    // Due labels
    'due.completed': 'Completada',
    'due.overdue': 'Vencida',
    'due.today': 'Vence hoy',
    'due.soon': 'Vence pronto',
    'due.normal': 'Normal',
    'due.tomorrow': 'Mañana',
    'due.next_week': 'Próxima Semana',

    // Quick Task
    'quick_task.title': 'Nueva Tarea Rápida',
    'quick.new_quick_task': 'Nueva Tarea Rápida',
    'quick.today': 'Hoy',
    'quick.tomorrow': 'Mañana',
    'quick.next_week': 'Próxima Semana',
    'quick.create_task': 'Crear Tarea',

    // Form
    'form.title': 'Título',
    'form.description': 'Descripción',
    'form.priority': 'Prioridad',
    'form.category': 'Categoría',
    'form.due_date': 'Fecha de Vencimiento',
    
    // Task Form
    'task.title_label': 'Título',
    'task.description_label': 'Descripción',
    'task.priority_label': 'Prioridad',
    'task.category_label': 'Categoría',
    'task.due_date_label': 'Fecha de Vencimiento',
    'task.title_placeholder': 'Ingresa el título de la tarea',
    'task.description_quick_placeholder': 'Describe brevemente la tarea...',
    'task.category_placeholder': 'Selecciona una categoría',

    // Placeholders
    'form.placeholder.title': 'Ingresa el título de la tarea',
    'form.placeholder.description_quick': 'Describe brevemente la tarea...',
    'form.select_category': 'Selecciona una categoría',

    // Actions
    'actions.cancel': 'Cancelar',
    'actions.create_task': 'Crear Tarea',
    'actions.creating': 'Creando...',
    
    // Common
    'common.saving': 'Guardando...',

    // Floating Action Button
    'fab.new_task': 'Nueva Tarea',
    'fab.quick_task': 'Tarea Rápida',
    'fab.view_tasks': 'Ver Tareas',

    // Toasts / Alerts
    'toast.task_created': '¡Tarea creada exitosamente!',
    'toast.task_updated': '¡Tarea actualizada exitosamente!',
    'toast.task_deleted': '¡Tarea eliminada exitosamente!',
    'toast.task_deleted_all': '¡Todas las tareas fueron eliminadas exitosamente!',
    'toast.task_restored': '¡Respaldo restaurado exitosamente!',
    'toast.error': 'Algo salió mal. Por favor intenta de nuevo.',

    // Validation
    'validation.required': 'Este campo es obligatorio.',
    'validation.email': 'Por favor, ingresa un correo válido.',
    'validation.min': 'El valor es muy corto.',
    'validation.max': 'El valor es muy largo.',
    'validation.date': 'Por favor, ingresa una fecha válida.',
    'validation.number': 'Por favor, ingresa un número válido.',
    'validation.confirmed': 'La confirmación no coincide.',
    'validation.unique': 'Este valor ya ha sido utilizado.',
    'validation.password': 'La contraseña debe contener al menos 8 caracteres, incluyendo una letra mayúscula, un número y un carácter especial.',
    'validation.invalid_priority': 'Por favor, selecciona una prioridad válida.',
    'validation.invalid_status': 'Por favor, selecciona un estado válido.',
    'validation.invalid_category': 'Por favor, selecciona una categoría válida.',
    'validation.file_too_large': 'El archivo es muy grande.',
    'validation.invalid_file_type': 'Tipo de archivo inválido.',

    // Holiday alerts
    'holiday.alert.title': '¡Atención!',
    'holiday.alert.body': 'La fecha de vencimiento seleccionada es feriado: {name}.',
    'holiday.alert.generic': 'La fecha de vencimiento seleccionada es feriado.',
    'holiday.state.label': 'Estado (UF)',
    
    // Holidays
    'holidays.alert': 'Alerta de Feriado',
    'holidays.on_date': 'La fecha seleccionada es feriado:',
  },
}

export function useLocale() {
  const page = usePage()
  const locale = (page.props.__LOCALE__ as Locale) || 'pt'

  const t = (key: string): string => messages[locale][key] ?? key

  const formatDate = (date: string | number | Date): string => {
    const loc = locale === 'pt' ? 'pt-BR' : locale === 'es' ? 'es-ES' : 'en-US'
    try {
      return new Intl.DateTimeFormat(loc).format(new Date(date))
    } catch {
      return String(date)
    }
  }

  const routeL = (name: string, params: Record<string, any> = {}, absolute?: boolean, config?: any) => {
    return route(name, { locale, ...params }, absolute, config)
  }

  // Adicionar suporte para currentLocale e switchLocale
  const currentLocale = computed(() => locale)
  
  const switchLocale = async (newLocale: Locale) => {
    try {
      const response = await fetch(`/api/language/${newLocale}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      })
      
      if (!response.ok) {
        throw new Error('Failed to switch language')
      }
      
      return response.json()
    } catch (error) {
      console.error('Error switching language:', error)
      throw error
    }
  }

  return { 
    locale, 
    currentLocale, 
    switchLocale, 
    t, 
    formatDate, 
    routeL 
  }
}
