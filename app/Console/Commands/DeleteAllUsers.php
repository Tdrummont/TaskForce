<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeleteAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete-all {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all users from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userCount = User::count();
        
        if ($userCount === 0) {
            $this->info('Não há usuários para excluir.');
            return 0;
        }

        $this->info("Encontrados {$userCount} usuário(s) no banco de dados.");

        if (!$this->option('force')) {
            if (!$this->confirm('Tem certeza que deseja excluir TODOS os usuários? Esta ação não pode ser desfeita.')) {
                $this->info('Operação cancelada.');
                return 0;
            }
        }

        try {
            DB::beginTransaction();

            // Excluir tokens de acesso pessoal primeiro (se existirem)
            if (Schema::hasTable('personal_access_tokens')) {
                DB::table('personal_access_tokens')->delete();
                $this->info('Tokens de acesso pessoal excluídos.');
            }

            // Excluir sessões
            DB::table('sessions')->delete();
            $this->info('Sessões excluídas.');

            // Excluir tokens de reset de senha
            DB::table('password_reset_tokens')->delete();
            $this->info('Tokens de reset de senha excluídos.');

            // Excluir todos os usuários
            User::truncate();
            $this->info('Todos os usuários foram excluídos com sucesso.');

            DB::commit();

            $this->info('✅ Operação concluída com sucesso!');
            $this->info('O sistema está pronto para ser testado do zero.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Erro ao excluir usuários: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
