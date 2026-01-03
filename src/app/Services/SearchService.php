<?php

namespace App\Services;

use App\Services\Ai\AiManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service for intelligent search functionality using AI
 */

class SearchService
{
    public function __construct(
        private ?AiManager $aiManager = null
    ) {
    }

    /**
     * Prepara termos de busca: tokeniza e expande se necessário
     * 
     * @param string $query
     * @return array Array com palavras individuais e termos expandidos
     */
    public function prepareSearchTerms(string $query): array
    {
        $query = trim($query);
        if (empty($query)) {
            return [];
        }

        // Sempre tokenizar primeiro (remove stop words e separa palavras)
        $tokenizedWords = $this->tokenizeQuery($query);
        
        // Se IA estiver disponível e habilitada, expandir termos
        if ($this->shouldExpandTerms($query)) {
            $expanded = $this->generateAiSearchTerms($query);
            // Combinar palavras tokenizadas com termos expandidos pela IA
            $allTerms = array_merge($tokenizedWords, $expanded);
            return array_unique(array_filter($allTerms));
        }

        // Sem IA, retornar apenas palavras tokenizadas
        return $tokenizedWords;
    }

    /**
     * Tokeniza a query em palavras individuais, removendo stop words
     */
    public function tokenizeQuery(string $query): array
    {
        $query = trim($query);
        
        // Remover caracteres especiais, mantendo letras, números e espaços
        $query = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $query);
        
        // Separar em palavras
        $words = preg_split('/\s+/', $query);
        
        // Remover stop words e palavras muito curtas
        $stopWords = ['de', 'da', 'do', 'das', 'dos', 'em', 'no', 'na', 'nas', 'nos', 
                      'a', 'o', 'as', 'os', 'e', 'ou', 'para', 'por', 'com', 'sem',
                      'que', 'qual', 'quais', 'um', 'uma', 'uns', 'umas'];
        
        $words = array_filter($words, function ($word) use ($stopWords) {
            $word = trim(strtolower($word));
            return strlen($word) >= 2 && !in_array($word, $stopWords);
        });
        
        return array_values(array_unique($words));
    }

    /**
     * Verifica se deve expandir termos com IA
     */
    private function shouldExpandTerms(string $query): bool
    {
        return config('ai.search_expansion_enabled', true) 
            && $this->aiManager 
            && config('ai.api_key')
            && strlen(trim($query)) >= 3;
    }

    /**
     * Gera termos de busca expandidos usando IA (método interno)
     */
    private function generateAiSearchTerms(string $query): array
    {
        // Verificar cache
        $cacheKey = 'search_terms_ai_' . md5($query);
        $cached = Cache::get($cacheKey);
        
        if ($cached !== null) {
            return $cached;
        }

        try {
            $systemPrompt = "Você é um assistente especializado em melhorar termos de busca para sistemas de helpdesk.
Dado um termo ou frase de busca, gere uma lista de palavras-chave, sinônimos e variações que ajudem a encontrar tickets relevantes.
IMPORTANTE: 
- Se a busca for uma frase, também inclua as palavras individuais importantes
- Inclua sinônimos comuns e termos técnicos relacionados
- Mantenha os termos relevantes para contextos de suporte técnico
- Retorne APENAS uma lista JSON simples de strings, sem explicações
Exemplo: Para 'Falha de dados', retorne algo como: [\"falha\", \"dados\", \"erro\", \"problema\", \"dados perdidos\", \"corrupção\"]
Mantenha tudo em português.";

            $userPrompt = "Termo de busca: \"{$query}\"\n\nGere palavras-chave e termos relacionados para busca em tickets de helpdesk. Inclua palavras individuais e sinônimos.";

            $response = $this->aiManager->client()->chat($systemPrompt, $userPrompt);
            $content = trim($response['content'] ?? '');

            // Tentar extrair JSON da resposta
            $terms = $this->extractTermsFromResponse($content, $query);
            
            // Garantir que o termo original sempre está presente
            if (!in_array($query, $terms)) {
                array_unshift($terms, $query);
            }

            // Também adicionar palavras tokenizadas se ainda não estiverem
            $tokenized = $this->tokenizeQuery($query);
            $terms = array_merge($terms, $tokenized);
            $terms = array_unique(array_filter($terms));

            // Cache por 24 horas
            Cache::put($cacheKey, $terms, now()->addHours(24));

            return $terms;
        } catch (\Exception $e) {
            Log::warning('Erro ao gerar termos de busca com IA', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            
            // Em caso de erro, retornar apenas palavras tokenizadas
            return $this->tokenizeQuery($query);
        }
    }

    /**
     * Extrai termos do JSON retornado pela IA
     */
    private function extractTermsFromResponse(string $content, string $originalQuery): array
    {
        // Tentar encontrar JSON no conteúdo
        if (preg_match('/\[.*?\]/s', $content, $matches)) {
            $json = $matches[0];
            $decoded = json_decode($json, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        // Fallback: tentar decodificar o conteúdo inteiro
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Se não conseguir parsear, retornar apenas o termo original
        return [$originalQuery];
    }

    /**
     * Prepara query para busca full-text no MySQL
     */
    public function prepareFullTextQuery(string $query): string
    {
        // Limpar e sanitizar
        $query = trim($query);
        
        // Remover caracteres especiais que podem quebrar a busca
        $query = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $query);
        
        // Remover palavras muito curtas (stop words comuns)
        $words = explode(' ', $query);
        $words = array_filter($words, function ($word) {
            $word = trim($word);
            return strlen($word) >= 2 && !in_array(strtolower($word), ['de', 'da', 'do', 'em', 'no', 'na', 'a', 'o', 'e', 'ou']);
        });
        
        return implode(' ', $words);
    }
}

