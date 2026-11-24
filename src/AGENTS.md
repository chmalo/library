# Arquitectura Hexagonal (`src/`)

Esta carpeta contiene el núcleo de la aplicación bajo el namespace PSR-4 `Src\` (`src/`).

## Organización por módulos

Cada módulo de negocio vive en su propio directorio en PascalCase (por ejemplo `src/Books/`, `src/Categories/`) y se divide obligatoriamente en 3 capas:

- `Domain/`: entidades, value objects, interfaces de repositorio (puertos) y excepciones de dominio. Debe ser PHP puro, sin dependencias de Laravel, Eloquent ni HTTP.
- `Application/`: casos de uso y DTOs. Orquesta la lógica de aplicación apoyándose únicamente en los contratos definidos en `Domain/`.
- `Infrastructure/`: implementaciones concretas de los repositorios (Eloquent/PostgreSQL) y adaptadores hacia el framework o servicios externos.

## Regla de dependencias

`Infrastructure` → `Application` → `Domain`

Las dependencias apuntan siempre hacia adentro; `Domain` no debe importar ni conocer nada de `Application`, `Infrastructure` o `App\`.
