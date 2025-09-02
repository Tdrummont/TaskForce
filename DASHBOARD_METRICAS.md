# 📊 Dashboard - Métricas e KPIs Avançados

## 🎯 Visão Geral

O Dashboard foi completamente reformulado para incluir métricas e KPIs avançados que permitem monitorar a produtividade e o desempenho do usuário de forma detalhada.

## 📈 KPIs Principais

### 1. **Produtividade da Semana**
- **Cálculo**: (Tarefas concluídas na semana / Total de tarefas da semana) × 100
- **Exibição**: Porcentagem com ícone de gráfico
- **Cor**: Azul (#3B82F6)

### 2. **Streak de Produtividade**
- **Cálculo**: Dias consecutivos atingindo a meta diária (3 tarefas)
- **Exibição**: Número de dias com ícone de raio
- **Cor**: Verde (#10B981)
- **Máximo**: Verifica os últimos 30 dias

### 3. **Tarefas em Atraso**
- **Cálculo**: Tarefas com data de vencimento passada e status não concluído
- **Exibição**: Número com destaque vermelho
- **Cor**: Vermelho (#EF4444)
- **Destaque**: Seção especial com lista detalhada

### 4. **Tempo Médio de Conclusão**
- **Cálculo**: Média dos dias entre criação e conclusão de tarefas
- **Exibição**: Número de dias com ícone de relógio
- **Cor**: Roxo (#8B5CF6)

## 🎯 Metas vs Realizado

### **Meta Diária**
- **Meta padrão**: 3 tarefas por dia
- **Cálculo**: (Tarefas concluídas hoje / Meta diária) × 100
- **Barra de progresso**: Azul com animação
- **Máximo**: 100%

### **Meta Semanal**
- **Meta padrão**: 15 tarefas por semana
- **Cálculo**: (Tarefas concluídas na semana / Meta semanal) × 100
- **Barra de progresso**: Verde com animação
- **Máximo**: 100%

## 📊 Produtividade dos Últimos 7 Dias

### **Visualização**
- **Layout**: Grid de 7 colunas (uma para cada dia)
- **Informações por dia**:
  - Dia da semana (ex: Seg, Ter, Qua...)
  - Data (ex: 25/08, 26/08...)
  - Tarefas criadas vs concluídas
  - Barra de progresso da produtividade
  - Porcentagem de produtividade

### **Cálculo por Dia**
```javascript
produtividade = (tarefas_concluidas / tarefas_criadas) × 100
```

## 🚨 Tarefas em Atraso (Destaque)

### **Seção Especial**
- **Condição**: Aparece apenas se há tarefas em atraso
- **Fundo**: Vermelho claro (#FEF2F2)
- **Borda**: Vermelha (#FECACA)

### **Informações Exibidas**
- Título da tarefa
- Descrição
- Data de vencimento
- Dias de atraso
- Botão "Marcar como Concluída"

### **Cálculo de Dias de Atraso**
```javascript
dias_atraso = Math.ceil((hoje - data_vencimento) / (1000 * 60 * 60 * 24))
```

## 📋 Estatísticas Detalhadas

### **1. Status das Tarefas**
- **Pendentes**: Cinza
- **Em Progresso**: Amarelo
- **Concluídas**: Verde
- **Exibição**: Contador por status

### **2. Por Prioridade**
- **Baixa**: Azul
- **Média**: Laranja
- **Alta**: Vermelho
- **Exibição**: Contador por prioridade

### **3. Categorias Mais Produtivas**
- **Cálculo**: Top 5 categorias com mais tarefas concluídas
- **Exibição**: Nome da categoria + número de concluídas
- **Cor**: Verde para destacar produtividade

## 📝 Tarefas Recentes e Próximas

### **Tarefas Recentes**
- **Limite**: 5 tarefas mais recentes
- **Ordenação**: Por data de criação (mais recente primeiro)
- **Informações**:
  - Título
  - Descrição
  - Status com cor
  - Data de criação

### **Próximas Tarefas**
- **Filtro**: Tarefas não concluídas com vencimento nos próximos 7 dias
- **Limite**: 5 tarefas
- **Ordenação**: Por data de vencimento
- **Informações**:
  - Título
  - Descrição
  - Prioridade com cor
  - Data de vencimento

## 🔧 Implementação Técnica

### **Backend (TaskController)**

#### **Método `dashboard()`**
```php
public function dashboard()
{
    $userId = Auth::id();
    $now = Carbon::now();
    
    // Cálculos de métricas...
    
    return Inertia::render('Dashboard', [
        'metrics' => [
            'week_productivity' => $weekProductivity,
            'productivity_streak' => $productivityStreak,
            'overdue_tasks' => $overdueTasks,
            'avg_completion_time' => $avgCompletionTime,
            // ... outras métricas
        ],
        'overdue_tasks' => $overdueTasksList,
        'last_7_days' => $last7Days,
        // ... outros dados
    ]);
}
```

#### **Método `calculateProductivityStreak()`**
```php
private function calculateProductivityStreak($userId)
{
    $streak = 0;
    $dailyGoal = 3;
    
    // Verifica os últimos 30 dias
    for ($i = 0; $i < 30; $i++) {
        $date = Carbon::now()->subDays($i);
        $dayCompleted = Task::where('created_by', $userId)
            ->where('status', 'completed')
            ->whereDate('updated_at', $date->toDateString())
            ->count();
            
        if ($dayCompleted >= $dailyGoal) {
            $streak++;
        } else {
            break; // Streak quebrado
        }
    }
    
    return $streak;
}
```

### **Frontend (Dashboard.vue)**

#### **Estrutura de Dados**
```javascript
const props = defineProps({
    metrics: Object,
    overdue_tasks: Array,
    last_7_days: Array,
    category_productivity: Array,
    priority_stats: Array,
    status_stats: Array,
    recent_tasks: Array,
    upcoming_tasks: Array
});
```

#### **Funções Auxiliares**
```javascript
// Formatação de data
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

// Cálculo de dias em atraso
const getDaysOverdue = (dueDate) => {
    const due = new Date(dueDate);
    const today = new Date();
    const diffTime = today - due;
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

// Marcar tarefa como concluída
const markAsCompleted = (taskId) => {
    if (confirm('Marcar esta tarefa como concluída?')) {
        router.patch(route('tasks.updateStatus', taskId), {
            status: 'completed'
        });
    }
};
```

## 🎨 Design e UX

### **Layout Responsivo**
- **Grid principal**: 4 colunas em desktop, 2 em tablet, 1 em mobile
- **Metas vs Realizado**: 2 colunas em desktop, 1 em mobile
- **Produtividade 7 dias**: 7 colunas em desktop, scroll horizontal em mobile
- **Estatísticas**: 3 colunas em desktop, 1 em mobile

### **Cores e Ícones**
- **Produtividade**: Azul + ícone de gráfico
- **Streak**: Verde + ícone de raio
- **Atraso**: Vermelho + ícone de relógio
- **Tempo médio**: Roxo + ícone de relógio

### **Animações**
- **Barras de progresso**: Transição suave de 300ms
- **Hover effects**: Mudança de cor nos cards
- **Loading states**: Indicadores visuais durante carregamento

## 📊 Métricas Calculadas

### **Produtividade Semanal**
```php
$weekTasks = Task::where('created_by', $userId)
    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
    ->get();

$weekCompletedTasks = $weekTasks->where('status', 'completed')->count();
$weekProductivity = $weekTasks->count() > 0 
    ? round(($weekCompletedTasks / $weekTasks->count()) * 100, 1) 
    : 0;
```

### **Tempo Médio de Conclusão**
```php
$completedTasksWithTime = Task::where('created_by', $userId)
    ->where('status', 'completed')
    ->whereNotNull('created_at')
    ->whereNotNull('updated_at')
    ->get();

$avgCompletionTime = 0;
if ($completedTasksWithTime->count() > 0) {
    $totalDays = 0;
    foreach ($completedTasksWithTime as $task) {
        $created = Carbon::parse($task->created_at);
        $completed = Carbon::parse($task->updated_at);
        $totalDays += $created->diffInDays($completed);
    }
    $avgCompletionTime = round($totalDays / $completedTasksWithTime->count(), 1);
}
```

### **Produtividade dos Últimos 7 Dias**
```php
$last7Days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = $now->copy()->subDays($i);
    $dayTasks = Task::where('created_by', $userId)
        ->whereDate('created_at', $date->toDateString())
        ->count();
    $dayCompleted = Task::where('created_by', $userId)
        ->where('status', 'completed')
        ->whereDate('updated_at', $date->toDateString())
        ->count();
    
    $last7Days[] = [
        'date' => $date->format('d/m'),
        'day' => $date->format('D'),
        'total' => $dayTasks,
        'completed' => $dayCompleted,
        'productivity' => $dayTasks > 0 ? round(($dayCompleted / $dayTasks) * 100, 1) : 0
    ];
}
```

## 🚀 Benefícios

### **Para o Usuário**
- ✅ **Visão clara** da produtividade
- ✅ **Metas definidas** e acompanhamento
- ✅ **Alertas visuais** para tarefas em atraso
- ✅ **Motivação** através do streak
- ✅ **Análise temporal** da performance

### **Para o Sistema**
- ✅ **Dados estruturados** para relatórios
- ✅ **Métricas quantificáveis** de performance
- ✅ **Base para gamificação** futura
- ✅ **Insights** para melhorias

## 🔮 Funcionalidades Futuras

### **Gamificação**
- Badges por conquistas
- Níveis baseados em streak
- Competição entre usuários

### **Análises Avançadas**
- Gráficos de tendência
- Comparação com períodos anteriores
- Previsões de produtividade

### **Personalização**
- Metas personalizáveis
- Categorias customizáveis
- Temas visuais

---

**Status**: ✅ Implementado e Funcional
**Performance**: Otimizado com queries eficientes
**UX**: Interface moderna e responsiva
**Dados**: Métricas em tempo real 