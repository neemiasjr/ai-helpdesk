<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if AI configuration is valid
 * Can be applied to routes that require AI functionality
 */
class CheckAiConfiguration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('ai.api_key');
        
        if (empty($apiKey)) {
            $errorMessage = 'Serviço de IA não configurado. Configure a chave de API (AI_API_KEY) no arquivo .env para usar os recursos de Inteligência Artificial.';
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'message' => $errorMessage,
                    'error' => 'ai_not_configured',
                ], 503);
            }

            // For Inertia requests, redirect back with errors
            // Use back() which works well with Inertia and preserves the previous URL
            return back()->withErrors([
                'ai_config' => $errorMessage,
            ])->with('error', $errorMessage);
        }

        return $next($request);
    }
}

