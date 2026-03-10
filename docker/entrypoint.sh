#!/bin/sh
set -e

if [ -n "${DB_CONNECTION}" ] && [ "${DB_CONNECTION}" = "mysql" ]; then
    echo "Esperando a MySQL..."
    until php -r "
        \$host = getenv('DB_HOST');
        \$port = getenv('DB_PORT') ?: '3306';
        \$db = getenv('DB_DATABASE');
        \$user = getenv('DB_USERNAME');
        \$pass = getenv('DB_PASSWORD');
        try {
            new PDO(\"mysql:host={\$host};port={\$port};dbname={\$db}\", \$user, \$pass);
            exit(0);
        } catch (Throwable \$e) {
            exit(1);
        }
    "; do
        sleep 2
    done
fi

php artisan migrate --force

exec "$@"
