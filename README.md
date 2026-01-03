# 🎫 AI Helpdesk

Sistema de gerenciamento de tickets com integração de Inteligência Artificial, desenvolvido com Laravel 12, Vue 3, Inertia.js e Tailwind CSS.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D.svg)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4.svg)

## ✨ Funcionalidades

- **Gestão de Tickets**: Criação, edição, filtros e busca
- **Sistema de Comentários**: Comentários em tempo real
- **Inteligência Artificial**: Resumos automáticos, classificação e sugestão de respostas
- **Sistema de Permissões**: Roles (Admin, Agente, Cliente) com controle granular
- **Interface Moderna**: Design responsivo com dark mode

## 🛠️ Tecnologias

**Backend**: Laravel 12, PHP 8.3+, MySQL, Laravel Queue  
**Frontend**: Vue.js 3, Inertia.js, Tailwind CSS 4  
**IA**: OpenAI API (extensível)  
**DevOps**: Docker, Docker Compose, Nginx

## 📦 Pré-requisitos

- Docker (20.10+)
- Docker Compose (2.0+)
- Git

## 🚀 Instalação Rápida

```bash
# 1. Clone o repositório
git clone https://github.com/seu-usuario/ai-helpdesk.git
cd ai-helpdesk

# 2. Configure o ambiente
cd src
cp .env.example .env

# 3. Configure a chave de IA (opcional)
# Edite .env e adicione: AI_API_KEY=sk-your-key-here

# 4. Inicie os containers (na raiz do projeto)
cd ..
docker-compose up -d

# 5. Instale dependências e configure
docker exec -it ai-helpdesk-app-1 composer install
docker exec -it ai-helpdesk-app-1 npm install
docker exec -it ai-helpdesk-app-1 php artisan key:generate
docker exec -it ai-helpdesk-app-1 php artisan migrate:fresh --seed
docker exec -it ai-helpdesk-app-1 npm run build
```

## 🏃 Executando

```bash
# Iniciar containers
docker-compose up -d

# Acessar aplicação
# http://localhost:8080

# Processar jobs de IA (em outro terminal)
docker exec -it ai-helpdesk-app-1 php artisan queue:work

# Parar containers
docker-compose down
```

## 👥 Usuários Padrão

| Email | Senha | Role |
|-------|-------|------|
| admin@example.com | password | Admin |
| agent@example.com | password | Agent |
| customer@example.com | password | Customer |

> ⚠️ **Importante**: Altere as senhas em produção!

## 📚 Documentação

- [Integração de IA](src/docs/AI_INTEGRATION.md) - Detalhes técnicos da integração com APIs de IA
- [Políticas de Autorização](src/docs/REGISTER_POLICY.md) - Sistema de permissões
- [Comandos Úteis](docs/COMMANDS.md) - Comandos Docker e Artisan
- [Troubleshooting](docs/TROUBLESHOOTING.md) - Solução de problemas comuns

## 🧪 Testes

```bash
docker exec -it ai-helpdesk-app-1 php artisan test
```

## 📝 Licença

MIT License - veja o arquivo [LICENSE](LICENSE) para mais detalhes.
