<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Adicionar coluna user_id como nullable primeiro
            if (!Schema::hasColumn('tasks', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
            
            // Adicionar índice para performance
            $table->index(['user_id', 'created_at']);
        });

        // Atualizar registros existentes com o primeiro usuário disponível
        $firstUser = DB::table('users')->first();
        if ($firstUser) {
            DB::table('tasks')->whereNull('user_id')->update(['user_id' => $firstUser->id]);
        }

        // Tornar a coluna NOT NULL após atualizar os dados
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
