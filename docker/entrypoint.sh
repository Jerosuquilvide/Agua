#!/usr/bin/env sh
set -e

cd /app

log() { printf '%s\n' "[init] $*"; }

# Esperar DB (vía PHP PDO)
db_ready_php() {
  php -r '
    $url = getenv("DATABASE_URL");
    if (!$url) { exit(1); }
    try { new PDO($url); exit(0); } catch (Throwable $e) { exit(1); }
  ' >/dev/null 2>&1
}

db_ready_pg() {
  command -v pg_isready >/dev/null 2>&1 || return 1
  # Extraemos host/puerto de DATABASE_URL si es posible, si no usamos defaults
  H="${DB_HOST:-db}"
  P="${DB_PORT:-5432}"
  U="${DB_USER:-${POSTGRES_USER:-devuser}}"
  D="${DB_NAME:-${POSTGRES_DB:-devdb}}"
  pg_isready -h "$H" -p "$P" -U "$U" -d "$D" >/dev/null 2>&1
}

log "Esperando DB.."
i=0
while [ "$i" -lt 60 ]; do
  if db_ready_php || db_ready_pg; then
    break
  fi
  i=$((i + 1))
  sleep 1
done

# Composer install si falta vendor/
if [ ! -d "vendor" ] || [ "composer.lock" -nt "vendor" ]; then
  log "Instalando dependencias.."
  composer install --no-interaction --prefer-dist
else
  log "Dependencias ya presentes, skip.."
fi

# --- JWT keys (idempotente) ---
if [ ! -f "config/jwt/private.pem" ] || [ ! -f "config/jwt/public.pem" ]; then
  log "Generando llaves JWT.."
  mkdir -p config/jwt
  php bin/console lexik:jwt:generate-keypair --no-interaction
  chmod 600 config/jwt/private.pem || true
  chmod 644 config/jwt/public.pem || true
else
  log "Llaves JWT existentes, skip.."
fi

# Hacer dump de env productivo
# if [ ! -f ".env.local.php" ]; then
#   log "Dump de env para producción.."
#   composer dump-env prod
# else
#   log "Dump de env existente, skip.."
# fi

# Caché
# log "Warmup de caché.."
# php bin/console cache:warmup --no-interaction || true

# Iniciar servicios
log "Iniciando servicios.."
if [ "$#" -eq 0 ]; then
  set -- frankenphp run --config=/etc/caddy/Caddyfile
fi
exec "$@"
