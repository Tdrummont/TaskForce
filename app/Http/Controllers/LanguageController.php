<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Lista de idiomas suportados
     */
    protected $supportedLocales = ['en', 'pt', 'es'];

    /**
     * Alterar idioma da aplicação
     */
    public function switch(Request $request, string $locale)
    {
        // Log para debug
        \Log::info("LanguageController::switch called with locale: {$locale}");
        
        // Validar se o idioma é suportado
        if (!in_array($locale, $this->supportedLocales)) {
            return Redirect::back()->withErrors(['language' => 'Idioma não suportado.']);
        }

        // Salvar idioma na sessão
        Session::put('locale', $locale);
        
        // Definir o idioma da aplicação para esta requisição
        App::setLocale($locale);
        
        \Log::info("LanguageController::switch: Set locale to {$locale} and saved to session");

        // Retornar resposta JSON para Vue.js
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Idioma alterado com sucesso!',
                'locale' => $locale
            ]);
        }

        return Redirect::back()->with('success', 'Idioma alterado com sucesso!');
    }

    /**
     * Obter idioma atual
     */
    public function current()
    {
        // Verificar se há idioma na sessão
        $sessionLocale = Session::get('locale');
        
        // Se não há locale na sessão, usar o padrão da configuração
        if (!$sessionLocale || !in_array($sessionLocale, $this->supportedLocales)) {
            $sessionLocale = config('app.locale', 'en');
        }
        
        // Definir o idioma da aplicação
        App::setLocale($sessionLocale);
        
        $currentLocale = App::getLocale();
        
        \Log::info("LanguageController::current: App locale: {$currentLocale}, Session locale: {$sessionLocale}");
        
        return response()->json([
            'locale' => $currentLocale,
            'session_locale' => $sessionLocale,
            'supported_locales' => $this->supportedLocales
        ]);
    }

    /**
     * Obter traduções para um idioma específico
     */
    public function translations(string $locale)
    {
        if (!in_array($locale, $this->supportedLocales)) {
            return response()->json(['error' => 'Idioma não suportado'], 400);
        }

        // Carregar traduções do arquivo de idioma
        $translations = [];
        
        if ($locale === 'pt') {
            $translations = [
                'task_system' => 'Sistema de Tarefas',
                'organize_your_tasks' => 'Organize suas tarefas de forma simples e prática.',
                'login' => 'Entrar',
                'register' => 'Registrar',
                'name' => 'Nome',
                'email' => 'E-mail',
                'password' => 'Senha',
                'confirm_password' => 'Confirmar Senha',
                'already_have_an_account' => 'Já tem uma conta?',
                'welcome' => 'Bem-vindo',
                'dashboard' => 'Painel',
                'logout' => 'Sair',
                'language' => 'Idioma',
                'tasks' => 'Tarefas',
                'add_task' => 'Adicionar Tarefa',
                'edit_task' => 'Editar Tarefa',
                'delete_task' => 'Excluir Tarefa',
                'task_title' => 'Título da Tarefa',
                'task_description' => 'Descrição da Tarefa',
                'due_date' => 'Data de Vencimento',
                'priority' => 'Prioridade',
                'status' => 'Status',
                'completed' => 'Concluída',
                'pending' => 'Pendente',
                'save' => 'Salvar',
                'cancel' => 'Cancelar',
            ];
        } else {
            $translations = [
                'task_system' => 'Task System',
                'organize_your_tasks' => 'Organize your tasks simply and practically.',
                'login' => 'Login',
                'register' => 'Register',
                'name' => 'Name',
                'email' => 'Email',
                'password' => 'Password',
                'confirm_password' => 'Confirm Password',
                'already_have_an_account' => 'Already have an account?',
                'welcome' => 'Welcome',
                'dashboard' => 'Dashboard',
                'logout' => 'Logout',
                'language' => 'Language',
                'tasks' => 'Tasks',
                'add_task' => 'Add Task',
                'edit_task' => 'Edit Task',
                'delete_task' => 'Delete Task',
                'task_title' => 'Task Title',
                'task_description' => 'Task Description',
                'due_date' => 'Due Date',
                'priority' => 'Priority',
                'status' => 'Status',
                'completed' => 'Completed',
                'pending' => 'Pending',
                'save' => 'Save',
                'cancel' => 'Cancel',
            ];
        }

        return response()->json($translations);
    }

    /**
     * Obter idioma atual para o frontend
     */
    public function getCurrentLocale()
    {
        // Verificar se há idioma na sessão
        $sessionLocale = Session::get('locale');
        
        // Se não há locale na sessão, usar o padrão da configuração
        if (!$sessionLocale || !in_array($sessionLocale, $this->supportedLocales)) {
            $sessionLocale = config('app.locale', 'en');
        }
        
        return $sessionLocale;
    }
}
