# Troubleshooting

## Erro: "Connection refused" no banco de dados

- Verifique se os containers estão rodando: `docker-compose ps`
- Aguarde alguns segundos após iniciar os containers para o MySQL inicializar

## Erro: "Class not found"

```bash
docker exec -it ai-helpdesk-app-1 composer dump-autoload
```

## Assets não carregam

```bash
# Recompile os assets
docker exec -it ai-helpdesk-app-1 npm run build

# Ou em desenvolvimento
docker exec -it ai-helpdesk-app-1 npm run dev
```

## Jobs de IA não processam

Certifique-se de que o queue worker está rodando:

```bash
docker exec -it ai-helpdesk-app-1 php artisan queue:work
```

## Erro 302 em rotas de IA

- Verifique se a chave de IA está configurada no `.env`: `AI_API_KEY=sk-your-key-here`
- O sistema redireciona de volta com mensagem de erro se a chave não estiver configurada

## Porta 8080 já em uso

Edite o `docker-compose.yml` e altere a porta:

```yaml
ports:
  - "8081:80"  # Mude 8080 para 8081 ou outra porta disponível
```

