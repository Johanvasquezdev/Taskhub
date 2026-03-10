 # TaskHub

 TaskHub es una aplicación Laravel + Vue para gestionar tareas y notas en un solo lugar.

 Incluye:

 - Autenticación con Laravel Fortify
 - Un panel de control con resúmenes de tareas y notas
 - Un módulo `Tareas` con prioridad, fecha límite, estado y filtros
 - Un módulo `Notas` para notas rápidas del proyecto y recordatorios
 - Frontend Vue impulsado por Vite

 ## Stack

 - Laravel 12
 - PHP 8.5
 - Vue 3
 - Inertia.js
 - Vite
 - SQLite para desarrollo local

 ## Configuración local

 Clona el proyecto e instala las dependencias:

 ```bash
 composer install
 npm install
 ```

 Copia el archivo de entorno y genera la clave de la aplicación:

 ```bash
 copy .env.example .env
 php artisan key:generate
 ```

 Ejecuta las migraciones:

 ```bash
 php artisan migrate
 ```

 ## Ejecución local

 Inicia el servidor Laravel:

 ```bash
 php artisan serve
 ```

 Inicia Vite en una segunda terminal:

 ```bash
 npm run dev
 ```

 Abre:

 ```text
 http://127.0.0.1:8000
 ```

 ## Ejecución con Docker

 Construye y inicia los contenedores:

 ```bash
 docker compose up --build
 ```

 TaskHub estará disponible en:

 ```text
 http://127.0.0.1:8000
 ```

 La configuración de Docker incluye:

 - Contenedor `app` con Laravel, PHP, Apache y assets de Vite compilados
 - Contenedor `db` con MySQL 8

 Comandos útiles de Docker:

 ```bash
 docker compose up --build
 docker compose down
 docker compose down -v
 ```

 El contenedor de MySQL está expuesto en el puerto `3307` localmente.

 Credenciales de base de datos utilizadas por Docker:

 ```text
 DB_CONNECTION=mysql
 DB_HOST=db
 DB_PORT=3306
 DB_DATABASE=taskhub
 DB_USERNAME=taskhub
 DB_PASSWORD=secret
 ```

 ## Comandos útiles

 Ejecutar pruebas:

 ```bash
 php artisan test
 ```

 Generar rutas/acciones de Wayfinder:

 ```bash
 php artisan wayfinder:generate --with-form
 ```

 Compilar assets del frontend para producción:

 ```bash
 npm run build
 ```

 ## Notas sobre despliegue

 TaskHub puede ser desplegado en plataformas como Railway.

 Pasos típicos de producción:

 ```bash
 composer install --no-dev --optimize-autoloader
 npm install
 npm run build
 php artisan migrate --force
 php artisan optimize
 ```

 Vite se utiliza para compilar los assets del frontend. En producción, Laravel sirve los archivos compilados desde
 `public/build`.

 ## Estructura del proyecto

 - `app/Http/Controllers` - controladores de Laravel
 - `app/Models` - modelos Eloquent
 - `database/migrations` - cambios en el esquema de base de datos
 - `resources/js/pages` - páginas Inertia/Vue
 - `resources/js/components` - componentes UI reutilizables
 - `routes/web.php` - rutas de la aplicación

 ## Módulos actuales

 ### Tareas

 - Crear tareas
 - Marcar como completadas
 - Eliminar tareas
 - Prioridad: baja, media, alta
 - Fecha límite
 - Filtros por estado

 ### Notas

 - Crear notas
 - Eliminar notas
 - Ver resumen desde el panel de control
