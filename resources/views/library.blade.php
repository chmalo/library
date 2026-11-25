<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Biblioteca</title>
    <link rel="stylesheet" href="/css/library.css">
    <script type="module" src="/js/library.js"></script>
</head>
<body>
    <div class="page">
        <header class="site-header">
            <div>
                <h1>Gestión de Biblioteca</h1>
                <p class="subtitle">Interfaz web conectada directamente a la API REST de libros y categorías.</p>
            </div>
            <a class="btn btn-secondary" href="/api/documentation" target="_blank" rel="noopener">Swagger UI</a>
        </header>

        <div id="status-banner" class="status-banner" role="status" aria-live="polite" hidden></div>
        <div id="field-announcer" class="visually-hidden" aria-live="polite"></div>

        <main>
            <div class="tabs-shell">
                <div role="tablist" aria-label="Secciones de biblioteca" class="tablist">
                    <button type="button" role="tab" id="tab-books" aria-selected="true" tabindex="0">Libros</button>
                    <button type="button" role="tab" id="tab-categories" aria-selected="false" tabindex="-1">Categorías</button>
                </div>

                <section id="panel-books" role="tabpanel" aria-labelledby="tab-books">
                    <div class="panel-card">
                        <div class="toolbar">
                            <search>
                                <form id="books-filter-form" class="filter-form">
                                    <div class="inline-field">
                                        <label for="filter-category">Categoría</label>
                                        <select id="filter-category" name="category">
                                            <option value="">Todas las categorías</option>
                                        </select>
                                    </div>
                                    <div class="toolbar-actions">
                                        <button type="submit" class="btn btn-secondary">Filtrar</button>
                                        <button type="button" id="clear-filter-btn" class="btn btn-ghost">Limpiar</button>
                                    </div>
                                </form>
                            </search>
                            <button type="button" id="new-book-btn" class="btn btn-primary">Nuevo libro</button>
                        </div>

                        <div tabindex="0" role="region" aria-labelledby="books-caption" class="table-wrapper">
                            <table class="books-table">
                                <caption id="books-caption" class="visually-hidden">Listado de libros</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Título</th>
                                        <th scope="col">Autor</th>
                                        <th scope="col">Año</th>
                                        <th scope="col">Categoría</th>
                                        <th scope="col">Descripción</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="books-tbody">
                                    <tr>
                                        <td colspan="6" class="empty-cell">Cargando libros...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section id="panel-categories" role="tabpanel" aria-labelledby="tab-categories" hidden>
                    <div class="panel-card">
                        <div class="toolbar">
                            <h2>Categorías registradas</h2>
                            <button type="button" id="new-category-btn" class="btn btn-primary">Nueva categoría</button>
                        </div>

                        <div tabindex="0" role="region" aria-labelledby="categories-caption" class="table-wrapper">
                            <table class="categories-table">
                                <caption id="categories-caption" class="visually-hidden">Listado de categorías</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="categories-tbody">
                                    <tr>
                                        <td colspan="2" class="empty-cell">Cargando categorías...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <dialog id="book-dialog" aria-labelledby="book-dialog-title">
        <form id="book-form" class="dialog-form" novalidate>
            <div class="dialog-header">
                <h1 id="book-dialog-title">Nuevo libro</h1>
                <button type="button" class="btn btn-ghost btn-close" data-close-dialog="book-dialog" aria-label="Cerrar ventana">×</button>
            </div>

            <input type="hidden" id="book-id" name="id">

            <div id="book-form-alert" class="form-alert" role="alert" hidden></div>

            <div class="field">
                <label for="book-title">Título <span aria-hidden="true">*</span></label>
                <input type="text" id="book-title" name="title" required minlength="3" maxlength="255" aria-invalid="false" data-error="book-title-error" autofocus>
                <span id="book-title-error" class="error-msg"></span>
            </div>

            <div class="field">
                <label for="book-author">Autor <span aria-hidden="true">*</span></label>
                <input type="text" id="book-author" name="author" required maxlength="255" aria-invalid="false" data-error="book-author-error">
                <span id="book-author-error" class="error-msg"></span>
            </div>

            <div class="field-row">
                <div class="field">
                    <label for="book-published-year">Año de publicación <span aria-hidden="true">*</span></label>
                    <input type="number" id="book-published-year" name="published_year" min="1000" max="{{ date('Y') }}" inputmode="numeric" required aria-invalid="false" data-error="book-published_year-error">
                    <span id="book-published_year-error" class="error-msg"></span>
                </div>

                <div class="field">
                    <label for="book-category-id">Categoría <span aria-hidden="true">*</span></label>
                    <select id="book-category-id" name="category_id" required aria-invalid="false" data-error="book-category_id-error">
                        <option value="">Selecciona una categoría</option>
                    </select>
                    <span id="book-category_id-error" class="error-msg"></span>
                </div>
            </div>

            <div class="field">
                <label for="book-description">Descripción</label>
                <textarea id="book-description" name="description" rows="3" aria-invalid="false" data-error="book-description-error"></textarea>
                <span id="book-description-error" class="error-msg"></span>
            </div>

            <div class="dialog-actions">
                <button type="button" class="btn btn-secondary" data-close-dialog="book-dialog">Cancelar</button>
                <button type="submit" id="book-submit-btn" class="btn btn-primary">Guardar libro</button>
            </div>
        </form>
    </dialog>

    <dialog id="category-dialog" aria-labelledby="category-dialog-title">
        <form id="category-form" class="dialog-form" novalidate>
            <div class="dialog-header">
                <h1 id="category-dialog-title">Nueva categoría</h1>
                <button type="button" class="btn btn-ghost btn-close" data-close-dialog="category-dialog" aria-label="Cerrar ventana">×</button>
            </div>

            <input type="hidden" id="category-id" name="id">

            <div id="category-form-alert" class="form-alert" role="alert" hidden></div>

            <div class="field">
                <label for="category-name">Nombre <span aria-hidden="true">*</span></label>
                <input type="text" id="category-name" name="name" required maxlength="255" aria-invalid="false" data-error="category-name-error" autofocus>
                <span id="category-name-error" class="error-msg"></span>
            </div>

            <div class="dialog-actions">
                <button type="button" class="btn btn-secondary" data-close-dialog="category-dialog">Cancelar</button>
                <button type="submit" id="category-submit-btn" class="btn btn-primary">Guardar categoría</button>
            </div>
        </form>
    </dialog>

    <dialog id="confirm-dialog" aria-labelledby="confirm-dialog-title">
        <div class="dialog-form">
            <h1 id="confirm-dialog-title">Confirmar eliminación</h1>
            <p id="confirm-dialog-message" class="confirm-message"></p>
            <form method="dialog" class="dialog-actions">
                <button type="submit" value="cancel" class="btn btn-secondary" autofocus>Cancelar</button>
                <button type="submit" value="confirm" class="btn btn-danger">Eliminar</button>
            </form>
        </div>
    </dialog>
</body>
</html>
