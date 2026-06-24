#!/usr/bin/env bash
#==============================================================================
# VetEssence - Script de Instalação Completa
#==============================================================================
# Uso: sudo bash install.sh
#==============================================================================
set -euo pipefail

# ── Cores ──────────────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; CYAN='\033[0;36m'; NC='\033[0m'
INFO="${CYAN}[INFO]${NC}"; OK="${GREEN}[OK]${NC}"; WARN="${YELLOW}[AVISO]${NC}"; ERR="${RED}[ERRO]${NC}"

# ── Funções auxiliares ─────────────────────────────────────────────────────
log()  { echo -e "${INFO} $*"; }
ok()   { echo -e "${OK} $*"; }
warn() { echo -e "${WARN} $*"; }
fail() { echo -e "${ERR} $*"; exit 1; }

# Obtém o usuário original (aquele que invocou sudo, ou root se for direto)
ORIGINAL_USER="${SUDO_USER:-$USER}"
ORIGINAL_HOME=$(eval echo "~${ORIGINAL_USER}")

prompt() {
    local var="$1" msg="$2" default="${3:-}"
    if [[ -n "$default" ]]; then
        read -rp "$(echo -e "${CYAN}${msg}${NC} [${default}]: ")" val
        echo "${val:-$default}"
    else
        read -rp "$(echo -e "${CYAN}${msg}${NC}: ")" val
        echo "$val"
    fi
}

prompt_secret() {
    local var="$1" msg="$2"
    read -rsp "$(echo -e "${CYAN}${msg}${NC}: ")" val
    echo
    echo "$val"
}

SUDO=""
if [[ $EUID -ne 0 ]]; then
    if command -v sudo &>/dev/null; then
        SUDO="sudo"
        warn "Este script precisa de privilégios root. Usarei 'sudo' automaticamente."
    else
        fail "Execute como root ou tenha sudo disponível."
    fi
fi

# ── Banner ─────────────────────────────────────────────────────────────────
echo -e "${BLUE}"
cat << 'EOF'
 __     __        _   _____
 \ \   / /__  ___| |_| ____|___  ___  ___  ___ _ __ ___
  \ \ / / _ \/ __| __|  _| / _ \/ __|/ _ \/ _ \ '_ ` _ \
   \ V /  __/\__ \ |_| |__|  __/\__ \  __/  __/ | | | | |
    \_/ \___||___/\__|_____\___||___/\___|\___|_| |_| |_|
EOF
echo -e "${NC}"
echo -e "${CYAN}═══ Script de Instalação Completa ═══${NC}"
echo ""

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 1:  Verificação de dependências do sistema
# ═══════════════════════════════════════════════════════════════════════════
log "Verificando dependências do sistema..."

REQUIRED_CMDS=(php composer node npm git curl unzip)
MISSING=()
for cmd in "${REQUIRED_CMDS[@]}"; do
    if ! command -v "$cmd" &>/dev/null; then
        MISSING+=("$cmd")
    fi
done

if [[ ${#MISSING[@]} -gt 0 ]]; then
    echo -e "${WARN} Faltam os seguintes comandos: ${MISSING[*]}"
    echo -e "${INFO} Eles serão instalados automaticamente na Etapa 3."
    INSTALL_DEPS=true
else
    ok "Todas as dependências básicas estão instaladas."
    INSTALL_DEPS=false
fi

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 2:  Coleta de informações do usuário
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   CONFIGURAÇÃO DO SISTEMA               ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

APP_URL=$(prompt "APP_URL" "Domínio completo do sistema (ex: https://vetessence.com.br)" "http://localhost")
APP_NAME=$(prompt "APP_NAME" "Nome do sistema" "VetEssence")
ADMIN_EMAIL=$(prompt "ADMIN_EMAIL" "E-mail do super-admin" "super@vet.com")
ADMIN_PASS=$(prompt_secret "ADMIN_PASS" "Senha do super-admin")
TIMEZONE=$(prompt "TIMEZONE" "Fuso horário (ex: America/Sao_Paulo)" "America/Sao_Paulo")

echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   GITHUB (para atualizações do sistema) ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"
GITHUB_USER=$(prompt "GITHUB_USER" "Usuário do GitHub (owner do repositório)" "hectordufau")
GITHUB_REPO="${GITHUB_USER}/vetessence"
echo -e "${INFO} Repositório: ${GITHUB_REPO}"
GITHUB_TOKEN=$(prompt_secret "GITHUB_TOKEN" "Personal Access Token (PAT) com acesso ao repositório")
GITHUB_BRANCH=$(prompt "GITHUB_BRANCH" "Branch principal" "main")

echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   BANCO DE DADOS                        ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

DB_TYPE=$(prompt "DB_TYPE" "Instalar banco localmente (L) ou usar remoto (R)" "L")
DB_CONNECTION="mysql"
DB_PORT="3306"

if [[ "$DB_TYPE" =~ ^[Ll]$ ]]; then
    INSTALL_LOCAL_DB=true
    DB_HOST="127.0.0.1"
    log "O MySQL/MariaDB será instalado localmente."
    DB_DATABASE=$(prompt "DB_DATABASE" "Nome do banco de dados" "vetessence")
    DB_USERNAME=$(prompt "DB_USERNAME" "Usuário do banco" "vetessence")
    DB_PASSWORD=$(prompt_secret "DB_PASSWORD" "Senha do banco")
else
    INSTALL_LOCAL_DB=false
    DB_HOST=$(prompt "DB_HOST" "Host do banco remoto" "127.0.0.1")
    DB_PORT=$(prompt "DB_PORT" "Porta" "3306")
    DB_DATABASE=$(prompt "DB_DATABASE" "Nome do banco de dados" "vetessence")
    DB_USERNAME=$(prompt "DB_USERNAME" "Usuário" "vetessence")
    DB_PASSWORD=$(prompt_secret "DB_PASSWORD" "Senha")
fi

echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   E-MAIL (opcional)                     ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

CONFIGURE_MAIL=$(prompt "CONFIGURE_MAIL" "Configurar envio de e-mail? (S/n)" "S")
if [[ "$CONFIGURE_MAIL" =~ ^[Ss]$ ]]; then
    MAIL_MAILER=$(prompt "MAIL_MAILER" "Driver de e-mail (smtp/mailgun/ses/sendgrid)" "smtp")
    MAIL_HOST=$(prompt "MAIL_HOST" "Host SMTP" "")
    MAIL_PORT=$(prompt "MAIL_PORT" "Porta SMTP" "587")
    MAIL_USERNAME=$(prompt "MAIL_USERNAME" "Usuário SMTP" "")
    MAIL_PASSWORD=$(prompt_secret "MAIL_PASSWORD" "Senha SMTP")
    MAIL_ENCRYPTION=$(prompt "MAIL_ENCRYPTION" "Criptografia (tls/ssl)" "tls")
    MAIL_FROM_ADDRESS=$(prompt "MAIL_FROM_ADDRESS" "E-mail de envio" "$ADMIN_EMAIL")
else
    MAIL_MAILER="smtp"
    MAIL_HOST="localhost"
    MAIL_PORT="1025"
    MAIL_USERNAME=""
    MAIL_PASSWORD=""
    MAIL_ENCRYPTION=""
    MAIL_FROM_ADDRESS="$ADMIN_EMAIL"
fi



# ═══════════════════════════════════════════════════════════════════════════
# RESUMO E CONFIRMAÇÃO
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   RESUMO DA INSTALAÇÃO                  ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e " ${CYAN}Domínio:${NC}          $APP_URL"
echo -e " ${CYAN}Nome do sistema:${NC}  $APP_NAME"
echo -e " ${CYAN}GitHub:${NC}           $GITHUB_REPO ($GITHUB_BRANCH)"
echo -e " ${CYAN}Banco:${NC}            $DB_CONNECTION://$DB_HOST:$DB_PORT/$DB_DATABASE"
echo -e " ${CYAN}Super-admin:${NC}      $ADMIN_EMAIL"
echo ""

CONFIRM=$(prompt "CONFIRM" "Confirmar instalação com estas configurações? (S/n)" "S")
if [[ ! "$CONFIRM" =~ ^[Ss]$ ]]; then
    fail "Instalação cancelada pelo usuário."
fi

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 3:  Instalação de dependências do sistema
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 3: DEPENDÊNCIAS DO SISTEMA      ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

# ── Detectar distribuição ─────────────────────────────────────────────
if [[ -f /etc/os-release ]]; then
    . /etc/os-release
    OS_ID="${ID}"        # ubuntu | debian
    OS_VERSION="${VERSION_ID}"
else
    fail "Não foi possível detectar o sistema operacional."
fi
log "Distribuição detectada: ${OS_ID} ${OS_VERSION}"

# ── Verificar o que precisa de apt ────────────────────────────────────
NEEDS_APT=false

if ! command -v php &>/dev/null; then
    NEEDS_APT=true
fi
if ! command -v node &>/dev/null || ! command -v npm &>/dev/null; then
    NEEDS_APT=true
fi
for cmd in curl git unzip zip nginx; do
    if ! command -v "$cmd" &>/dev/null; then
        NEEDS_APT=true
    fi
done
if [[ "$INSTALL_LOCAL_DB" == true ]] && ! command -v mysql &>/dev/null; then
    NEEDS_APT=true
fi

# ── Repositórios extras (apenas se for instalar algo) ─────────────────
if [[ "$NEEDS_APT" == true ]]; then
    log "Instalando pacotes auxiliares (software-properties-common, ca-certificates)..."
    $SUDO apt-get update -qq
    $SUDO apt-get install -y -qq software-properties-common ca-certificates gnupg 2>&1 | tail -2

    # PHP: PPA do ondrej (Ubuntu) ou sury.org (Debian)
    if ! command -v php &>/dev/null; then
        if [[ "$OS_ID" == "ubuntu" ]]; then
            log "Adicionando PPA ondrej/php..."
            $SUDO add-apt-repository -y ppa:ondrej/php 2>&1 | tail -2
        elif [[ "$OS_ID" == "debian" ]]; then
            log "Adicionando repositório sury.org (PHP para Debian)..."
            $SUDO curl -fsSL https://packages.sury.org/php/apt.gpg -o /usr/share/keyrings/sury-php.gpg 2>/dev/null || \
            $SUDO curl -fsSL https://packages.sury.org/php/apt.gpg | gpg --dearmor | $SUDO tee /usr/share/keyrings/sury-php.gpg >/dev/null
            echo "deb [signed-by=/usr/share/keyrings/sury-php.gpg] https://packages.sury.org/php/ ${OS_VERSION} main" | $SUDO tee /etc/apt/sources.list.d/sury-php.list >/dev/null
        fi
    fi

    # NodeSource
    if ! command -v node &>/dev/null || [[ $(node -v 2>/dev/null | sed 's/v//' | cut -d. -f1) -lt 18 ]]; then
        log "Adicionando NodeSource 20.x..."
        curl -fsSL https://deb.nodesource.com/setup_20.x | $SUDO bash - 2>&1 | tail -2
    fi

    $SUDO apt-get update -qq
fi

# ── Pacotes base ──────────────────────────────────────────────────────
BASE_PKGS=""
for pkg in curl wget git unzip zip nginx; do
    if ! command -v "$pkg" &>/dev/null; then
        BASE_PKGS="$BASE_PKGS $pkg"
    fi
done
if [[ -n "$BASE_PKGS" ]]; then
    log "Instalando pacotes base:$BASE_PKGS"
    $SUDO apt-get install -y -qq $BASE_PKGS 2>&1 | tail -3
fi

# ── Node.js e npm ─────────────────────────────────────────────────────
if ! command -v node &>/dev/null; then
    log "Instalando Node.js..."
    $SUDO apt-get install -y -qq nodejs 2>&1 | tail -3
fi
if ! command -v npm &>/dev/null; then
    log "Instalando npm..."
    $SUDO apt-get install -y -qq npm 2>&1 | tail -3
fi
ok "Node.js $(node -v 2>/dev/null), npm $(npm -v 2>/dev/null)."

# ── PHP ────────────────────────────────────────────────────────────────
# Detecta a versão mais recente do PHP 8.x disponível nos repositórios
PHP_VER=""
if command -v php &>/dev/null; then
    PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null)
else
    PHP_AVAIL=$($SUDO apt-cache search '^php8\.[0-9]+-cli$' 2>/dev/null | sed 's/-cli.*//' | sort -V | tail -1 || echo "php8.4")
    PHP_VER="${PHP_AVAIL#php}"
fi

PHP_PKGS=""
for pkg in php"${PHP_VER}"-cli php"${PHP_VER}"-common php"${PHP_VER}"-fpm \
           php"${PHP_VER}"-mysql php"${PHP_VER}"-xml php"${PHP_VER}"-mbstring \
           php"${PHP_VER}"-curl php"${PHP_VER}"-gd php"${PHP_VER}"-zip \
           php"${PHP_VER}"-bcmath php"${PHP_VER}"-intl php"${PHP_VER}"-redis; do
    if ! dpkg -s "$pkg" &>/dev/null 2>&1; then
        PHP_PKGS="$PHP_PKGS $pkg"
    fi
done
if [[ -n "$PHP_PKGS" ]]; then
    log "Instalando PHP ${PHP_VER}:$PHP_PKGS"
    $SUDO apt-get install -y -qq $PHP_PKGS 2>&1 | tail -5
fi

# Verifica versão do PHP
PHP_VER_CHECK=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo "0.0")
log "Versão do PHP instalada: ${PHP_VER_CHECK}"
if [[ $(echo "$PHP_VER_CHECK 8.2" | awk '{print ($1 < $2) ? 1 : 0}') -eq 1 ]]; then
    fail "PHP 8.2+ é necessário. Versão detectada: ${PHP_VER_CHECK}"
fi
ok "PHP ${PHP_VER_CHECK} OK."

# ── Composer ───────────────────────────────────────────────────────────
if ! command -v composer &>/dev/null; then
    log "Instalando Composer..."
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_CHECKSUM="$(php -r 'echo hash_file("sha384", "composer-setup.php");')"
    if [[ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]]; then
        rm composer-setup.php
        fail "Composer installer corrupt"
    fi
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
    ok "Composer $(composer --version 2>/dev/null | head -1) instalado."
else
    ok "Composer $(composer --version 2>/dev/null | head -1) já instalado."
fi

# ── MySQL (se for instalação local) ────────────────────────────────────
if [[ "$INSTALL_LOCAL_DB" == true ]]; then
    if ! command -v mysql &>/dev/null; then
        log "Instalando MySQL Server..."
        if [[ "$OS_ID" == "ubuntu" ]]; then
            $SUDO apt-get install -y -qq mysql-server 2>&1 | tail -5
        elif [[ "$OS_ID" == "debian" ]]; then
            $SUDO apt-get install -y -qq default-mysql-server mariadb-server 2>&1 | tail -5
            log "Debian detectado — MariaDB será usado como drop-in do MySQL."
        fi
    else
        ok "MySQL já instalado."
    fi

    log "Iniciando MySQL..."
    $SUDO systemctl enable mysql --now 2>/dev/null || $SUDO systemctl enable mariadb --now 2>/dev/null || true

    log "Criando banco de dados e usuário..."
    $SUDO mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
    $SUDO mysql -e "CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';" 2>/dev/null || true
    $SUDO mysql -e "GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'localhost'; FLUSH PRIVILEGES;" 2>/dev/null || true
fi

ok "Todas as dependências do sistema instaladas."

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 4:  Clonar repositório
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 4: CLONAR REPOSITÓRIO           ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

APP_DIR=$(prompt "APP_DIR" "Diretório de instalação" "/var/www/vetessence")

if [[ -d "$APP_DIR" && -f "$APP_DIR/artisan" ]]; then
    warn "Já existe uma instalação do Laravel em ${APP_DIR}."
    REINSTALL=$(prompt "REINSTALL" "Deseja reinstalar sobrescrevendo? (s/N)" "N")
    if [[ "$REINSTALL" =~ ^[Ss]$ ]]; then
        $SUDO rm -rf "$APP_DIR"
    else
        log "Usando diretório existente. Ignorando clone..."
        SKIP_CLONE=true
    fi
fi

if [[ "${SKIP_CLONE:-false}" != true ]]; then
    log "Clonando repositório ${GITHUB_REPO}..."
    $SUDO mkdir -p "$(dirname "$APP_DIR")"
    GIT_URL="https://${GITHUB_TOKEN}@github.com/${GITHUB_REPO}.git"
    $SUDO git clone --branch "$GITHUB_BRANCH" "$GIT_URL" "$APP_DIR"
    ok "Repositório clonado em ${APP_DIR}"
fi

$SUDO chown -R "${ORIGINAL_USER}:${ORIGINAL_USER}" "$APP_DIR" 2>/dev/null || true

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 5:  Configurar ambiente (.env)
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 5: CONFIGURAR .ENV              ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

cd "$APP_DIR"

if [[ -f .env ]]; then
    $SUDO cp .env ".env.backup.$(date +%Y%m%d%H%M%S)"
    warn "Backup do .env existente criado."
fi

$SUDO cp .env.example .env

# Gerar APP_KEY
log "Gerando APP_KEY..."
$SUDO php artisan key:generate --force
APP_KEY=$(grep -oP '^APP_KEY=\K.*' .env || "")

# Escrever .env
log "Escrevendo configurações no .env..."
$SUDO tee .env > /dev/null <<ENVEOF
APP_NAME="${APP_NAME}"
APP_ENV=production
APP_KEY=${APP_KEY:-base64:$(openssl rand -base64 32)}
APP_DEBUG=false
APP_URL=${APP_URL}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=${DB_CONNECTION}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=${MAIL_MAILER}
MAIL_HOST=${MAIL_HOST}
MAIL_PORT=${MAIL_PORT}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"

ENVEOF

ok ".env configurado."

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 6:  Instalar dependências Composer e NPM
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 6: DEPENDÊNCIAS PHP E JS        ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

log "Instalando dependências Composer (produção)..."
$SUDO composer install --no-dev --optimize-autoloader --no-interaction --quiet
ok "Composer OK."

log "Instalando dependências NPM..."
$SUDO npm install --no-audit --no-fund --silent 2>&1 | tail -3
ok "NPM install OK."

log "Compilando assets (Mix)..."
$SUDO npm run production --silent 2>&1 | tail -5
ok "Assets compilados."

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 7:  Configurar permissões e storage
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 7: PERMISSÕES                   ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

WWW_USER="www-data"

$SUDO mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
$SUDO chmod -R 775 storage bootstrap/cache public/img public/storage
$SUDO chown -R "${WWW_USER}:${WWW_USER}" storage bootstrap/cache public/img

if [[ -d public/storage ]]; then
    $SUDO rm -f public/storage
fi
$SUDO php artisan storage:link --force
ok "Permissões configuradas."

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 8:  Migrations e Seeders
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 8: BANCO DE DADOS               ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

log "Criando tabela queue (jobs)..."
$SUDO php artisan queue:table --quiet 2>/dev/null || true

log "Executando migrations..."
$SUDO php artisan migrate --force --seed --no-interaction
ok "Migrations e seeders executados."

log "Salvando configurações do GitHub no banco..."
$SUDO php artisan tinker --execute="
    \\App\\Models\\Setting::set('github_token', '${GITHUB_TOKEN}');
    \\App\\Models\\Setting::set('github_repo', '${GITHUB_REPO}');
    \\App\\Models\\Setting::set('github_branch', '${GITHUB_BRANCH}');
" 2>/dev/null
ok "Configurações de atualização salvas."

# ── Alterar senha do admin ─────────────────────────────────────────────────
if [[ -n "$ADMIN_PASS" ]]; then
    log "Alterando senha do administrador (${ADMIN_EMAIL})..."
    $SUDO php artisan tinker --execute="
        \$u = \\App\\Models\\User::where('email', '${ADMIN_EMAIL}')->first();
        if (\$u) { \$u->password = bcrypt('${ADMIN_PASS}'); \$u->save(); }
    " 2>/dev/null
    ok "Senha do administrador alterada."
fi

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 9:  Configurar Nginx
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 9: NGINX                        ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

# Garante que PHP_VER esteja definido (pode não estar se INSTALL_DEPS=false)
if [[ -z "${PHP_VER:-}" ]]; then
    PHP_VER_CHECK=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo "8.4")
    PHP_VER="${PHP_VER_CHECK}"
fi

DOMAIN=$(echo "$APP_URL" | sed -E 's|^https?://||' | sed 's|/.*$||')
NGINX_CONF="/etc/nginx/sites-available/vetessence"

log "Criando configuração Nginx para ${DOMAIN}..."

$SUDO tee "$NGINX_CONF" > /dev/null <<NGINXEOF
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN};
    root ${APP_DIR}/public;

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log  /var/log/nginx/vetessence_access.log;
    error_log   /var/log/nginx/vetessence_error.log;

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php${PHP_VER}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ /\.ht {
        deny all;
    }

    # Segurança
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;
}
NGINXEOF

# Ativar site
if [[ -L /etc/nginx/sites-enabled/vetessence ]]; then
    $SUDO rm /etc/nginx/sites-enabled/vetessence
fi
$SUDO ln -s "$NGINX_CONF" /etc/nginx/sites-enabled/

# Remover default
if [[ -L /etc/nginx/sites-enabled/default ]]; then
    $SUDO rm /etc/nginx/sites-enabled/default 2>/dev/null || true
fi

# Testar configuração
log "Testando configuração do Nginx..."
$SUDO nginx -t 2>&1 | tail -2

log "Reiniciando Nginx..."
$SUDO systemctl enable nginx --now 2>/dev/null || true
$SUDO systemctl restart nginx
ok "Nginx configurado."

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 10:  Cache de otimização
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 10: OTIMIZAÇÃO                  ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

$SUDO php artisan config:cache --quiet
$SUDO php artisan route:cache --quiet
$SUDO php artisan view:cache --quiet
$SUDO php artisan event:cache --quiet
ok "Cache de otimização gerado."

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 11:  Cron (agendador Laravel)
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 11: CRON                        ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

CRON_LINE="* * * * * ${WWW_USER} php ${APP_DIR}/artisan schedule:run >> /dev/null 2>&1"
if $SUDO crontab -u "${WWW_USER}" -l 2>/dev/null | grep -qF "$CRON_LINE"; then
    ok "Cron já configurado."
else
    ($SUDO crontab -u "${WWW_USER}" -l 2>/dev/null; echo "$CRON_LINE") | $SUDO crontab -u "${WWW_USER}" -
    ok "Cron configurado para o usuário ${WWW_USER}."
fi

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 12:  Supervisor (fila de jobs)
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 12: SUPERVISOR (QUEUE)          ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

if command -v supervisorctl &>/dev/null; then
    SUP_CFG="/etc/supervisor/conf.d/vetessence-queue.conf"
    $SUDO tee "$SUP_CFG" > /dev/null <<SUPEOF
[program:vetessence-queue]
process_name=%(program_name)s_%(process_num)02d
command=php ${APP_DIR}/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=${WWW_USER}
numprocs=2
redirect_stderr=true
stdout_logfile=${APP_DIR}/storage/logs/queue-worker.log
stdout_logfile_maxbytes=50MB
stdout_logfile_backups=5
SUPEOF

    $SUDO supervisorctl reread 2>/dev/null || true
    $SUDO supervisorctl update 2>/dev/null || true
    $SUDO supervisorctl start vetessence-queue:* 2>/dev/null || true
    ok "Supervisor configurado para a fila."
else
    warn "Supervisor não encontrado. Instale com: sudo apt-get install -y supervisor"
fi

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 13:  Cron — Agendador do Laravel
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   ETAPA 13: CRON (SCHEDULER)            ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"

CRON_LINE="* * * * * ${WWW_USER} cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1"
if command -v crontab &>/dev/null; then
    # Adiciona ao crontab do www-user ou root — o ideal é /etc/cron.d/
    CRON_FILE="/etc/cron.d/vetessence-scheduler"
    if [[ ! -f "$CRON_FILE" ]]; then
        echo "# Executa o agendador Laravel a cada minuto" | $SUDO tee "$CRON_FILE" > /dev/null
        echo "${CRON_LINE}" | $SUDO tee -a "$CRON_FILE" > /dev/null
        $SUDO chmod 644 "$CRON_FILE"
        ok "Cron do scheduler configurado em ${CRON_FILE}"
    else
        ok "${CRON_FILE} já existe, pulando."
    fi
else
    warn "crontab não encontrado. Adicione manualmente ao crontab:"
    warn "${CRON_LINE}"
fi

# ═══════════════════════════════════════════════════════════════════════════
# ETAPA 14:  Finalização
# ═══════════════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo -e "${BLUE}   INSTALAÇÃO CONCLUÍDA!                 ${NC}"
echo -e "${BLUE}══════════════════════════════════════════${NC}"
echo ""
echo -e "${GREEN}VetEssence foi instalado com sucesso!${NC}"
echo ""
echo -e " ${CYAN}Site:${NC}              ${APP_URL}"
echo -e " ${CYAN}Diretório:${NC}         ${APP_DIR}"
echo -e " ${CYAN}Admin e-mail:${NC}      ${ADMIN_EMAIL}"
echo -e " ${CYAN}Banco:${NC}             ${DB_DATABASE}"
echo ""
echo -e " ${YELLOW}Próximos passos:${NC}"
echo -e " 1. Configure o DNS do domínio ${DOMAIN} para apontar para este servidor."
echo ""
echo -e " 2. Para HTTPS (recomendado), instale o Certbot:"
echo -e "    sudo apt-get install -y certbot python3-certbot-nginx"
echo -e "    sudo certbot --nginx -d ${DOMAIN}"
echo ""
echo -e " 3. Configure os gateways de pagamento em:"
echo -e "    Financeiro → Gateways de Pagamento (inclusive PIX por unidade)"
echo ""
echo -e " 4. Configure as notificações em:"
echo -e "    Configurações → Notificações"
echo ""
echo -e " 5. Para atualizações futuras, use:"
echo -e "    Configurações → Atualizar Sistema"
echo ""
echo -e " ${YELLOW}Senhas importantes:${NC}"
echo -e " - Admin: a senha que você definiu durante a instalação"
if [[ "$INSTALL_LOCAL_DB" == true ]]; then
    echo -e " - MySQL root: a senha definida durante a instalação do MySQL"
fi
echo ""
echo -e "${BLUE}══════════════════════════════════════════${NC}"
