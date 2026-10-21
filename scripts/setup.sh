#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
mkdir -p .runtime assets artifacts
if [[ ! -f .env ]]; then
  umask 077
  {
    printf 'DB_PASSWORD=%s\n' "$(openssl rand -hex 24)"
    printf 'DB_ROOT_PASSWORD=%s\n' "$(openssl rand -hex 24)"
    printf 'ADMIN_PASSWORD=%s\n' "$(openssl rand -hex 20)"
  } > .env
fi
if [[ ! -f .runtime/wp-cli.phar ]]; then
  curl --fail --silent --show-error --location https://raw.githubusercontent.com/wp-cli/builds/e1b93919b8da39cbc3707f44bd2c61df3bd5b0d6/phar/wp-cli.phar -o .runtime/wp-cli.phar
fi
printf '%s  %s\n' 'ce34ddd838f7351d6759068d09793f26755463b4a4610a5a5c0a97b68220d85c' '.runtime/wp-cli.phar' | sha256sum --check --status
docker compose up -d
for attempt in $(seq 1 60); do
  if docker compose exec -T wordpress test -f /var/www/html/wp-config.php; then break; fi
  sleep 2
done
docker compose exec -T wordpress php /opt/nexo/install.php
docker compose exec -T wordpress php /opt/nexo-runtime/wp-cli.phar --allow-root plugin install woocommerce --version=11.2.1 --activate
docker compose exec -T wordpress php /opt/nexo-runtime/wp-cli.phar --allow-root plugin activate nexo-commerce
docker compose exec -T wordpress php /opt/nexo-runtime/wp-cli.phar --allow-root theme activate nexo
docker compose exec -T wordpress php /opt/nexo-runtime/wp-cli.phar --allow-root eval-file /opt/nexo/seed.php
docker compose exec -T wordpress php /opt/nexo-runtime/wp-cli.phar --allow-root language core install pt_BR --activate
docker compose exec -T wordpress php /opt/nexo-runtime/wp-cli.phar --allow-root language plugin install woocommerce pt_BR
docker compose exec -T wordpress php /opt/nexo-runtime/wp-cli.phar --allow-root rewrite structure '/%postname%/' --hard
docker compose exec -T wordpress chown -R www-data:www-data /var/www/html/wp-content/uploads /var/www/html/wp-content/languages
printf '\nLoja disponível em http://localhost:8092\nAdministrador: eduardo (senha no arquivo .env local, não versionado)\n'
