<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateDashboardData extends Command
{
    protected $signature = 'dashboard:generate-data {user_id?}';
    protected $description = 'Gera dados de teste para o dashboard';

    public function handle()
    {
        $userId = $this->argument('user_id') ?? User::first()->id;
        $user = User::find($userId);

        if (!$user) {
            $this->error('Usuário não encontrado!');
            return 1;
        }

        $this->info("Gerando dados de teste para o usuário: {$user->name}");

        // Limpar tarefas existentes do usuário
        Task::where('created_by', $userId)->delete();

        $now = Carbon::now();
        $categories = ['Trabalho', 'Estudo', 'Pessoal', 'Saúde', 'Finanças'];
        $priorities = ['low', 'medium', 'high'];
        $statuses = ['pending', 'in_progress', 'completed'];

        // Gerar tarefas dos últimos 30 dias
        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            
            // 1-3 tarefas por dia
            $tasksPerDay = rand(1, 3);
            
            for ($j = 0; $j < $tasksPerDay; $j++) {
                $status = $statuses[array_rand($statuses)];
                $priority = $priorities[array_rand($priorities)];
                $category = $categories[array_rand($categories)];
                
                $task = Task::create([
                    'title' => $this->generateTaskTitle($category),
                    'description' => $this->generateTaskDescription(),
                    'status' => $status,
                    'priority' => $priority,
                    'category' => $category,
                    'due_date' => $date->copy()->addDays(rand(1, 7)),
                    'start_date' => $date->copy()->subDays(rand(0, 2)),
                    'estimated_hours' => rand(1, 8),
                    'created_by' => $userId,
                    'created_at' => $date->copy()->addHours(rand(9, 17)),
                    'updated_at' => $status === 'completed' 
                        ? $date->copy()->addHours(rand(9, 17))->addDays(rand(1, 5))
                        : $date->copy()->addHours(rand(9, 17))
                ]);

                // Adicionar algumas tags
                if (rand(0, 1)) {
                    $tags = $this->generateTags($category);
                    $task->update(['tags' => $tags]);
                }
            }
        }

        // Gerar algumas tarefas em atraso
        for ($i = 0; $i < 3; $i++) {
            Task::create([
                'title' => "Tarefa em atraso " . ($i + 1),
                'description' => 'Esta tarefa está em atraso para testar o dashboard',
                'status' => 'pending',
                'priority' => 'high',
                'category' => 'Trabalho',
                'due_date' => $now->copy()->subDays(rand(1, 10)),
                'created_by' => $userId,
                'created_at' => $now->copy()->subDays(rand(5, 15)),
            ]);
        }

        // Gerar tarefas para os próximos 7 dias
        for ($i = 1; $i <= 7; $i++) {
            $date = $now->copy()->addDays($i);
            $tasksCount = rand(1, 2);
            
            for ($j = 0; $j < $tasksCount; $j++) {
                Task::create([
                    'title' => "Tarefa futura " . ($i * 10 + $j),
                    'description' => 'Tarefa agendada para o futuro',
                    'status' => 'pending',
                    'priority' => $priorities[array_rand($priorities)],
                    'category' => $categories[array_rand($categories)],
                    'due_date' => $date,
                    'created_by' => $userId,
                    'created_at' => $now->copy()->subDays(rand(1, 5)),
                ]);
            }
        }

        $totalTasks = Task::where('created_by', $userId)->count();
        $completedTasks = Task::where('created_by', $userId)->where('status', 'completed')->count();
        $overdueTasks = Task::where('created_by', $userId)->overdue()->count();

        $this->info("✅ Dados gerados com sucesso!");
        $this->info("📊 Estatísticas:");
        $this->info("   - Total de tarefas: {$totalTasks}");
        $this->info("   - Tarefas concluídas: {$completedTasks}");
        $this->info("   - Tarefas em atraso: {$overdueTasks}");
        $this->info("   - Produtividade: " . round(($completedTasks / $totalTasks) * 100, 1) . "%");

        return 0;
    }

    private function generateTaskTitle($category)
    {
        $titles = [
            'Trabalho' => [
                'Reunião com cliente',
                'Relatório mensal',
                'Atualizar documentação',
                'Revisar código',
                'Planejamento semanal',
                'Análise de dados',
                'Preparar apresentação',
                'Responder emails'
            ],
            'Estudo' => [
                'Ler capítulo do livro',
                'Fazer exercícios práticos',
                'Assistir vídeo aula',
                'Revisar anotações',
                'Fazer resumo',
                'Praticar exercícios',
                'Pesquisar sobre tema',
                'Participar de fórum'
            ],
            'Pessoal' => [
                'Fazer exercícios',
                'Ligar para família',
                'Organizar casa',
                'Fazer compras',
                'Ler livro',
                'Assistir filme',
                'Sair com amigos',
                'Meditar'
            ],
            'Saúde' => [
                'Consulta médica',
                'Exame de sangue',
                'Fazer exercícios',
                'Beber água',
                'Dormir cedo',
                'Fazer alongamento',
                'Caminhar',
                'Preparar refeição saudável'
            ],
            'Finanças' => [
                'Pagar contas',
                'Revisar orçamento',
                'Investir dinheiro',
                'Pesquisar preços',
                'Organizar recibos',
                'Planejar gastos',
                'Consultar extrato',
                'Fazer reserva'
            ]
        ];

        $categoryTitles = $titles[$category] ?? $titles['Pessoal'];
        return $categoryTitles[array_rand($categoryTitles)];
    }

    private function generateTaskDescription()
    {
        $descriptions = [
            'Tarefa importante que precisa ser concluída com atenção aos detalhes.',
            'Atividade que contribui para o desenvolvimento pessoal e profissional.',
            'Trabalho que requer foco e dedicação para alcançar os objetivos.',
            'Projeto que visa melhorar a qualidade de vida e bem-estar.',
            'Meta que precisa ser atingida para manter a produtividade.',
            'Compromisso que deve ser cumprido dentro do prazo estabelecido.',
            'Objetivo que requer planejamento e execução cuidadosa.',
            'Desafio que testa as habilidades e conhecimentos adquiridos.'
        ];

        return $descriptions[array_rand($descriptions)];
    }

    private function generateTags($category)
    {
        $tags = [
            'Trabalho' => ['urgente', 'importante', 'cliente', 'projeto', 'deadline'],
            'Estudo' => ['curso', 'aprendizado', 'conhecimento', 'desenvolvimento', 'habilidade'],
            'Pessoal' => ['lazer', 'família', 'amigos', 'hobby', 'descanso'],
            'Saúde' => ['bem-estar', 'exercício', 'nutrição', 'medicina', 'prevenção'],
            'Finanças' => ['dinheiro', 'investimento', 'economia', 'orçamento', 'planejamento']
        ];

        $categoryTags = $tags[$category] ?? $tags['Pessoal'];
        $selectedTags = array_rand($categoryTags, min(2, count($categoryTags)));
        
        if (!is_array($selectedTags)) {
            $selectedTags = [$selectedTags];
        }

        return array_map(function($index) use ($categoryTags) {
            return $categoryTags[$index];
        }, $selectedTags);
    }
} 