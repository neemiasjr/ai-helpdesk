# Comandos Úteis

## Docker

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f
docker exec -it ai-helpdesk-app-1 tail -f storage/logs/laravel.log

# Acessar container
docker exec -it ai-helpdesk-app-1 bash

# Acessar MySQL
docker exec -it ai-helpdesk-mysql-1 mysql -u helpdesk -phelpdesk helpdesk
```

## Laravel Artisan

```bash
# Limpar cache
docker exec -it ai-helpdesk-app-1 php artisan cache:clear
docker exec -it ai-helpdesk-app-1 php artisan config:clear
docker exec -it ai-helpdesk-app-1 php artisan route:clear
docker exec -it ai-helpdesk-app-1 php artisan view:clear

# Recriar banco de dados
docker exec -it ai-helpdesk-app-1 php artisan migrate:fresh --seed

# Processar filas
docker exec -it ai-helpdesk-app-1 php artisan queue:work
docker exec -it ai-helpdesk-app-1 php artisan queue:listen

# Autoload
docker exec -it ai-helpdesk-app-1 composer dump-autoload
```

## Frontend

```bash
# Compilar assets (produção)
docker exec -it ai-helpdesk-app-1 npm run build

# Desenvolvimento com hot-reload
docker exec -it ai-helpdesk-app-1 npm run dev
```

