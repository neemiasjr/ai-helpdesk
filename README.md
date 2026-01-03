# 🎫 AI Helpdesk

Sistema completo de gerenciamento de tickets (helpdesk) com integração de Inteligência Artificial para automação de tarefas, desenvolvido com Laravel 12, Vue 3, Inertia.js e Tailwind CSS.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D.svg)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4.svg)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

## 📋 Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
- [Tecnologias](#tecnologias)
- [Pré-requisitos](#pré-requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Executando o Projeto](#executando-o-projeto)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Integração com IA](#integração-com-ia)
- [Usuários Padrão](#usuários-padrão)
- [Documentação Adicional](#documentação-adicional)

## 🎯 Sobre o Projeto

O **AI Helpdesk** é uma aplicação moderna de gerenciamento de tickets desenvolvida como projeto de portfólio, demonstrando conhecimentos avançados em:

- **Fullstack Development**: Laravel (backend) + Vue.js (frontend)
- **Integração de APIs de IA**: Consumo de APIs externas, gestão de prompts e tratamento robusto de erros
- **UX/UI Moderna**: Design responsivo, animações e feedback visual em tempo real
- **Arquitetura Escalável**: Jobs assíncronos, circuit breakers, rate limiting e retry logic

O sistema permite que equipes de suporte gerenciem tickets de forma eficiente, com recursos de IA para:
- Geração automática de resumos
- Classificação inteligente de tickets
- Sugestão de respostas

## ✨ Funcionalidades

### Gestão de Tickets
- ✅ Criação, edição e visualização de tickets
- ✅ Sistema de comentários em tempo real
- ✅ Filtros e busca de tickets
- ✅ Paginação responsiva
- ✅ Atribuição de tickets a agentes
- ✅ Status e prioridades configuráveis

### Inteligência Artificial
- 🤖 **Geração de Resumos**: Resumos automáticos de tickets longos
- 🏷️ **Classificação**: Classificação automática por categoria
- 💬 **Sugestão de Respostas**: Respostas sugeridas para agentes
- 🔄 **Retry Logic**: Tentativas automáticas com backoff exponencial
- 🛡️ **Circuit Breaker**: Proteção contra falhas em cascata
- ⚡ **Rate Limiting**: Controle de requisições à API

### Sistema de Usuários e Permissões
- 👥 **Três níveis de acesso**: Admin, Agente e Cliente
- 🔐 **Autenticação completa**: Login, registro e recuperação de senha
- 📝 **Perfil de usuário**: Edição de informações e alteração de senha
- 🎫 **Permissões por role**: Controle granular de acesso

### Interface Moderna
- 🎨 **Design Responsivo**: Funciona perfeitamente em desktop, tablet e mobile
- 🌙 **Dark Mode**: Suporte nativo a tema escuro
- ✨ **Animações Suaves**: Transições e feedback visual
- 🎯 **UX Intuitiva**: Navegação clara e intuitiva

## 🛠️ Tecnologias

### Backend
- **Laravel 12.x** - Framework PHP
- **PHP 8.3+** - Linguagem de programação
- **MySQL** - Banco de dados
- **Redis** - Cache e filas (opcional)
- **Laravel Queue** - Processamento assíncrono
- **Spatie Laravel Permission** - Gerenciamento de roles e permissões

### Frontend
- **Vue.js 3.x** - Framework JavaScript (Composition API)
- **Inertia.js 2.x** - Bridge entre Laravel e Vue
- **Tailwind CSS 4.x** - Framework CSS utilitário
- **Ziggy** - Rotas Laravel no JavaScript
- **Vite** - Build tool moderna

### Integração de IA
- **OpenAI API** - Provedor de IA (extensível para outros)
- **Guzzle HTTP** - Cliente HTTP
- **Jobs Assíncronos** - Processamento em background

### DevOps
- **Docker & Docker Compose** - Containerização
- **Nginx** - Servidor web
- **PHP-FPM** - Processamento PHP

## 📦 Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- **Docker** (versão 20.10 ou superior)
- **Docker Compose** (versão 2.0 ou superior)
- **Git**

> **Nota**: O projeto utiliza Docker, então você não precisa instalar PHP, Composer, Node.js ou MySQL localmente.

## 🚀 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/ai-helpdesk.git
cd ai-helpdesk
```

### 2. Copie o arquivo de ambiente

```bash
cd src
cp .env.example .env
```

### 3. Configure as variáveis de ambiente

Edite o arquivo `.env` com suas configurações:

```env
APP_NAME="AI Helpdesk"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=helpdesk
DB_USERNAME=helpdesk
DB_PASSWORD=helpdesk

QUEUE_CONNECTION=database

# Configuração de IA (OpenAI)
AI_API_KEY=sk-your-openai-api-key-here
AI_PROVIDER=openai
AI_MODEL=gpt-4o-mini
AI_MAX_RETRIES=3
AI_RETRY_BACKOFF_SECONDS=2
```

### 4. Inicie os containers Docker

Na raiz do projeto (não dentro da pasta `src`):

```bash
docker-compose up -d
```

### 5. Instale as dependências PHP

```bash
docker exec -it ai-helpdesk-app-1 composer install
```

### 6. Instale as dependências Node.js

```bash
docker exec -it ai-helpdesk-app-1 npm install
```

### 7. Gere a chave da aplicação

```bash
docker exec -it ai-helpdesk-app-1 php artisan key:generate
```

### 8. Execute as migrações e seeders

```bash
docker exec -it ai-helpdesk-app-1 php artisan migrate:fresh --seed
```

Este comando irá:
- Criar todas as tabelas do banco de dados
- Criar os roles (admin, agent, customer)
- Criar usuários padrão (veja [Usuários Padrão](#usuários-padrão))
- Popular o banco com dados de teste (50 tickets)

### 9. Compile os assets do frontend

```bash
docker exec -it ai-helpdesk-app-1 npm run build
```

Ou, para desenvolvimento com hot-reload:

```bash
docker exec -it ai-helpdesk-app-1 npm run dev
```

## ⚙️ Configuração

### Configuração do Banco de Dados

As configurações padrão no `.env` funcionam com o Docker Compose. Se precisar alterar:

```env
DB_DATABASE=ai_helpdesk
DB_USERNAME=ai_helpdesk
DB_PASSWORD=ai_helpdesk
```

### Configuração de IA (Opcional)

Para usar os recursos de IA, você precisa de uma chave da OpenAI:

1. Acesse [OpenAI Platform](https://platform.openai.com/api-keys)
2. Crie uma nova chave de API
3. Adicione no `.env`:

```env
AI_API_KEY=sk-your-key-here
```

> **Nota**: O sistema funciona sem a chave de IA, mas os recursos de IA não estarão disponíveis. Uma mensagem de erro amigável será exibida no frontend.

### Configurações Avançadas de IA

Edite `config/ai.php` para personalizar:

- Número máximo de tentativas
- Tempo de backoff entre tentativas
- Circuit breaker (ativar/desativar)
- Rate limiting
- Modelo de IA usado

## 🏃 Executando o Projeto

### Iniciar os containers

```bash
docker-compose up -d
```

### Acessar a aplicação

Abra seu navegador em: **http://localhost:8080**

### Processar jobs de IA (em outro terminal)

Os recursos de IA são processados em background. Para processar as filas:

```bash
docker exec -it ai-helpdesk-app-1 php artisan queue:work
```

Ou, para desenvolvimento com auto-reload:

```bash
docker exec -it ai-helpdesk-app-1 php artisan queue:listen
```

### Parar os containers

```bash
docker-compose down
```

### Ver logs

```bash
# Logs de todos os containers
docker-compose logs -f

# Logs apenas do app
docker exec -it ai-helpdesk-app-1 tail -f storage/logs/laravel.log
```

## 📁 Estrutura do Projeto

```
ai-helpdesk/
├── src/                          # Código-fonte da aplicação
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/      # Controladores
│   │   │   └── Middleware/       # Middlewares
│   │   ├── Jobs/Ai/              # Jobs de IA (assíncronos)
│   │   ├── Models/               # Models Eloquent
│   │   ├── Policies/             # Policies de autorização
│   │   └── Services/Ai/          # Serviços de IA
│   │       ├── Exceptions/       # Exceções customizadas
│   │       ├── Providers/        # Clientes de APIs
│   │       └── Prompts/          # Gestão de prompts
│   ├── config/                   # Arquivos de configuração
│   ├── database/
│   │   ├── factories/            # Factories para testes
│   │   ├── migrations/           # Migrações do banco
│   │   └── seeders/              # Seeders
│   ├── resources/
│   │   ├── js/                   # Código JavaScript/Vue
│   │   │   ├── Components/       # Componentes Vue reutilizáveis
│   │   │   ├── Layouts/          # Layouts Vue
│   │   │   └── Pages/            # Páginas Vue
│   │   └── views/                # Templates Blade
│   ├── routes/                   # Rotas
│   └── tests/                    # Testes
├── docker/                       # Configurações Docker
│   ├── nginx/                    # Configuração Nginx
│   └── php/                      # Dockerfile PHP
├── docker-compose.yml            # Configuração Docker Compose
└── README.md                     # Este arquivo
```

## 🤖 Integração com IA

O projeto demonstra uma integração profissional com APIs de IA, incluindo:

### Recursos Implementados

1. **Tratamento Robusto de Erros**
   - Exceções customizadas para diferentes tipos de erro
   - Tratamento específico para rate limits, timeouts, chaves inválidas, etc.

2. **Retry Logic com Exponential Backoff**
   - Tentativas automáticas em caso de falha
   - Delay exponencial entre tentativas

3. **Circuit Breaker Pattern**
   - Proteção contra falhas em cascata
   - Bloqueio temporário quando o serviço está instável

4. **Rate Limiting**
   - Controle de requisições por janela de tempo
   - Prevenção de exceder limites da API

5. **Logging Estruturado**
   - Logs detalhados de todas as requisições e respostas
   - Rastreamento de erros e métricas

6. **Validação de Configuração**
   - Middleware para verificar chave de API
   - Mensagens de erro amigáveis no frontend

Para mais detalhes, consulte: [Documentação de Integração de IA](src/docs/AI_INTEGRATION.md)

## 👥 Usuários Padrão

Após executar `php artisan migrate:fresh --seed`, os seguintes usuários são criados:

| Email | Senha | Role | Descrição |
|-------|-------|------|-----------|
| admin@example.com | password | Admin | Acesso total ao sistema |
| agent@example.com | password | Agent | Pode ver e gerenciar todos os tickets |
| customer@example.com | password | Customer | Pode criar e ver apenas seus tickets |

> **Importante**: Altere essas senhas em produção!

## 📚 Documentação Adicional

- [Integração de IA](src/docs/AI_INTEGRATION.md) - Detalhes técnicos da integração com APIs de IA
- [Políticas de Autorização](src/docs/REGISTER_POLICY.md) - Documentação sobre o sistema de permissões

## 🧪 Testes

Execute os testes com:

```bash
docker exec -it ai-helpdesk-app-1 php artisan test
```

Ou, se estiver usando Pest:

```bash
docker exec -it ai-helpdesk-app-1 ./vendor/bin/pest
```

## 🔧 Comandos Úteis

```bash
# Limpar cache
docker exec -it ai-helpdesk-app-1 php artisan cache:clear
docker exec -it ai-helpdesk-app-1 php artisan config:clear
docker exec -it ai-helpdesk-app-1 php artisan route:clear
docker exec -it ai-helpdesk-app-1 php artisan view:clear

# Recriar banco de dados
docker exec -it ai-helpdesk-app-1 php artisan migrate:fresh --seed

# Acessar o container
docker exec -it ai-helpdesk-app-1 bash

# Acessar o MySQL
docker exec -it ai-helpdesk-mysql-1 mysql -u helpdesk -phelpdesk helpdesk

# Recompilar assets
docker exec -it ai-helpdesk-app-1 npm run build
```

## 🐛 Troubleshooting

### Erro: "Connection refused" no banco de dados
- Verifique se os containers estão rodando: `docker-compose ps`
- Aguarde alguns segundos após iniciar os containers para o MySQL inicializar

### Erro: "Class not found"
- Execute: `docker exec -it ai-helpdesk-app-1 composer dump-autoload`

### Assets não carregam
- Recompile os assets: `docker exec -it ai-helpdesk-app-1 npm run build`
- Verifique se o Vite está rodando em desenvolvimento: `docker exec -it ai-helpdesk-app-1 npm run dev`

### Jobs de IA não processam
- Certifique-se de que o queue worker está rodando: `docker exec -it ai-helpdesk-app-1 php artisan queue:work`

### Erro 302 em rotas de IA
- Verifique se a chave de IA está configurada no `.env`
- O sistema redireciona de volta com mensagem de erro se a chave não estiver configurada

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 👨‍💻 Autor

Desenvolvido como projeto de portfólio para demonstrar conhecimentos em:
- Fullstack Development (Laravel + Vue.js)
- Integração de APIs de Inteligência Artificial
- UX/UI Moderna e Responsiva
- Arquitetura Escalável e Manutenível

## 🙏 Agradecimentos

- [Laravel](https://laravel.com) - Framework PHP
- [Vue.js](https://vuejs.org) - Framework JavaScript
- [Inertia.js](https://inertiajs.com) - Bridge Laravel-Vue
- [Tailwind CSS](https://tailwindcss.com) - Framework CSS
- [OpenAI](https://openai.com) - API de IA
- [Spatie](https://spatie.be) - Laravel Permission package

---

**Nota**: Este repositório contém todo o código da aplicação (sem `vendor/` e `node_modules/`).
