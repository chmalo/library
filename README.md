# Library — Gestión de Biblioteca

Base del proyecto en Laravel 13 (`PHP 8.4` / `>= 8.3`). Contexto y requisitos de la prueba en [PRUEBA_TECNICA.md](PRUEBA_TECNICA.md).

## Opción 1: Instalación con Docker (entorno aislado)

### Requisitos
- Docker
- Docker Compose

### Pasos
1. Clonar el repositorio y entrar al directorio:
   ```bash
   git clone <url-del-repositorio> library
   cd library
   ```
2. Construir e iniciar los contenedores (`app` + `db` PostgreSQL 16):
   ```bash
   docker compose up -d --build
   ```
   El entrypoint crea `.env`, instala dependencias si faltan, genera `APP_KEY` y ejecuta las migraciones automáticamente.
3. Verificar el estado:
   ```bash
   docker compose ps
   curl -i http://localhost:8000/up
   ```
   La aplicación queda disponible en `http://localhost:8000`.

### Comandos útiles (Docker)
- Ejecutar tests: `docker compose exec app php artisan test`
- Ejecutar migraciones: `docker compose exec app php artisan migrate`
- Ejecutar seeders: `docker compose exec app php artisan db:seed`
- Ver logs: `docker compose logs -f app`
- Detener entorno: `docker compose down`
- Puertos configurables por entorno: `APP_PORT` (default `8000`) y `DOCKER_DB_PORT` (default `5435`).

## Documentación de la API (Swagger)

- **Swagger UI:** `http://localhost:8000/api/documentation`
- **OpenAPI JSON:** `http://localhost:8000/docs`
- **Regenerar especificación:**
  - Docker: `docker compose exec app php artisan l5-swagger:generate`
  - Nativa: `php artisan l5-swagger:generate`

## Opción 2: Instalación nativa (local sin Docker)

### Requisitos
- PHP `>= 8.3` con extensiones: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_pgsql`, `pgsql`, `pdo_sqlite`, `session`, `tokenizer`, `xml`
- PostgreSQL `16` (o `>= 15`)
- Composer `2.x`

### Pasos
1. Instalar dependencias PHP:
   ```bash
   composer install
   ```
2. Crear archivo de entorno, configurar PostgreSQL y generar clave de aplicación:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Asegurar que la base de datos `library` exista en PostgreSQL y que `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` en `.env` coincidan con tu instancia local.
3. Ejecutar migraciones, seeders y generar la documentación Swagger:
   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan l5-swagger:generate
   ```
4. Iniciar el servidor local:
   ```bash
   php artisan serve
   ```
   Disponible en `http://127.0.0.1:8000`.

### Verificación (nativa)
- Salud de la aplicación: `curl -i http://127.0.0.1:8000/up`
- Suite de tests: `php artisan test`
