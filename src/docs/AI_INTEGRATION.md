# Integração de APIs de Inteligência Artificial

Este documento descreve a implementação profissional da integração de APIs de IA no sistema, demonstrando conhecimento avançado em:

- Consumo de APIs externas
- Gestão de prompts
- Tratamento de respostas e erros

## Arquitetura

### Estrutura de Diretórios

```
app/
├── Services/Ai/
│   ├── Exceptions/          # Exceções customizadas
│   │   ├── AiException.php
│   │   ├── RateLimitException.php
│   │   ├── TimeoutException.php
│   │   ├── InvalidApiKeyException.php
│   │   ├── ServiceUnavailableException.php
│   │   └── InvalidResponseException.php
│   ├── Providers/           # Implementações de providers
│   │   └── OpenAiClient.php
│   ├── Prompts/             # Gestão de prompts
│   │   └── TicketPrompts.php
│   ├── CircuitBreaker.php   # Circuit Breaker pattern
│   ├── RateLimiter.php      # Rate limiting
│   └── AiManager.php        # Gerenciador principal
├── Jobs/Ai/                 # Jobs assíncronos
│   ├── BaseAiTicketJob.php
│   ├── SummarizeTicketJob.php
│   ├── ClassifyTicketJob.php
│   └── SuggestReplyJob.php
```

## Funcionalidades Implementadas

### 1. Consumo de APIs Externas

#### Cliente OpenAI (`OpenAiClient`)
- Implementação do padrão `AiClientInterface` para abstração
- Tratamento específico de erros HTTP
- Headers customizados (User-Agent)
- Timeout configurável
- Logging estruturado de requisições e respostas

#### Gerenciador de Providers (`AiManager`)
- Sistema extensível para múltiplos providers
- Pattern matching para seleção de provider
- Facilita adição de novos providers (Anthropic, Google, etc.)

### 2. Gestão de Prompts

#### Estrutura Organizada (`TicketPrompts`)
- Separação entre system prompts e user prompts
- Prompts específicos por tipo de operação:
  - `summarize`: Resumo de tickets
  - `classify`: Classificação automática
  - `suggestReply`: Sugestão de respostas
- Fácil manutenção e versionamento de prompts
- Prompts em português (PT-PT)

#### Exemplo de Uso

```php
$systemPrompt = TicketPrompts::summarizeSystem();
$userPrompt = TicketPrompts::summarizeUser($ticket);
```

### 3. Tratamento de Respostas e Erros

#### Exceções Customizadas

Sistema completo de exceções para diferentes tipos de erros:

- **`AiException`**: Base para todas as exceções de IA
- **`RateLimitException`**: Quando excedido limite de requisições (com `retryAfter`)
- **`TimeoutException`**: Quando timeout é excedido
- **`InvalidApiKeyException`**: Chave de API inválida ou ausente
- **`ServiceUnavailableException`**: Serviço temporariamente indisponível
- **`InvalidResponseException`**: Resposta inválida ou inesperada

#### Retry Logic com Backoff Exponencial

```php
// Configuração no config/ai.php
'max_retries' => 3,
'retry_delay_seconds' => 1,  // Base delay

// Backoff: 1s, 2s, 4s, 8s...
```

- Retry automático com backoff exponencial
- Não retenta erros de API key inválida
- Retry específico para rate limits (usa `Retry-After` header)

#### Rate Limiting

```php
// Previne exceder limites da API
'rate_limit_enabled' => true,
'rate_limit_max_requests' => 60,
'rate_limit_window_seconds' => 60,
```

- Controle de requisições por janela de tempo
- Prevenção de exceder limites da API
- Logging de tentativas bloqueadas

#### Circuit Breaker Pattern

```php
// Previne cascading failures
'circuit_breaker_enabled' => true,
```

- Abre circuito após 5 falhas consecutivas
- Bloqueia requisições por 60 segundos
- Transição para half-open após 30 segundos
- Fecha automaticamente após sucessos

**Estados:**
- `closed`: Normal, requisições permitidas
- `open`: Bloqueado após muitas falhas
- `half-open`: Testando se serviço recuperou

#### Validação de Respostas

```php
'validate_responses' => true,
'min_response_length' => 10,
'max_response_length' => 50000,
```

- Validação de tamanho mínimo/máximo
- Prevenção de respostas vazias
- Tratamento de respostas inválidas

### 4. Logging Estruturado

```php
// Logs incluem:
- Provider usado
- Model utilizado
- Tempo de resposta (duration_ms)
- Tipo de erro
- Status code HTTP
- Tentativas (attempts)
- Usage tokens (quando disponível)
```

**Níveis de Log:**
- `info`: Requisições bem-sucedidas
- `warning`: Rate limits, circuit breaker aberto
- `error`: Falhas com detalhes completos
- `debug`: Respostas completas (opcional)

### 5. Processamento Assíncrono

Todos os jobs de IA são processados assincronamente:

- **Queue**: `ai` (fila dedicada)
- **Jobs**: Extendem `BaseAiTicketJob`
- **Tracking**: Cada execução é registrada em `ai_runs`
- **Estado**: `queued`, `running`, `success`, `failed`

## Configuração

### Variáveis de Ambiente

```env
# Provider
AI_PROVIDER=openai

# Credenciais
AI_API_KEY=sk-...

# Modelo
AI_MODEL=gpt-4o-mini

# Timeout
AI_TIMEOUT_SECONDS=30

# Retry
AI_MAX_RETRIES=3
AI_RETRY_DELAY_SECONDS=1

# Rate Limiting
AI_RATE_LIMIT_ENABLED=true
AI_RATE_LIMIT_MAX_REQUESTS=60
AI_RATE_LIMIT_WINDOW_SECONDS=60

# Circuit Breaker
AI_CIRCUIT_BREAKER_ENABLED=true

# Validação
AI_VALIDATE_RESPONSES=true
AI_MIN_RESPONSE_LENGTH=10
AI_MAX_RESPONSE_LENGTH=50000

# Logging
AI_LOG_REQUESTS=true
AI_LOG_RESPONSES=false

# Opções do Modelo
AI_TEMPERATURE=0.2
AI_MAX_TOKENS=2000
```

## Uso

### No Controller

```php
// AiTicketController.php
public function summarize(Request $request, Ticket $ticket)
{
    $this->authorize('useAi', $ticket);
    
    $run = AiRun::create([...]);
    dispatch(new SummarizeTicketJob($run->id, $ticket->id))
        ->onQueue('ai');
    
    return back();
}
```

### No Job

```php
// SummarizeTicketJob.php
class SummarizeTicketJob extends BaseAiTicketJob
{
    protected function systemPrompt(Ticket $ticket): string
    {
        return TicketPrompts::summarizeSystem();
    }
    
    protected function userPrompt(Ticket $ticket): string
    {
        return TicketPrompts::summarizeUser($ticket);
    }
}
```

## Métricas e Monitoramento

### Dados Armazenados em `ai_runs`

- `status`: queued, running, success, failed
- `duration_ms`: Tempo de processamento
- `attempt`: Número de tentativas
- `error_message`: Mensagem de erro (se falhou)
- `provider`: Provider usado
- `model`: Modelo usado
- `prompt`: Prompt completo enviado
- `response`: Resposta da IA
- `usage`: Tokens usados (quando disponível)

## Boas Práticas Implementadas

1. **Separação de Responsabilidades**: Cada classe tem uma responsabilidade única
2. **Interface Abstração**: `AiClientInterface` permite múltiplos providers
3. **Error Handling**: Exceções específicas para cada tipo de erro
4. **Retry Logic**: Backoff exponencial inteligente
5. **Rate Limiting**: Prevenção de exceder limites
6. **Circuit Breaker**: Prevenção de cascading failures
7. **Logging**: Logging estruturado para debug e monitoramento
8. **Validação**: Validação de respostas antes de salvar
9. **Configuração**: Sistema de configuração flexível
10. **Processamento Assíncrono**: Jobs em queue para não bloquear requests

## Extensibilidade

### Adicionar Novo Provider

1. Criar classe que implementa `AiClientInterface`
2. Adicionar no `AiManager::client()`
3. Configurar no `.env`

### Adicionar Novo Tipo de Operação IA

1. Criar métodos em `TicketPrompts`
2. Criar Job que estende `BaseAiTicketJob`
3. Adicionar rota e método no `AiTicketController`

## Conclusão

Esta implementação demonstra conhecimento avançado em:

✅ Consumo profissional de APIs externas  
✅ Gestão organizada de prompts  
✅ Tratamento robusto de erros e respostas  
✅ Padrões de design (Circuit Breaker, Retry, Rate Limiting)  
✅ Processamento assíncrono  
✅ Logging e monitoramento  
✅ Configuração flexível  
✅ Código extensível e manutenível  

