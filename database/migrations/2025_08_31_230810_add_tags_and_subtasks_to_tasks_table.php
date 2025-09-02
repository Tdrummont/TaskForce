<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Tags para categorização
            $table->json('tags')->nullable()->after('description');
            
            // Campos para subtarefas
            $table->foreignId('parent_task_id')->nullable()->after('assigned_to')->constrained('tasks')->onDelete('cascade');
            $table->integer('order')->default(0)->after('parent_task_id'); // Para drag & drop
            
            // Campos para filtros avançados
            $table->date('start_date')->nullable()->after('due_date');
            $table->integer('estimated_hours')->nullable()->after('start_date');
            $table->integer('actual_hours')->nullable()->after('estimated_hours');
            $table->string('category')->nullable()->after('actual_hours');
            
            // Índices para performance
            $table->index(['parent_task_id', 'order']);
            $table->index(['tags']);
            $table->index(['category']);
            $table->index(['start_date', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['parent_task_id', 'order']);
            $table->dropIndex(['tags']);
            $table->dropIndex(['category']);
            $table->dropIndex(['start_date', 'due_date']);
            
            $table->dropForeign(['parent_task_id']);
            $table->dropColumn([
                'tags',
                'parent_task_id',
                'order',
                'start_date',
                'estimated_hours',
                'actual_hours',
                'category'
            ]);
        });
    }
};
