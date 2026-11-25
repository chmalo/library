# Reglas para Controladores HTTP

## Responsabilidad única (Single Action Controllers)

- Cada endpoint de la API debe tener su propio controlador invocable (`__invoke`), agrupado por módulo en `app/Http/Controllers/Api/{Modulo}/` (por ejemplo, `Api/Books/` y `Api/Categories/`).
- Prohibido agrupar múltiples acciones CRUD (`index`, `store`, `show`, `update`, `destroy`) en un mismo controlador.

## Convención de nombres

- Usar `{Recurso}{MetodoHttp}Controller`:
  - Plural para colecciones (`BooksGetController`, `CategoriesGetController`).
  - Singular para recurso individual (`BookGetController`, `BookPostController`, `BookPutController`, `BookDeleteController`, `HealthGetController`).

## Documentación OpenAPI obligatoria

- Cada controlador debe incluir la documentación Swagger de su endpoint mediante atributos PHP 8 (`OpenApi\Attributes as OA`) arriba de la declaración de la clase (`class ... extends Controller`) con ruta, resumen, tag, parámetros, request body y respuestas HTTP.
- Después de agregar o modificar atributos OpenAPI, regenerar la especificación con `php artisan l5-swagger:generate`.

## Controladores delgados

- Validar la entrada con Form Requests (`app/Http/Requests/`).
- Formatear la salida con API Resources (`app/Http/Resources/`).
- Mantener los controladores sin lógica de negocio compleja; delegar a los casos de uso en `src/` cuando existan.
- Capturar excepciones de dominio explícitamente con `try/catch` en cada controlador para traducirlas a la respuesta HTTP adecuada (`404`, `409`, etc.), sin registrar excepciones de dominio en `bootstrap/app.php`.
