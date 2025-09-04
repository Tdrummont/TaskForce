<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $users = User::factory(3)->create();
        }

        foreach ($users as $user) {
            Task::create([
                'title' => 'Tarefa de exemplo para ' . $user->name,
                'description' => 'Esta é uma tarefa de exemplo criada automaticamente.',
                'due_date' => now()->addDays(rand(1, 30)),
                'status' => ['pending', 'in_progress', 'completed'][rand(0, 2)],
                'priority' => ['low', 'medium', 'high'][rand(0, 2)],
                'created_by' => $user->id,
                'assigned_to' => $users->random()->id,
            ]);
        }
    }
}
