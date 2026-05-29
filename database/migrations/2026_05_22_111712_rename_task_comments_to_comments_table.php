<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renomeia task_comments (sistema antigo) para comments (novo padrão)
        if (Schema::hasTable('task_comments') && !Schema::hasTable('comments')) {
            Schema::rename('task_comments', 'comments');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('comments') && !Schema::hasTable('task_comments')) {
            Schema::rename('comments', 'task_comments');
        }
    }
};

