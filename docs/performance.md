# Guia de Otimização de Performance — VetEssence

Guia de referência para instalação e configuração de servidores rodando o VetEssence com performance adequada.

## Índice

1. [Visão Geral](#visão-geral)
2. [PHP (CLI + FPM)](#php-cli--fpm)
3. [OPcache](#opcache)
4. [PHP-FPM Pool](#php-fpm-pool)
5. [MariaDB / MySQL](#mariadb--mysql)
6. [Laravel Application](#laravel-application)
7. [Queue Worker (Daemon)](#queue-worker-daemon)
8. [Cron do Scheduler](#cron-do-scheduler)
9. [Nginx](#nginx)
10. [Server Specs Reference](#server-specs-reference)

---

## Visão Geral

As otimizações abaixo foram validadas em servidor **Ubuntu 24.04 LTS, 2 vCPUs, 4 GB RAM, MariaDB 10.11, PHP 8.4 FPM, Nginx**.

### Principais gargalos identificados

1. **OPcache desabilitado** — PHP recompila todos os arquivos a cada requisição
2. **MariaDB `innodb_buffer_pool_size` default (128 MB)** — consultas vão ao disco em vez de memória
3. **`QUEUE_CONNECTION=sync`** — jobs executam sincronamente, travando a resposta HTTP
4. **`APP_DEBUG=true` em produção** — stack traces e query logging consomem CPU/memória
5. **Sem cache de config/routes/events** — Laravel lê e processa todos os arquivos a cada requisição
6. **`memory_limit=-1` (ilimitado)** — PHP pode consumir toda RAM e ser OOM-killed
7. **FPM `pm.max_children=6`** — baixo para 4 GB RAM; limita concorrência
8. **Sem cron do scheduler** — tarefas agendadas nunca executam

---

## PHP (CLI + FPM)

### memory_limit

> **Produção:** `256M` · **Demo:** `256M`

Define o limite máximo de memória por processo PHP. Com `-1` (ilimitado), um script mal comportado pode consumir toda a RAM do servidor.

```ini
; /etc/php/8.4/fpm/conf.d/99-limits.ini
memory_limit = 256M
max_execution_time = 300
max_input_time = 60
post_max_size = 64M
upload_max_filesize = 64M
```

### max_execution_time

> **Recomendado:** `300` segundos

Tempo máximo de execução de um script. Suficiente para requisições web e fila. CLI não é afetado.

---

## OPcache

> **Recomendado:** `memory_consumption=256`, `max_accelerated_files=20000`, `revalidate_freq=2`

OPcache armazena scripts PHP compilados em memória compartilhada. **É a otimização de maior impacto** — elimina a recompilação do PHP a cada requisição.

### Verificar se está habilitado

```bash
php -m | grep opcache
# Deve exibir "Zend OPcache"
```

### Configuração

```ini
; /etc/php/8.4/fpm/conf.d/99-opcache-settings.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
opcache.enable_cli=0
opcache.validate_timestamps=1
```

### Verificar em runtime (FPM)

Crie um arquivo PHP temporário no `public/` e acesse pelo navegador:

```php
<?php
phpinfo();
```

Busque por `opcache.enable` — deve estar `On`. Remova o arquivo após verificar.

### Monitoramento

O OPcache expõe estatísticas via `opcache_get_status()`. É possível integrar com ferramentas de monitoramento ou criar um health check simples.

---

## PHP-FPM Pool

> **2 vCPUs / 4 GB RAM:** `pm.max_children=20`, `pm.start_servers=4`

Cada child do PHP-FPM consome ~15–70 MB RSS (após OPcache, ~15 MB ocioso; sob carga ~70 MB).

### Fórmula

```
pm.max_children = (RAM_total - RAM_OS - RAM_MariaDB - RAM_outros) / RSS_médio_por_child
```

- RAM total: 4.096 MB
- SO + buffers: ~1.000 MB
- MariaDB (buffer pool 1.5 GB + overhead): ~1.600 MB
- Demais serviços: ~200 MB
- Disponível para PHP: ~1.200 MB
- RSS médio por child: ~60 MB
- **max_children = 20**

### Configuração

```ini
; /etc/php/8.4/fpm/pool.d/www.conf
[www]
user = www-data
group = www-data
listen = /run/php/php8.4-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 500

pm.status_path = /status
request_terminate_timeout = 300
slowlog = /var/log/php-fpm-slow.log
request_slowlog_timeout = 10
catch_workers_output = no
security.limit_extensions = .php .phar
```

### Slow Log

Com `request_slowlog_timeout=10`, queries lentas (>10s) são registradas em `/var/log/php-fpm-slow.log`. Monitore para identificar gargalos na aplicação.

### Reiniciar após alterações

```bash
systemctl reload php8.4-fpm
# ou
systemctl restart php8.4-fpm
```

---

## MariaDB / MySQL

> **2 vCPUs / 4 GB RAM:** `innodb_buffer_pool_size = 1536M` (~75% da RAM disponível após SO)

### Configuração

```ini
; /etc/mysql/mariadb.conf.d/99-vetessence-optimizations.cnf
[mysqld]
# InnoDB
innodb_buffer_pool_size = 1536M
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1
innodb_io_capacity = 1000
innodb_read_io_threads = 4
innodb_write_io_threads = 4

# Conexões
max_connections = 50

# Tabelas temporárias
tmp_table_size = 64M
max_heap_table_size = 64M

# Query Cache (MariaDB 10.1+)
query_cache_type = 1
query_cache_size = 32M
query_cache_limit = 2M

# Buffers de query
join_buffer_size = 4M
sort_buffer_size = 4M
read_buffer_size = 2M
read_rnd_buffer_size = 2M

# Slow Query Log
slow_query_log = 1
slow_query_log_file = /var/log/mariadb-slow.log
long_query_time = 2
log_queries_not_using_indexes = 0
```

### Cálculo do buffer pool

```
innodb_buffer_pool_size = (RAM_total - RAM_OS) × 0,65
                        = (4.096 - 1.000) × 0,65
                        ≈ 2.012 MB
```

Para 4 GB: recomenda-se entre 1.5 GB e 2 GB. Ajuste conforme o tamanho real do banco:

```sql
SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024) AS db_size_mb
FROM information_schema.tables
WHERE table_schema = 'vetessence';
```

O buffer pool deve ser **maior que o banco de dados** para mantê-lo integralmente em memória.

### Verificar

```bash
mysql -e "SHOW ENGINE INNODB STATUS\G" | grep -E "Buffer pool hit rate|Free buffers"
```

```bash
mysql -e "SELECT @@innodb_buffer_pool_size, @@query_cache_type, @@max_connections;"
```

### Slow Query Log

Monitore queries lentas:

```bash
tail -f /var/log/mariadb-slow.log
```

---

## Laravel Application

### .env

```bash
# Produção
APP_ENV=production
APP_DEBUG=false

# Demo / testes (para ver erros)
APP_ENV=local
APP_DEBUG=true

# Queue
QUEUE_CONNECTION=database   # assíncrono (recomendado)
# ou
QUEUE_CONNECTION=sync       # síncrono (apenas dev)

# Cache
CACHE_DRIVER=file            # OK para 1 servidor
# ou
CACHE_DRIVER=redis           # recomendado produção

# Sessões
SESSION_DRIVER=file          # OK para 1 servidor
# ou
SESSION_DRIVER=redis         # necessário com múltiplos servidores
```

### Caching de Configuração

```bash
php artisan config:cache     # unifica config/*.php em 1 arquivo
php artisan route:cache      # serializa rotas
php artisan event:cache      # descobre event listeners
php artisan view:cache       # compila Blade
```

**Para rebuildar após alterações em config/rotas/eventos:**

```bash
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan event:cache && php artisan view:cache
```

### Composer Autoload

```bash
composer dump-autoload -o
```

Já configurado no `composer.json` com `"optimize-autoloader": true`.

### Queue Table

```bash
php artisan queue:table
php artisan migrate
```

---

## Queue Worker (Daemon)

> **Necessário quando `QUEUE_CONNECTION=database`** (ou `redis`)

Jobs de fila (notificações, NFSe, claims, etc.) executam em background, sem travar a resposta HTTP.

### Systemd Service

```ini
; /etc/systemd/system/vetessence-queue.service
[Unit]
Description=VetEssence Queue Worker
After=network.target mariadb.service php8.4-fpm.service
Requires=mariadb.service php8.4-fpm.service

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/vetessence
ExecStart=/usr/bin/php /var/www/vetessence/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --queue=default
ExecReload=/bin/kill -HUP $MAINPID
Restart=always
RestartSec=10
MemoryMax=512M

[Install]
WantedBy=multi-user.target
```

### Ativar

```bash
systemctl daemon-reload
systemctl enable vetessence-queue
systemctl start vetessence-queue
systemctl status vetessence-queue
```

### Monitorar

```bash
php artisan queue:monitor database
journalctl -u vetessence-queue -f
```

---

## Cron do Scheduler

O Laravel Schedule executa tarefas agendadas (lembretes, backups, emissão de NFSe). **Obrigatório para o funcionamento correto do sistema.**

```bash
# Instalar (executar como root ou www-data)
echo "* * * * * cd /var/www/vetessence && php artisan schedule:run >> /dev/null 2>&1" | crontab -
```

### Verificar

```bash
crontab -l
# Deve exibir a linha do schedule
```

As seguintes tarefas são executadas pelo schedule (parciais):

| Comando | Frequência |
|---------|-----------|
| `vaccines:remind` | Diário 08:00 |
| `appointments:generate-recurring` | Diário 03:00 |
| `appointments:remind` | Diário 18:00 |
| `recall:process` | Semanal |
| `birthday:process` | Diário 08:00 |
| `backup:database --compress` | Diário 01:00 |
| `backup:cleanup --keep=30` | Diário 02:00 |
| `queue:process` | A cada minuto |
| `nfse:emit-pending` | A cada 10 min |
| `nfe:emit-pending` | A cada 10 min |
| `claims:auto-file` | A cada 30 min |
| `stock:forecast --recalculate` | Diário 03:00 |
| `stock:forecast --alert-expiry` | Diário 06:00 |

---

## Nginx

### Worker Processes

```nginx
# /etc/nginx/nginx.conf
worker_processes auto;          # 1 por CPU core
worker_connections 1024;        # conexões simultâneas por worker
multi_accept on;
```

Para 2 vCPUs, `worker_processes=2` (auto detecta).

### Gzip / Brotli

```nginx
gzip on;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/javascript image/svg+xml;
gzip_min_length 256;
gzip_comp_level 5;
```

### FastCGI Cache (opcional para páginas públicas)

```nginx
fastcgi_cache_path /var/cache/nginx levels=1:2 keys_zone=VETESSENCE:10m inactive=60m;
```

---

## Server Specs Reference

### Demonstração (1–5 usuários simultâneos)

| Componente | Mínimo | Recomendado |
|---|---|---|
| CPU | 1 core | 2 cores |
| RAM | 2 GB | 4 GB |
| Disco | 10 GB SSD | 20 GB SSD |
| SO | Ubuntu 22.04+ / Debian 12+ | Ubuntu 24.04 LTS |

#### Configurações recomendadas (2 vCPUs / 4 GB)

| Serviço | Configuração |
|---------|-------------|
| OPcache | `memory_consumption=256`, `max_accelerated_files=20000` |
| PHP-FPM | `pm.max_children=20`, `pm.start_servers=4` |
| PHP memory_limit | `256M` |
| MariaDB buffer pool | `1536M` (ou 75% RAM após SO) |
| Query cache | `32M` |
| Queue | `database` + daemon systemd |
| Laravel cache | `config:cache`, `route:cache`, `event:cache` |
| Nginx workers | `auto` (2) |

### Produção (50–200 usuários simultâneos)

Recomenda-se arquitetura com 2 servidores separando aplicação e banco. Consulte a seção "Requisitos de Hardware" no `README.md` para detalhes.

---

## Troubleshooting

### "Erro 502 Bad Gateway"

Possíveis causas:
- PHP-FPM reiniciando (verifique `systemctl status php8.4-fpm`)
- `pm.max_children` esgotado (aumente ou adicione Redis para sessions/cache)
- `memory_limit` muito baixo

### "Erro 504 Gateway Timeout"

- Aumente `max_execution_time=300` no PHP
- Aumente `fastcgi_read_timeout` no Nginx

### Queries lentas

```bash
tail -f /var/log/mariadb-slow.log
tail -f /var/log/php-fpm-slow.log
```

Identifique queries lentas e considere adicionar índices. Consulte o esquema do banco para colunas frequentemente filtradas sem índice (ex.: `status`, `is_active`, `date` em tabelas grandes).

### Queue não processa

```bash
systemctl status vetessence-queue
journalctl -u vetessence-queue -n 50
php artisan queue:monitor database
```

---

## Referências

- [Laravel Deployment — Optimization](https://laravel.com/docs/deployment#optimization)
- [PHP OPcache Documentation](https://www.php.net/manual/en/book.opcache.php)
- [MariaDB — Configuring Buffer Pool](https://mariadb.com/kb/en/innodb-buffer-pool/)
- [PHP-FPM — Pool Configuration](https://www.php.net/manual/en/install.fpm.configuration.php)
