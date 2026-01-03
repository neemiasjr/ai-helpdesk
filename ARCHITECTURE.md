# Arquitetura DDD - AI Helpdesk

Este documento descreve a arquitetura Domain-Driven Design (DDD) implementada no projeto.

## 📐 Estrutura de Camadas

A aplicação está organizada em camadas seguindo os princípios de DDD:

```
app/
├── Domain/              # Camada de Domínio (Interfaces e Contratos)
│   ├── Tickets/
│   │   ├── Enums/
│   │   ├── Policies/
│   │   └── Repositories/  # Interfaces de Repositório
│   ├── Ai/
│   │   └── Repositories/
│   └── Users/
│       └── Repositories/
│
├── Infrastructure/      # Camada de Infraestrutura (Implementações)
│   └── Repositories/    # Implementações concretas dos Repositories
│
├── Services/            # Camada de Aplicação (Lógica de Negócio)
│   ├── TicketService.php
│   ├── TicketCommentService.php
│   ├── ProfileService.php
│   └── AiTicketService.php
│
├── Http/                # Camada de Apresentação
│   └── Controllers/     # Controllers (Apenas orquestração)
│
└── Models/              # Entidades do Domínio (Eloquent Models)
```

## 🎯 Princípios Aplicados

### 1. Separação de Responsabilidades

- **Controllers**: Apenas recebem requisições, chamam Services e retornam respostas
- **Services**: Contêm a lógica de negócio da aplicação
- **Repositories**: Abstraem o acesso a dados, isolando a camada de domínio da infraestrutura
- **Models**: Representam entidades do domínio (mantidos em `App\Models` seguindo convenção Laravel)

### 2. Inversão de Dependência

As interfaces dos Repositories estão no **Domain**, enquanto as implementações estão no **Infrastructure**. Os Services dependem das interfaces, não das implementações concretas.

### 3. Injeção de Dependência

Todos os Services e Repositories são registrados no `AppServiceProvider` e injetados via construtor.

## 📦 Componentes Principais

### Repositories (Infrastructure Layer)

#### TicketRepository
- **Interface**: `App\Domain\Tickets\Repositories\TicketRepositoryInterface`
- **Implementação**: `App\Infrastructure\Repositories\TicketRepository`
- **Responsabilidades**:
  - CRUD de tickets
  - Busca e filtragem
  - Paginação com filtros por usuário

#### TicketCommentRepository
- **Interface**: `App\Domain\Tickets\Repositories\TicketCommentRepositoryInterface`
- **Implementação**: `App\Infrastructure\Repositories\TicketCommentRepository`
- **Responsabilidades**:
  - CRUD de comentários
  - Busca por ticket

#### AiRunRepository
- **Interface**: `App\Domain\Ai\Repositories\AiRunRepositoryInterface`
- **Implementação**: `App\Infrastructure\Repositories\AiRunRepository`
- **Responsabilidades**:
  - CRUD de execuções de IA
  - Busca por ticket e tipo

#### UserRepository
- **Interface**: `App\Domain\Users\Repositories\UserRepositoryInterface`
- **Implementação**: `App\Infrastructure\Repositories\UserRepository`
- **Responsabilidades**:
  - CRUD de usuários
  - Busca por email

### Services (Application Layer)

#### TicketService
- **Localização**: `App\Services\TicketService`
- **Dependências**: `TicketRepositoryInterface`
- **Responsabilidades**:
  - Listar tickets com filtros
  - Criar novos tickets
  - Atualizar tickets
  - Buscar tickets com relacionamentos
  - Aplicar regras de negócio (ex: status padrão, prioridade padrão)

#### TicketCommentService
- **Localização**: `App\Services\TicketCommentService`
- **Dependências**: `TicketCommentRepositoryInterface`
- **Responsabilidades**:
  - Criar comentários
  - Listar comentários de um ticket
  - Remover comentários

#### ProfileService
- **Localização**: `App\Services\ProfileService`
- **Dependências**: `UserRepositoryInterface`
- **Responsabilidades**:
  - Atualizar perfil do usuário
  - Remover conta de usuário
  - Lógica de validação de email (limpar verificação se mudar)

#### AiTicketService
- **Localização**: `App\Services\AiTicketService`
- **Dependências**: `AiRunRepositoryInterface`
- **Responsabilidades**:
  - Enfileirar operações de IA (summarize, classify, suggest-reply)
  - Criar registros de execução
  - Despachar jobs assíncronos

### Controllers (Presentation Layer)

Os controllers foram refatorados para serem "finos" (thin controllers), contendo apenas:

1. Validação de entrada (Form Requests quando necessário)
2. Autorização (gates/policies)
3. Chamada aos Services
4. Retorno de resposta (Inertia/Redirect)

**Exemplo**:
```php
public function store(Request $request)
{
    $this->authorize('create', Ticket::class);
    
    $data = $request->validate([...]);
    
    $ticket = $this->ticketService->createTicket($request->user(), $data);
    
    return redirect()->route('tickets.show', $ticket);
}
```

## 🔄 Fluxo de Dados

```
HTTP Request
    ↓
Controller (validação, autorização)
    ↓
Service (lógica de negócio)
    ↓
Repository Interface (contrato)
    ↓
Repository Implementation (acesso a dados)
    ↓
Model (Eloquent)
    ↓
Database
```

## 🧪 Testabilidade

A arquitetura facilita testes unitários:

- **Services**: Podem ser testados injetando mocks dos Repositories
- **Repositories**: Podem ser testados isoladamente ou com testes de integração
- **Controllers**: Podem ser testados injetando mocks dos Services

## 📝 Registro de Dependências

Todas as dependências são registradas no `AppServiceProvider`:

```php
public function register(): void
{
    $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
    $this->app->bind(TicketCommentRepositoryInterface::class, TicketCommentRepository::class);
    $this->app->bind(AiRunRepositoryInterface::class, AiRunRepository::class);
    $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
}
```

## 🚀 Benefícios da Arquitetura

1. **Separação de Responsabilidades**: Cada camada tem uma responsabilidade clara
2. **Testabilidade**: Fácil criar mocks e testes unitários
3. **Manutenibilidade**: Código mais organizado e fácil de entender
4. **Extensibilidade**: Fácil adicionar novas funcionalidades sem modificar código existente
5. **Reutilização**: Services e Repositories podem ser reutilizados em diferentes contextos
6. **Inversão de Dependência**: Facilita trocar implementações (ex: mudar de Eloquent para Doctrine)

## 📚 Convenções

- **Interfaces**: Sempre no Domain, com sufixo `Interface`
- **Implementações**: Sempre no Infrastructure
- **Services**: No diretório Services, com sufixo `Service`
- **Repositories**: Implementações no Infrastructure/Repositories
- **Controllers**: Mantêm nomenclatura Laravel padrão

## 🔮 Próximos Passos (Opcional)

Para evoluir ainda mais a arquitetura, pode-se considerar:

1. **DTOs (Data Transfer Objects)**: Para transferência de dados entre camadas
2. **Value Objects**: Para representar conceitos do domínio (ex: Email, Money)
3. **Domain Events**: Para comunicação entre bounded contexts
4. **Specification Pattern**: Para regras de negócio complexas
5. **CQRS**: Separação entre comandos (write) e queries (read)

