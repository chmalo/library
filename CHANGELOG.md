# Changelog — Library API

Trazabilidad completa de las decisiones, cambios, commits y Pull Requests realizados en el proyecto.

## Estado actual

- **Stack:** Laravel 13, PHP 8.4 (Docker + ejecución nativa), PostgreSQL 16, Swagger OpenAPI 3.0 (`darkaonline/l5-swagger` `11.4.1` / `zircote/swagger-php` `7.9.0`).
- **Arquitectura:** Hexagonal por módulos (`Categories`, `Books`) bajo `Src\` (`src/`) con capas `Domain/`, `Application/` e `Infrastructure/`.
- **Calidad verificada:** `44 passed (202 assertions)` en PHPUnit (10 clases de Unit tests aisladas + Feature tests en SQLite `:memory:`), 5 paths y 6 esquemas OpenAPI activos en `/api/documentation` y `/docs`.

---

## [2026-11-25]

### Paso 13 — Rediseño Editorial Contemporáneo del Frontend (`GET /`)
- **PR:** [#13](https://github.com/chmalo/library/pull/13) (`OPEN`, rama `feat/modernize-frontend-ui`)
- **Cambios:**
  - Rediseño visual completo de `resources/views/library.blade.php` y `public/css/library.css` bajo dirección **Editorial Contemporáneo** (superficies piedra/folio, acento verde biblioteca, tipografía `Onest` y cero pasos de build), con soporte nativo `color-scheme: light dark`, `light-dark()` y fallbacks progresivos.
  - Barra superior pegajosa con navegación segmentada, contadores `tabular-nums`, selector de vista **Tarjetas / Tabla** persistido en `localStorage`, búsqueda instantánea en vivo (`#books-search`), riel de chips por categoría conectado a `GET /api/books?category=`, esqueletos de carga, iconos SVG consistentes y columna de conteo + acceso rápido `"Ver libros"` en Categorías.
  - Toggle claro/oscuro de dos estados sin FOUC (`dark-mode-toggle`), modales `<dialog closedby="any">` con animaciones de entrada/salida `@starting-style` + `transition-behavior: allow-discrete` y fallbacks para navegadores sin `closedBy` ni `overlay` (`public/js/library.js`).
  - Ampliación de `tests/Feature/LibraryFrontendTest.php` para verificar meta `color-scheme`, `closedby="any"`, toggle de tema, selector de vista y búsqueda.

### Paso 12 — Interfaz Web ligera (`GET /`) conectada a la API REST
- **PR:** [#12](https://github.com/chmalo/library/pull/12) (`MERGED`, merge commit `24df6f9`)
- **Commit:** `2697e9b`
- **Cambios:**
  - Vista Blade (`resources/views/library.blade.php`), estilos CSS propios (`public/css/library.css`) y cliente ES module con `fetch` (`public/js/library.js`) sirviendo el CRUD completo de libros y categorías desde `GET /` sin dependencias JS adicionales ni build step.
  - Manejo accesible de modales nativos `<dialog>`, pestañas con navegación por teclado, tablas responsivas con container queries y errores de validación `422` / conflicto `409` en pantalla.
  - Test de integración en `tests/Feature/LibraryFrontendTest.php` y enlace documentado en `README.md`.

### Paso 11 — Aislamiento de base de testing, pulido de filtro y documentación final
- **PR:** [#11](https://github.com/chmalo/library/pull/11) (`MERGED`, merge commit `ae83ffd`)
- **Commit:** `c829a10`
- **Cambios:**
  - Aislamiento de PHPUnit en Docker mediante `<server name="DB_CONNECTION" value="sqlite" force="true"/>` y `<server name="DB_DATABASE" value=":memory:" force="true"/>` en `phpunit.xml`, preservando los datos seeded en PostgreSQL.
  - Refinamiento de cláusula de guarda en `BooksGetController` (`$categoryId === null || $categoryId === ''`) para manejar `?category=0` explícitamente, validado en `tests/Feature/BookApiTest.php`.
  - Documentación del prefijo estándar `/api` y del comando `db:seed` en `README.md`.
  - Eliminación de los tests boilerplate `ExampleTest.php` y archivo local sin trackear `package-lock.json`.

### Paso 7 — Trazabilidad inicial (`CHANGELOG.md`)
- **PR:** [#7](https://github.com/chmalo/library/pull/7) (`MERGED`, merge commit `dd93c38`)
- **Commits:** `e6e308b`, `57732b2`, `401cae3`
- **Cambios:**
  - Creación y actualización de `CHANGELOG.md` con trazabilidad cronológica completa de commits y PRs (#1–#10).

### Paso 10 — Casos de uso `Books`, tests unitarios por clase y controladores cableados
- **PR:** [#10](https://github.com/chmalo/library/pull/10) (`MERGED`, merge commit `c17cf15`)
- **Commits:** `c6c9594`, `75628a0`
- **Cambios:**
  - Capa `src/Books/Application/` en PHP puro: `BooksSearcher`, `BookFinder`, `BookCreator` (+ `CreateBookDto`), `BookUpdater` (+ `UpdateBookDto`) y `BookDeleter`.
  - Contrato estricto de dominio en `app/Http/Resources/BookResource.php`, restricción numérica `whereNumber('book')` en `routes/api.php` y cableado de los 5 controladores en `app/Http/Controllers/Api/Books/` con `try/catch` explícito y cláusulas de guarda con `empty` en `BooksGetController`.
  - 5 clases de tests unitarios aislados en `tests/Unit/Books/` (`BooksSearcherTest`, `BookFinderTest`, `BookCreatorTest`, `BookUpdaterTest`, `BookDeleterTest`).

### Paso 9 — Casos de uso `Categories`, tests unitarios por clase, Swagger a nivel de clase y `try/catch` explícito
- **PR:** [#9](https://github.com/chmalo/library/pull/9) (`MERGED`)
- **Commits:** `1fd8aeb`, `59c65ae`, `d2a455e`, `81475bb`
- **Cambios:**
  - Capa `src/Categories/Application/` en PHP puro: `CategoriesSearcher`, `CategoryFinder`, `CategoryCreator` (+ `CreateCategoryDto`), `CategoryUpdater` (+ `UpdateCategoryDto`) y `CategoryDeleter`.
  - Atributos OpenAPI `#[OA\...]` movidos arriba de la declaración de cada clase en los 11 controladores y regla documentada en `app/Http/Controllers/AGENTS.md`.
  - Contrato estricto de dominio en `app/Http/Resources/CategoryResource.php`, restricción numérica `whereNumber('category')` en `routes/api.php` y manejo explícito de excepciones con `try/catch` en los controladores (sin handlers de dominio en `bootstrap/app.php`).
  - 5 clases de tests unitarios aislados en `tests/Unit/Categories/` (`CategoriesSearcherTest`, `CategoryFinderTest`, `CategoryCreatorTest`, `CategoryUpdaterTest`, `CategoryDeleterTest`) y convención de 1 clase por caso de uso en `tests/AGENTS.md`.

### Paso 8 — Capas `Domain` e `Infrastructure` para `Categories` y `Books` + DI bindings
- **PR:** [#8](https://github.com/chmalo/library/pull/8) (`MERGED`)
- **Commit:** `7885ad7`
- **Cambios:**
  - Entidades puras (`Src\Categories\Domain\Category`, `Src\Books\Domain\Book`), puertos (`CategoryRepository`, `BookRepository`) y excepciones de dominio (`CategoryNotFound`, `CategoryAlreadyExists`, `CategoryHasBooks`, `BookNotFound`).
  - Adaptadores Eloquent (`EloquentCategoryRepository`, `EloquentBookRepository`), bindings DI en `app/Providers/AppServiceProvider.php`, convenciones en `src/AGENTS.md` y tests de integración en `tests/Feature/EloquentRepositoriesTest.php`.

## [2026-11-24]

### Paso 6 — Controladores Single-Action, PostgreSQL local, `src/` PSR-4 y guías `AGENTS.md`
- **PR:** [#6](https://github.com/chmalo/library/pull/6) (`MERGED`)
- **Commits:** `70de642`, `dfd7091`, `48daa2f`
- **Cambios:**
  - Refactorización de `CategoryController` y `BookController` en 11 controladores invocables (`__invoke`) siguiendo la convención `{Recurso}{MetodoHttp}Controller`:
    - Health: `app/Http/Controllers/Api/HealthGetController.php`
    - Categorías: `CategoriesGetController`, `CategoryGetController`, `CategoryPostController`, `CategoryPutController`, `CategoryDeleteController` (`app/Http/Controllers/Api/Categories/`)
    - Libros: `BooksGetController`, `BookGetController`, `BookPostController`, `BookPutController`, `BookDeleteController` (`app/Http/Controllers/Api/Books/`)
  - Cada controlador conserva su propia documentación OpenAPI (`#[OA\...]`) y se actualizaron las rutas en `routes/api.php`.
  - Configuración de la instalación nativa/local para usar PostgreSQL por defecto (`.env.example`, `README.md`).
  - Registro del namespace PSR-4 `"Src\\": "src/"` en `composer.json` para arquitectura hexagonal.
  - Creación de guías `AGENTS.md` específicas por alcance:
    - `app/Http/Controllers/AGENTS.md`: responsabilidad única, convención de nombres `{Recurso}{MetodoHttp}Controller`, Swagger obligatorio y controladores delgados.
    - `src/AGENTS.md`: arquitectura hexagonal por módulos (`Domain/`, `Application/`, `Infrastructure/`) y regla de dependencias hacia adentro.
    - `tests/AGENTS.md`: `tests/Unit/` exclusivo para casos de uso aislados y `tests/Feature/` para integración HTTP/API.

### Paso 5 — Documentación Swagger OpenAPI completa
- **PR:** [#5](https://github.com/chmalo/library/pull/5) (`MERGED`)
- **Commit:** `8907d5a`
- **Cambios:**
  - Documentación OpenAPI 3.0 con atributos PHP 8 en todos los endpoints de Categorías y Libros (`200`, `201`, `204`, `404`, `409`, `422`).
  - Tags y esquemas reutilizables en `app/Http/Controllers/Controller.php` (`ErrorMessage`, `ValidationError`), `app/Http/Resources/CategoryResource.php` (`Category`, `CategoryInput`) y `app/Http/Resources/BookResource.php` (`Book`, `BookInput`).
  - Generación automática de la especificación (`php artisan l5-swagger:generate`) al iniciar el contenedor en `docker/entrypoint.sh`.
  - Validación de los 5 paths OpenAPI generados en `tests/Feature/ApiBaseTest.php`.

### Paso 4 — API REST de Libros (`/api/books`)
- **PR:** [#4](https://github.com/chmalo/library/pull/4) (`MERGED`)
- **Commit:** `3add8fe`
- **Cambios:**
  - Endpoints `GET /api/books` (con filtro `?category={category_id}` protegido con `ctype_digit` para PostgreSQL y eager loading `with('category')`), `GET /api/books/{book}`, `POST /api/books`, `PUT /api/books/{book}` y `DELETE /api/books/{book}`.
  - Validación con `StoreBookRequest` y `UpdateBookRequest` (`title` mín. 3, `author`, `description` nullable, `published_year` entre `1000` y el año actual, `category_id` existente en `categories`).
  - `BookResource` con categoría embebida y 6 tests Feature en `tests/Feature/BookApiTest.php`.

### Paso 3 — API REST de Categorías (`/api/categories`)
- **PR:** [#3](https://github.com/chmalo/library/pull/3) (`MERGED`)
- **Commit:** `d943b02`
- **Cambios:**
  - Endpoints CRUD bajo `/api/categories` con `StoreCategoryRequest`, `UpdateCategoryRequest` (unicidad de `name`) y `CategoryResource`.
  - Regla de negocio: bloqueo de eliminación (`409 Conflict`) cuando la categoría tiene libros asociados.
  - 6 tests Feature en `tests/Feature/CategoryApiTest.php`.

### Paso 2 — Esquema de BD, Modelos Eloquent, Factories, Seeders y README Swagger
- **PR:** [#2](https://github.com/chmalo/library/pull/2) (`MERGED`)
- **Commit:** `93b9e56`
- **Cambios:**
  - Migraciones `categories` y `books` (`published_year` como `smallInteger` y FK `category_id` con `restrictOnDelete()`).
  - Modelos `App\Models\Category` (`hasMany`) y `App\Models\Book` (`belongsTo`).
  - Factories (`CategoryFactory`, `BookFactory`) y seeders idempotentes (`CategorySeeder`, `BookSeeder`: 5 categorías y 10 libros).
  - Sección `Documentación de la API (Swagger)` en `README.md` y 3 tests en `tests/Feature/DatabaseSchemaAndModelsTest.php`.

### Paso 1 — Base API REST y PHP Swagger
- **PR:** [#1](https://github.com/chmalo/library/pull/1) (`MERGED`)
- **Commit:** `f42e41e`
- **Cambios:**
  - Habilitación de `routes/api.php` bajo el prefijo `/api` en `bootstrap/app.php` con renderizado de errores en JSON.
  - Instalación y configuración de `darkaonline/l5-swagger` (`config/l5-swagger.php`, `/storage/api-docs` en `.gitignore`).
  - Endpoint base `GET /api/health` y 3 tests Feature en `tests/Feature/ApiBaseTest.php`.

### Base inicial del repositorio
- **Commits:** `78aa215`, `67fb298`, `25a8b83`, `93c9586`
- **Cambios:**
  - Scaffold base de Laravel 13, configuración Docker aislada (`PHP 8.4` + `PostgreSQL 16` en `compose.yml`), `PRUEBA_TECNICA.md` y guía de instalación en `README.md`.

---

## Estado de entrega

- Todos los requisitos funcionales, validaciones, reglas de negocio y criterios de evaluación de `PRUEBA_TECNICA.md` están implementados, documentados en Swagger y cubiertos por tests Unit + Feature aislados.
