# Prueba Técnica — Gestión de Biblioteca (Laravel / PHP)

## 1. Descripción del Proyecto

Aplicación funcional para gestionar una biblioteca de libros (CRUD y filtrado por categoría) con Laravel y PHP.

### Funcionalidades requeridas
- Listar los libros disponibles.
- Crear un nuevo libro asociándolo a una categoría.
- Editar la información de un libro existente.
- Eliminar libros registrados.
- Filtrar la lista de libros por categoría.

## 2. Modelo y Base de Datos

Tablas principales y relaciones Eloquent:

### Tabla `books`
- `id`: BigInt / Auto-incremental (PK)
- `title`: String / Requerido
- `author`: String / Requerido
- `description`: Text / Opcional
- `published_year`: Year / Int / Requerido
- `category_id`: BigInt / FK (`categories.id`)

### Tabla `categories`
- `id`: BigInt / Auto-incremental (PK)
- `name`: String / Requerido y Único

### Relaciones Eloquent
- `Category` hasMany `Book`
- `Book` belongsTo `Category`

## 3. Rutas y Controladores

| Método | Ruta | Acción |
| --- | --- | --- |
| `GET` | `/books` | Listar todos los libros almacenados |
| `GET` | `/books?category={category_id}` | Filtrar libros por categoría |
| `POST` | `/books` | Crear un nuevo libro |
| `PUT` | `/books/{id}` | Actualizar un libro existente |
| `DELETE` | `/books/{id}` | Eliminar un libro específico |

## 4. Validaciones

- `title`: Obligatorio, mínimo 3 caracteres.
- `author`: Obligatorio.
- `published_year`: Obligatorio, año válido.
- `category_id`: Obligatorio, debe existir en `categories`.
- `categories.name`: Obligatorio y único al registrar una nueva categoría.

## 5. Lógica de Negocio

- Verificar la existencia del libro antes de eliminarlo.
- Verificar que una categoría no tenga libros asociados antes de permitir su eliminación.

## 6. Criterios de Evaluación

- Arquitectura MVC.
- Uso de Migraciones y Seeders.
- Form Requests para validación.
- Manejo adecuado de ORM Eloquent.
- Tratamiento correcto de respuestas y errores HTTP.
