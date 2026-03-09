# TaskHub

TaskHub is a Laravel + Vue application for managing tareas and notas in one place.

It includes:

- Authentication with Laravel Fortify
- A dashboard with task and note summaries
- A `Tareas` module with priority, due date, status, and filters
- A `Notas` module for quick project notes and reminders
- Vue frontend powered by Vite

## Stack

- Laravel 12
- PHP 8.5
- Vue 3
- Inertia.js
- Vite
- SQLite for local development

## Local setup

Clone the project and install dependencies:

```bash
composer install
npm install
```

Copy the environment file and generate the app key:

```bash
copy .env.example .env
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate
```

## Run locally

Start the Laravel server:

```bash
php artisan serve
```

Start Vite in a second terminal:

```bash
npm run dev
```

Open:

```text
http://127.0.0.1:8000
```

## Useful commands

Run tests:

```bash
php artisan test
```

Generate Wayfinder routes/actions:

```bash
php artisan wayfinder:generate --with-form
```

Build frontend assets for production:

```bash
npm run build
```

## Deployment notes

TaskHub can be deployed on platforms like Railway.

Typical production steps:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan optimize
```

Vite is used to build the frontend assets. In production, Laravel serves the built files from `public/build`.

## Project structure

- `app/Http/Controllers` - Laravel controllers
- `app/Models` - Eloquent models
- `database/migrations` - database schema changes
- `resources/js/pages` - Inertia/Vue pages
- `resources/js/components` - reusable UI components
- `routes/web.php` - application routes

## Current modules

### Tareas

- Crear tareas
- Marcar como completadas
- Eliminar tareas
- Prioridad: baja, media, alta
- Fecha limite
- Filtros por estado

### Notas

- Crear notas
- Eliminar notas
- Ver resumen desde el dashboard

## Notes

- `.env` is not committed
- `vendor`, `node_modules`, and build output are ignored
- Generated Wayfinder files are regenerated locally with Artisan commands
