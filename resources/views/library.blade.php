<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <script>try{document.querySelector('meta[name="color-scheme"]').content=localStorage.getItem('color-scheme')??'light dark';}catch{}</script>
    <title>Biblioteca — Catálogo editorial</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/library.css">
    <script type="module" src="/js/library.js"></script>
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand">
                <span class="brand-mark" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 4.5A2.5 2.5 0 0 1 4.5 2H11v18H4.5A2.5 2.5 0 0 0 2 22V4.5Z"/>
                        <path d="M22 4.5A2.5 2.5 0 0 0 19.5 2H13v18h6.5a2.5 2.5 0 0 1 2.5 2V4.5Z"/>
                    </svg>
                </span>
                <h1>Biblioteca</h1>
            </div>

            <div role="tablist" aria-label="Secciones de biblioteca" class="nav-tabs">
                <button type="button" role="tab" id="tab-books" aria-selected="true" tabindex="0" aria-controls="panel-books">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                    </svg>
                    <span>Libros</span>
                    <span id="books-tab-count" class="count-pill">0</span>
                </button>
                <button type="button" role="tab" id="tab-categories" aria-selected="false" tabindex="-1" aria-controls="panel-categories">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42Z"/>
                        <circle cx="7.5" cy="7.5" r="1"/>
                    </svg>
                    <span>Categorías</span>
                    <span id="categories-tab-count" class="count-pill">0</span>
                </button>
            </div>

            <div class="topbar-actions">
                <button type="button" id="theme-toggle-btn" class="btn btn-icon btn-ghost theme-toggle">
                    <span class="theme-option theme-to-dark">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                        </svg>
                        <span class="visually-hidden">Cambiar a tema oscuro</span>
                    </span>
                    <span class="theme-option theme-to-light">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"/>
                            <path d="M12 2v2"/>
                            <path d="M12 20v2"/>
                            <path d="m4.93 4.93 1.41 1.41"/>
                            <path d="m17.66 17.66 1.41 1.41"/>
                            <path d="M2 12h2"/>
                            <path d="M20 12h2"/>
                            <path d="m6.34 17.66-1.41 1.41"/>
                            <path d="m19.07 4.93-1.41 1.41"/>
                        </svg>
                        <span class="visually-hidden">Cambiar a tema claro</span>
                    </span>
                </button>

                <a class="btn btn-secondary btn-sm" href="/api/documentation" target="_blank" rel="noopener">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h6v6"/>
                        <path d="M10 14 21 3"/>
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    </svg>
                    <span>Swagger UI</span>
                </a>
            </div>
        </div>
    </header>

    <div class="page">
        <div id="status-banner" class="status-banner" role="status" aria-live="polite" hidden>
            <div class="status-content">
                <span id="status-banner-icon" class="status-icon" aria-hidden="true"></span>
                <span id="status-banner-text"></span>
            </div>
            <button type="button" id="status-dismiss-btn" class="btn-dismiss" aria-label="Cerrar aviso">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <div id="field-announcer" class="visually-hidden" aria-live="polite"></div>

        <main>
            <div class="workspace-head">
                <p id="catalog-summary" class="catalog-summary">Cargando catálogo...</p>
            </div>

            <section id="panel-books" role="tabpanel" aria-labelledby="tab-books">
                <div class="controls-bar">
                    <search class="search-wrap">
                        <svg class="search-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <label for="books-search" class="visually-hidden">Buscar libros por título, autor o año</label>
                        <input type="search" id="books-search" class="search-input" placeholder="Buscar por título, autor o año..." autocomplete="off">
                    </search>

                    <div class="controls-right">
                        <div class="view-switcher" role="group" aria-label="Modo de vista">
                            <button type="button" id="view-grid-btn" class="view-btn" data-view="grid" aria-pressed="true">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect width="7" height="7" x="3" y="3" rx="1"/>
                                    <rect width="7" height="7" x="14" y="3" rx="1"/>
                                    <rect width="7" height="7" x="14" y="14" rx="1"/>
                                    <rect width="7" height="7" x="3" y="14" rx="1"/>
                                </svg>
                                <span>Tarjetas</span>
                            </button>
                            <button type="button" id="view-table-btn" class="view-btn" data-view="table" aria-pressed="false">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M3 6h18"/>
                                    <path d="M3 12h18"/>
                                    <path d="M3 18h18"/>
                                </svg>
                                <span>Tabla</span>
                            </button>
                        </div>

                        <button type="button" id="new-book-btn" class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14"/>
                                <path d="M12 5v14"/>
                            </svg>
                            <span>Nuevo libro</span>
                        </button>
                    </div>
                </div>

                <div id="category-chips" class="chip-rail" role="group" aria-label="Filtrar libros por categoría"></div>

                <div id="books-grid" class="books-grid" aria-live="polite">
                    <div class="skeleton-card" aria-hidden="true">
                        <div class="skeleton-row"><span class="skeleton-pill"></span><span class="skeleton-year"></span></div>
                        <div class="skeleton-line skeleton-title"></div>
                        <div class="skeleton-line skeleton-author"></div>
                        <div class="skeleton-line skeleton-body"></div>
                        <div class="skeleton-line skeleton-body short"></div>
                    </div>
                    <div class="skeleton-card" aria-hidden="true">
                        <div class="skeleton-row"><span class="skeleton-pill"></span><span class="skeleton-year"></span></div>
                        <div class="skeleton-line skeleton-title"></div>
                        <div class="skeleton-line skeleton-author"></div>
                        <div class="skeleton-line skeleton-body"></div>
                        <div class="skeleton-line skeleton-body short"></div>
                    </div>
                    <div class="skeleton-card" aria-hidden="true">
                        <div class="skeleton-row"><span class="skeleton-pill"></span><span class="skeleton-year"></span></div>
                        <div class="skeleton-line skeleton-title"></div>
                        <div class="skeleton-line skeleton-author"></div>
                        <div class="skeleton-line skeleton-body"></div>
                        <div class="skeleton-line skeleton-body short"></div>
                    </div>
                </div>

                <div id="books-table-wrap" tabindex="0" role="region" aria-labelledby="books-caption" class="table-wrapper" hidden>
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
            </section>

            <section id="panel-categories" role="tabpanel" aria-labelledby="tab-categories" hidden>
                <div class="panel-surface">
                    <div class="panel-toolbar">
                        <h2>Categorías registradas</h2>
                        <button type="button" id="new-category-btn" class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14"/>
                                <path d="M12 5v14"/>
                            </svg>
                            <span>Nueva categoría</span>
                        </button>
                    </div>

                    <div tabindex="0" role="region" aria-labelledby="categories-caption" class="table-wrapper">
                        <table class="categories-table">
                            <caption id="categories-caption" class="visually-hidden">Listado de categorías</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Libros</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="categories-tbody">
                                <tr>
                                    <td colspan="3" class="empty-cell">Cargando categorías...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <dialog id="book-dialog" closedby="any" aria-labelledby="book-dialog-title">
        <form id="book-form" class="dialog-form" novalidate>
            <div class="dialog-header">
                <h1 id="book-dialog-title">Nuevo libro</h1>
                <button type="button" class="btn btn-icon btn-ghost" data-close-dialog="book-dialog" aria-label="Cerrar ventana">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
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

    <dialog id="category-dialog" closedby="any" aria-labelledby="category-dialog-title">
        <form id="category-form" class="dialog-form" novalidate>
            <div class="dialog-header">
                <h1 id="category-dialog-title">Nueva categoría</h1>
                <button type="button" class="btn btn-icon btn-ghost" data-close-dialog="category-dialog" aria-label="Cerrar ventana">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
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

    <dialog id="confirm-dialog" closedby="any" aria-labelledby="confirm-dialog-title">
        <div class="dialog-form">
            <div class="dialog-header">
                <h1 id="confirm-dialog-title">Confirmar eliminación</h1>
                <button type="button" class="btn btn-icon btn-ghost" data-close-dialog="confirm-dialog" aria-label="Cerrar confirmación">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>
            <p id="confirm-dialog-message" class="confirm-message"></p>
            <form method="dialog" id="confirm-form" class="dialog-actions">
                <button type="submit" value="cancel" class="btn btn-secondary" autofocus>Cancelar</button>
                <button type="submit" value="confirm" class="btn btn-danger">Eliminar</button>
            </form>
        </div>
    </dialog>
</body>
</html>
