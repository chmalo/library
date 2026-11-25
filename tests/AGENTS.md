# Convenciones de Tests (`tests/`)

## Separación por tipo

- `tests/Unit/`: reservado para probar **casos de uso** (`src/{Modulo}/Application/...`) de forma aislada, con **una clase de test por cada caso de uso** (`tests/Unit/{Modulo}/{CasoDeUso}Test.php`), utilizando mocks o fakes de los puertos definidos en `Domain/` (sin acceso a base de datos ni capa HTTP).
- `tests/Feature/`: pruebas de integración HTTP/API (rutas, Form Requests, códigos de estado, persistencia y generación de Swagger).

## Estilo

- Escribir tests justos, precisos y concisos; evitar suites verbosas o redundantes.
