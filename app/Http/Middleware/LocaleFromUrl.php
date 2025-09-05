<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;

class LocaleFromUrl
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // 1) Pega o {locale} da rota
            $locale = $request->route('locale');

            // 2) Se não há locale na URL, pula a validação (para rota raiz)
            if ($locale === null) {
                return $next($request);
            }

            // 3) Valida o locale quando presente
            if (!in_array($locale, ['pt', 'en'], true)) {
                abort(404);
            }

            // 4) Seta app, sessão e defaults para gerar URLs
            App::setLocale($locale);
            Session::put('locale', $locale);           // <- ESSENCIAL
            URL::defaults(['locale' => $locale]);      // <- ESSENCIAL

            // 5) Opcional: expõe no request
            $request->attributes->set('locale', $locale);

            return $next($request);
        } catch (\Exception $e) {
            // Log do erro e continua
            \Log::error('LocaleFromUrl middleware error: ' . $e->getMessage());
            return $next($request);
        }
    }
}
