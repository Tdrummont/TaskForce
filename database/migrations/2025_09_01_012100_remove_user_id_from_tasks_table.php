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
            // Remover o campo user_id que está causando confusão
            // O campo created_by já é suficiente para identificar o criador da tarefa
            if (Schema::hasColumn('tasks', 'user_id')) {
                $table->dropIndex(['user_id', 'created_at']);
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Recriar o campo user_id se necessário reverter
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
        });
    }
};
