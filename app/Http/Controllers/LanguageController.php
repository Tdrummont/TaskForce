<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class LanguageController extends Controller
{
    /**
     * Alterar o idioma da aplicação
     */
    public function changeLanguage(Request $request, $locale)
    {
        // Validar se o idioma é suportado
        if (!in_array($locale, ['pt', 'en', 'es'])) {
            $locale = 'pt';
        }

        // Salvar o idioma na sessão
        Session::put('locale', $locale);

        // Definir o idioma da aplicação
        App::setLocale($locale);

        // Redirecionar de volta com mensagem de sucesso
        return redirect()->back()->with('success', __('Idioma alterado com sucesso!'));
    }

    /**
     * Obter o idioma atual
     */
    public function getCurrentLanguage()
    {
        return response()->json([
            'current_locale' => App::getLocale(),
            'available_locales' => ['pt', 'en', 'es']
        ]);
    }
}
