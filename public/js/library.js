const SVG_NS = 'http://www.w3.org/2000/svg';

function readStorage(key) {
    try {
        return localStorage.getItem(key);
    } catch {
        return null;
    }
}

function writeStorage(key, value) {
    try {
        if (value === null) {
            localStorage.removeItem(key);
        } else {
            localStorage.setItem(key, value);
        }
    } catch {
    }
}

const initialView = readStorage('library-view');

const state = {
    categories: [],
    books: [],
    allBooksCount: 0,
    bookCountsByCategory: new Map(),
    activeCategoryFilter: '',
    searchQuery: '',
    viewMode: initialView === 'table' ? 'table' : 'grid',
    pendingDelete: null,
    loadingBooks: true,
    loadingCategories: true,
};

const colorSchemeMeta = document.querySelector('meta[name="color-scheme"]');
const themeToggleBtn = document.getElementById('theme-toggle-btn');

const statusBanner = document.getElementById('status-banner');
const statusBannerIcon = document.getElementById('status-banner-icon');
const statusBannerText = document.getElementById('status-banner-text');
const statusDismissBtn = document.getElementById('status-dismiss-btn');
const fieldAnnouncer = document.getElementById('field-announcer');

const tabs = [
    document.getElementById('tab-books'),
    document.getElementById('tab-categories'),
];
const panels = new Map([
    ['tab-books', document.getElementById('panel-books')],
    ['tab-categories', document.getElementById('panel-categories')],
]);

const booksTabCount = document.getElementById('books-tab-count');
const categoriesTabCount = document.getElementById('categories-tab-count');
const catalogSummary = document.getElementById('catalog-summary');

const booksSearchInput = document.getElementById('books-search');
const viewGridBtn = document.getElementById('view-grid-btn');
const viewTableBtn = document.getElementById('view-table-btn');
const categoryChipsEl = document.getElementById('category-chips');
const booksGridEl = document.getElementById('books-grid');
const booksTableWrap = document.getElementById('books-table-wrap');
const booksTbody = document.getElementById('books-tbody');
const categoriesTbody = document.getElementById('categories-tbody');

const bookDialog = document.getElementById('book-dialog');
const bookForm = document.getElementById('book-form');
const bookDialogTitle = document.getElementById('book-dialog-title');
const bookFormAlert = document.getElementById('book-form-alert');
const bookSubmitBtn = document.getElementById('book-submit-btn');
const bookIdInput = document.getElementById('book-id');
const bookTitleInput = document.getElementById('book-title');
const bookAuthorInput = document.getElementById('book-author');
const bookYearInput = document.getElementById('book-published-year');
const bookCategorySelect = document.getElementById('book-category-id');
const bookDescriptionInput = document.getElementById('book-description');

const categoryDialog = document.getElementById('category-dialog');
const categoryForm = document.getElementById('category-form');
const categoryDialogTitle = document.getElementById('category-dialog-title');
const categoryFormAlert = document.getElementById('category-form-alert');
const categorySubmitBtn = document.getElementById('category-submit-btn');
const categoryIdInput = document.getElementById('category-id');
const categoryNameInput = document.getElementById('category-name');

const confirmDialog = document.getElementById('confirm-dialog');
const confirmForm = document.getElementById('confirm-form');
const confirmDialogMessage = document.getElementById('confirm-dialog-message');

let announceTimer = null;
let supportsDisplayTransition;

function createSvgIcon(paths, size = 15) {
    const svg = document.createElementNS(SVG_NS, 'svg');
    svg.setAttribute('width', String(size));
    svg.setAttribute('height', String(size));
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('stroke-width', '1.8');
    svg.setAttribute('stroke-linecap', 'round');
    svg.setAttribute('stroke-linejoin', 'round');
    svg.setAttribute('aria-hidden', 'true');

    for (const d of paths) {
        const path = document.createElementNS(SVG_NS, 'path');
        path.setAttribute('d', d);
        svg.append(path);
    }

    return svg;
}

const ICONS = {
    edit: ['M12 20h9', 'M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z'],
    trash: ['M3 6h18', 'M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6', 'M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2'],
    checkCircle: ['M22 11.08V12a10 10 0 1 1-5.93-9.14', 'm9 11 3 3L22 4'],
    alertCircle: ['M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z', 'M12 8v4', 'M12 16h.01'],
    bookOpen: ['M2 4.5A2.5 2.5 0 0 1 4.5 2H11v18H4.5A2.5 2.5 0 0 0 2 22V4.5Z', 'M22 4.5A2.5 2.5 0 0 0 19.5 2H13v18h6.5a2.5 2.5 0 0 1 2.5 2V4.5Z'],
    filter: ['M3 6h18', 'M7 12h10', 'M10 18h4'],
};

function getSystemScheme() {
    return window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function getStoredScheme() {
    const value = readStorage('color-scheme');
    return value === 'light' || value === 'dark' ? value : null;
}

function syncColorSchemeMeta() {
    if (!colorSchemeMeta) {
        return;
    }

    colorSchemeMeta.content = getStoredScheme() ?? 'light dark';
}

function canTransitionDisplay() {
    if (supportsDisplayTransition !== undefined) {
        return supportsDisplayTransition;
    }

    if (!window.CSS?.supports?.('transition-behavior', 'allow-discrete') || !document.body) {
        return false;
    }

    const probe = document.createElement('div');
    probe.style.cssText = 'transition: display 1s allow-discrete !important; display: block;';
    document.body.appendChild(probe);
    getComputedStyle(probe).display;
    probe.style.display = 'none';
    supportsDisplayTransition = getComputedStyle(probe).display === 'block';
    probe.remove();

    return supportsDisplayTransition;
}

function supportsTopLayerExit() {
    return window.CSS?.supports?.('overlay', 'auto') && canTransitionDisplay();
}

async function closeTopLayer(element, returnValue = undefined) {
    if (!element?.open) {
        return;
    }

    if (!supportsTopLayerExit()) {
        element.setAttribute('data-closing', '');
        const animations = element.getAnimations({ subtree: true });
        if (animations.length > 0) {
            await Promise.race([
                Promise.allSettled(animations.map((animation) => animation.finished)),
                new Promise((resolve) => setTimeout(resolve, 2000)),
            ]);
        }
        if (!element.hasAttribute('data-closing')) {
            return;
        }
        element.removeAttribute('data-closing');
    }

    element.close(returnValue);
}

async function apiRequest(url, options = {}) {
    const headers = {
        Accept: 'application/json',
        ...(options.body ? { 'Content-Type': 'application/json' } : {}),
    };

    const response = await fetch(url, { ...options, headers });
    const text = await response.text();
    const data = text ? JSON.parse(text) : null;

    return {
        ok: response.ok,
        status: response.status,
        data,
    };
}

function showStatus(message, type = 'success') {
    const isError = type === 'error';
    statusBannerText.textContent = message;
    statusBannerIcon.replaceChildren(createSvgIcon(isError ? ICONS.alertCircle : ICONS.checkCircle, 18));
    statusBanner.className = `status-banner ${isError ? 'is-error' : 'is-success'}`;
    statusBanner.hidden = false;
}

function clearStatus() {
    statusBanner.hidden = true;
    statusBannerText.textContent = '';
    statusBannerIcon.replaceChildren();
}

function linkError(el, shown) {
    const errorId = el.dataset.error;
    if (!errorId) {
        return;
    }

    const ids = (el.getAttribute('aria-describedby') || '')
        .split(/\s+/)
        .filter((id) => id && id !== errorId);

    if (shown) {
        ids.push(errorId);
    }

    if (ids.length > 0) {
        el.setAttribute('aria-describedby', ids.join(' '));
    } else {
        el.removeAttribute('aria-describedby');
    }
}

function setFieldError(el, message) {
    const errorEl = document.getElementById(el.dataset.error || '');
    if (errorEl) {
        errorEl.textContent = message;
    }
    el.setAttribute('aria-invalid', 'true');
    linkError(el, true);
}

function clearFieldError(el) {
    const errorEl = document.getElementById(el.dataset.error || '');
    if (errorEl) {
        errorEl.textContent = '';
    }
    el.setAttribute('aria-invalid', 'false');
    linkError(el, false);
}

function resetFormErrors(form, alertEl) {
    alertEl.hidden = true;
    alertEl.textContent = '';
    form.querySelectorAll('input, select, textarea').forEach((el) => {
        if (el.type !== 'hidden') {
            clearFieldError(el);
        }
    });
}

function announceFieldError(el) {
    const errorEl = document.getElementById(el.dataset.error || '');
    if (!errorEl || !errorEl.textContent) {
        return;
    }

    const label = el.labels?.[0]?.textContent?.replace('*', '').trim() || '';
    clearTimeout(announceTimer);
    fieldAnnouncer.textContent = '';
    announceTimer = setTimeout(() => {
        fieldAnnouncer.textContent = label ? `${label}: ${errorEl.textContent}` : errorEl.textContent;
    }, 500);
}

function applyFieldValidation(el, announce = false) {
    if (!el.matches?.('input, select, textarea') || el.type === 'hidden') {
        return;
    }

    if (el.validity.valid) {
        clearFieldError(el);
        return;
    }

    if (el.matches(':user-invalid')) {
        setFieldError(el, el.validationMessage);
        if (announce) {
            announceFieldError(el);
        }
    }
}

function applyServerValidationErrors(form, alertEl, fieldMap, data) {
    const errors = data?.errors || {};
    let firstInvalid = null;

    for (const [field, messages] of Object.entries(errors)) {
        const input = fieldMap[field];
        const msg = Array.isArray(messages) ? messages[0] : String(messages);
        if (input) {
            setFieldError(input, msg);
            if (!firstInvalid) {
                firstInvalid = input;
            }
        }
    }

    if (firstInvalid) {
        firstInvalid.focus();
        return;
    }

    alertEl.textContent = data?.message || 'Ocurrió un error al guardar los datos.';
    alertEl.hidden = false;
}

function selectTab(selectedTab, moveFocus = false) {
    for (const tab of tabs) {
        const isSelected = tab === selectedTab;
        tab.setAttribute('aria-selected', isSelected ? 'true' : 'false');
        tab.setAttribute('tabindex', isSelected ? '0' : '-1');
        const panel = panels.get(tab.id);
        if (panel) {
            panel.hidden = !isSelected;
        }
    }

    if (moveFocus) {
        selectedTab.focus();
    }
}

function syncViewButtons() {
    const isGrid = state.viewMode === 'grid';
    viewGridBtn.setAttribute('aria-pressed', isGrid ? 'true' : 'false');
    viewTableBtn.setAttribute('aria-pressed', isGrid ? 'false' : 'true');
    booksGridEl.hidden = !isGrid;
    booksTableWrap.hidden = isGrid;
}

function updateBookCountsFromList(booksList) {
    const counts = new Map();
    for (const book of booksList) {
        const key = String(book.category_id);
        counts.set(key, (counts.get(key) || 0) + 1);
    }
    state.allBooksCount = booksList.length;
    state.bookCountsByCategory = counts;
}

function updateSummaryAndCounts() {
    booksTabCount.textContent = String(state.allBooksCount);
    categoriesTabCount.textContent = String(state.categories.length);

    if (state.loadingBooks || state.loadingCategories) {
        return;
    }

    const visibleBooks = getFilteredBooks();
    const activeCategory = state.categories.find((c) => String(c.id) === state.activeCategoryFilter);
    const scopeLabel = activeCategory ? activeCategory.name : 'Todas las categorías';
    const booksLabel = visibleBooks.length === 1 ? 'libro' : 'libros';
    const categoriesLabel = state.categories.length === 1 ? 'categoría' : 'categorías';

    if (state.searchQuery.trim() !== '') {
        catalogSummary.textContent = `${visibleBooks.length} ${booksLabel} coinciden · ${scopeLabel}`;
        return;
    }

    catalogSummary.textContent = `${state.allBooksCount} ${booksLabel} · ${state.categories.length} ${categoriesLabel} · ${scopeLabel}`;
}

function getFilteredBooks() {
    const term = state.searchQuery.trim().toLowerCase();
    if (!term) {
        return state.books;
    }

    return state.books.filter((book) => {
        const title = (book.title || '').toLowerCase();
        const author = (book.author || '').toLowerCase();
        const year = String(book.published_year || '');
        return title.includes(term) || author.includes(term) || year.includes(term);
    });
}

function populateCategorySelects() {
    const previousBookCategory = bookCategorySelect.value;

    bookCategorySelect.replaceChildren(new Option('Selecciona una categoría', ''));
    for (const category of state.categories) {
        bookCategorySelect.append(new Option(category.name, String(category.id)));
    }

    if (state.categories.some((c) => String(c.id) === previousBookCategory)) {
        bookCategorySelect.value = previousBookCategory;
    }

    if (
        state.activeCategoryFilter !== ''
        && !state.categories.some((c) => String(c.id) === state.activeCategoryFilter)
    ) {
        state.activeCategoryFilter = '';
    }
}

function renderCategoryChips() {
    const chips = [];

    const allChip = document.createElement('button');
    allChip.type = 'button';
    allChip.className = 'chip';
    allChip.setAttribute('aria-pressed', state.activeCategoryFilter === '' ? 'true' : 'false');
    allChip.textContent = 'Todas';

    const allCount = document.createElement('span');
    allCount.className = 'chip-count';
    allCount.textContent = String(state.allBooksCount);
    allChip.append(allCount);

    allChip.addEventListener('click', async () => {
        if (state.activeCategoryFilter === '') {
            return;
        }
        clearStatus();
        state.activeCategoryFilter = '';
        renderCategoryChips();
        await loadBooks('');
    });
    chips.push(allChip);

    for (const category of state.categories) {
        const categoryId = String(category.id);
        const chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'chip';
        chip.setAttribute('aria-pressed', state.activeCategoryFilter === categoryId ? 'true' : 'false');
        chip.textContent = category.name;

        const countSpan = document.createElement('span');
        countSpan.className = 'chip-count';
        countSpan.textContent = String(state.bookCountsByCategory.get(categoryId) || 0);
        chip.append(countSpan);

        chip.addEventListener('click', async () => {
            if (state.activeCategoryFilter === categoryId) {
                return;
            }
            clearStatus();
            state.activeCategoryFilter = categoryId;
            renderCategoryChips();
            await loadBooks(categoryId);
        });

        chips.push(chip);
    }

    categoryChipsEl.replaceChildren(...chips);
}

function buildEmptyBooksNode() {
    const hasFilters = state.activeCategoryFilter !== '' || state.searchQuery.trim() !== '';

    const box = document.createElement('div');
    box.className = 'empty-state';

    const iconWrap = document.createElement('div');
    iconWrap.className = 'empty-state-icon';
    iconWrap.append(createSvgIcon(hasFilters ? ICONS.filter : ICONS.bookOpen, 20));

    const heading = document.createElement('h2');
    heading.textContent = hasFilters
        ? 'No hay libros que coincidan con el filtro'
        : 'Todavía no hay libros en el catálogo';

    const message = document.createElement('p');
    message.textContent = hasFilters
        ? 'Prueba limpiando la búsqueda o seleccionando otra categoría para ver el resto del catálogo.'
        : 'Agrega el primer ejemplar para comenzar a organizar autores, años y categorías.';

    const actionBtn = document.createElement('button');
    actionBtn.type = 'button';
    actionBtn.className = hasFilters ? 'btn btn-secondary' : 'btn btn-primary';
    actionBtn.textContent = hasFilters ? 'Limpiar filtros' : 'Agregar libro';
    actionBtn.addEventListener('click', async () => {
        if (hasFilters) {
            state.searchQuery = '';
            booksSearchInput.value = '';
            const needsReload = state.activeCategoryFilter !== '';
            state.activeCategoryFilter = '';
            renderCategoryChips();
            if (needsReload) {
                await loadBooks('');
            } else {
                renderBooks();
            }
            return;
        }
        openBookDialog();
    });

    box.append(iconWrap, heading, message, actionBtn);
    return box;
}

function createActionButtons(onEdit, onDelete) {
    const wrap = document.createElement('div');
    wrap.className = 'row-actions';

    const editBtn = document.createElement('button');
    editBtn.type = 'button';
    editBtn.className = 'btn btn-secondary btn-sm';
    editBtn.append(createSvgIcon(ICONS.edit, 14), document.createTextNode('Editar'));
    editBtn.addEventListener('click', onEdit);

    const deleteBtn = document.createElement('button');
    deleteBtn.type = 'button';
    deleteBtn.className = 'btn btn-danger-ghost btn-sm';
    deleteBtn.append(createSvgIcon(ICONS.trash, 14), document.createTextNode('Eliminar'));
    deleteBtn.addEventListener('click', onDelete);

    wrap.append(editBtn, deleteBtn);
    return wrap;
}

function renderBooks() {
    syncViewButtons();
    updateSummaryAndCounts();

    const books = getFilteredBooks();

    if (books.length === 0) {
        booksGridEl.replaceChildren(buildEmptyBooksNode());

        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 6;
        cell.className = 'empty-cell';
        cell.textContent = state.activeCategoryFilter !== '' || state.searchQuery.trim() !== ''
            ? 'No hay libros que coincidan con el filtro aplicado.'
            : 'No hay libros registrados para mostrar.';
        row.append(cell);
        booksTbody.replaceChildren(row);
        return;
    }

    const cards = books.map((book) => {
        const card = document.createElement('article');
        card.className = 'book-card';

        const body = document.createElement('div');
        body.className = 'book-card-body';

        const meta = document.createElement('div');
        meta.className = 'book-card-meta';

        const categoryBadge = document.createElement('span');
        categoryBadge.className = 'category-badge';
        categoryBadge.textContent = book.category?.name || `#${book.category_id}`;

        const yearTag = document.createElement('span');
        yearTag.className = 'year-tag';
        yearTag.textContent = String(book.published_year);

        meta.append(categoryBadge, yearTag);

        const title = document.createElement('h3');
        title.className = 'book-card-title';
        title.textContent = book.title;

        const author = document.createElement('p');
        author.className = 'book-card-author';
        author.textContent = book.author;

        body.append(meta, title, author);

        if (book.description) {
            const desc = document.createElement('p');
            desc.className = 'book-card-desc';
            desc.textContent = book.description;
            body.append(desc);
        }

        const footer = document.createElement('div');
        footer.className = 'book-card-footer';
        footer.append(
            createActionButtons(
                () => openBookDialog(book),
                () => requestDeleteBook(book),
            ),
        );

        card.append(body, footer);
        return card;
    });

    const rows = books.map((book) => {
        const row = document.createElement('tr');

        const titleCell = document.createElement('th');
        titleCell.scope = 'row';
        titleCell.textContent = book.title;

        const authorCell = document.createElement('td');
        authorCell.textContent = book.author;

        const yearCell = document.createElement('td');
        yearCell.className = 'year-cell';
        yearCell.textContent = String(book.published_year);

        const categoryCell = document.createElement('td');
        const badge = document.createElement('span');
        badge.className = 'category-badge';
        badge.textContent = book.category?.name || `#${book.category_id}`;
        categoryCell.append(badge);

        const descCell = document.createElement('td');
        descCell.className = 'desc-cell';
        descCell.textContent = book.description || '—';

        const actionsCell = document.createElement('td');
        actionsCell.append(
            createActionButtons(
                () => openBookDialog(book),
                () => requestDeleteBook(book),
            ),
        );

        row.append(titleCell, authorCell, yearCell, categoryCell, descCell, actionsCell);
        return row;
    });

    booksGridEl.replaceChildren(...cards);
    booksTbody.replaceChildren(...rows);
}

function renderCategories() {
    updateSummaryAndCounts();

    if (state.categories.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 3;
        cell.className = 'empty-cell';
        cell.textContent = 'No hay categorías registradas.';
        row.append(cell);
        categoriesTbody.replaceChildren(row);
        return;
    }

    const rows = state.categories.map((category) => {
        const row = document.createElement('tr');
        const categoryId = String(category.id);
        const count = state.bookCountsByCategory.get(categoryId) || 0;

        const nameCell = document.createElement('th');
        nameCell.scope = 'row';
        nameCell.textContent = category.name;

        const countCell = document.createElement('td');
        countCell.className = 'count-cell';
        const pill = document.createElement('span');
        pill.className = 'count-pill';
        pill.textContent = count === 1 ? '1 libro' : `${count} libros`;
        countCell.append(pill);

        const actionsCell = document.createElement('td');
        const actionsWrap = createActionButtons(
            () => openCategoryDialog(category),
            () => requestDeleteCategory(category),
        );

        const filterBtn = document.createElement('button');
        filterBtn.type = 'button';
        filterBtn.className = 'btn btn-ghost btn-sm';
        filterBtn.textContent = 'Ver libros';
        filterBtn.addEventListener('click', async () => {
            clearStatus();
            state.activeCategoryFilter = categoryId;
            selectTab(tabs[0]);
            renderCategoryChips();
            await loadBooks(categoryId);
        });

        actionsWrap.prepend(filterBtn);
        actionsCell.append(actionsWrap);

        row.append(nameCell, countCell, actionsCell);
        return row;
    });

    categoriesTbody.replaceChildren(...rows);
}

async function refreshAllBookCounts() {
    const result = await apiRequest('/api/books');
    if (result.ok) {
        updateBookCountsFromList(result.data?.data || []);
        renderCategoryChips();
        renderCategories();
        updateSummaryAndCounts();
    }
}

async function loadCategories() {
    state.loadingCategories = true;
    const result = await apiRequest('/api/categories');
    state.loadingCategories = false;

    if (!result.ok) {
        showStatus('No se pudieron cargar las categorías.', 'error');
        return;
    }

    state.categories = result.data?.data || [];
    populateCategorySelects();
    renderCategoryChips();
    renderCategories();
}

async function loadBooks(categoryId = state.activeCategoryFilter) {
    state.loadingBooks = true;
    const query = categoryId ? `?category=${encodeURIComponent(categoryId)}` : '';
    const result = await apiRequest(`/api/books${query}`);
    state.loadingBooks = false;

    if (!result.ok) {
        showStatus('No se pudieron cargar los libros.', 'error');
        return;
    }

    state.books = result.data?.data || [];
    if (!categoryId) {
        updateBookCountsFromList(state.books);
        renderCategoryChips();
        renderCategories();
    }

    renderBooks();
}

function openBookDialog(book = null) {
    bookForm.reset();
    resetFormErrors(bookForm, bookFormAlert);
    populateCategorySelects();

    if (book) {
        bookDialogTitle.textContent = 'Editar libro';
        bookIdInput.value = String(book.id);
        bookTitleInput.value = book.title;
        bookAuthorInput.value = book.author;
        bookYearInput.value = String(book.published_year);
        bookCategorySelect.value = String(book.category_id);
        bookDescriptionInput.value = book.description || '';
    } else {
        bookDialogTitle.textContent = 'Nuevo libro';
        bookIdInput.value = '';
        if (state.activeCategoryFilter) {
            bookCategorySelect.value = state.activeCategoryFilter;
        }
    }

    bookDialog.showModal();
    bookTitleInput.focus();
}

function openCategoryDialog(category = null) {
    categoryForm.reset();
    resetFormErrors(categoryForm, categoryFormAlert);

    if (category) {
        categoryDialogTitle.textContent = 'Editar categoría';
        categoryIdInput.value = String(category.id);
        categoryNameInput.value = category.name;
    } else {
        categoryDialogTitle.textContent = 'Nueva categoría';
        categoryIdInput.value = '';
    }

    categoryDialog.showModal();
    categoryNameInput.focus();
}

function requestDeleteBook(book) {
    state.pendingDelete = { type: 'book', item: book };
    confirmDialogMessage.textContent = `¿Seguro que deseas eliminar el libro "${book.title}"?`;
    confirmDialog.showModal();
}

function requestDeleteCategory(category) {
    state.pendingDelete = { type: 'category', item: category };
    confirmDialogMessage.textContent = `¿Seguro que deseas eliminar la categoría "${category.name}"?`;
    confirmDialog.showModal();
}

themeToggleBtn?.addEventListener('click', () => {
    const systemScheme = getSystemScheme();
    const rendered = getStoredScheme() ?? systemScheme;
    const target = rendered === 'dark' ? 'light' : 'dark';

    if (target !== systemScheme) {
        writeStorage('color-scheme', target);
    } else {
        writeStorage('color-scheme', null);
    }

    syncColorSchemeMeta();
});

window.addEventListener('storage', (event) => {
    if (event.key === 'color-scheme') {
        syncColorSchemeMeta();
    }
});

statusDismissBtn?.addEventListener('click', () => clearStatus());

tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => selectTab(tab));
    tab.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowRight') {
            event.preventDefault();
            selectTab(tabs[(index + 1) % tabs.length], true);
        } else if (event.key === 'ArrowLeft') {
            event.preventDefault();
            selectTab(tabs[(index - 1 + tabs.length) % tabs.length], true);
        } else if (event.key === 'Home') {
            event.preventDefault();
            selectTab(tabs[0], true);
        } else if (event.key === 'End') {
            event.preventDefault();
            selectTab(tabs[tabs.length - 1], true);
        }
    });
});

booksSearchInput.addEventListener('input', () => {
    state.searchQuery = booksSearchInput.value;
    renderBooks();
});

[viewGridBtn, viewTableBtn].forEach((button) => {
    button.addEventListener('click', () => {
        const nextView = button.dataset.view === 'table' ? 'table' : 'grid';
        if (state.viewMode === nextView) {
            return;
        }
        state.viewMode = nextView;
        writeStorage('library-view', nextView);
        syncViewButtons();
    });
});

document.getElementById('new-book-btn').addEventListener('click', () => openBookDialog());
document.getElementById('new-category-btn').addEventListener('click', () => openCategoryDialog());

document.querySelectorAll('[data-close-dialog]').forEach((button) => {
    button.addEventListener('click', () => {
        const dialog = document.getElementById(button.dataset.closeDialog);
        closeTopLayer(dialog);
    });
});

[bookDialog, categoryDialog, confirmDialog].forEach((dialog) => {
    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeTopLayer(dialog);
    });

    if (!('closedBy' in HTMLDialogElement.prototype)) {
        dialog.addEventListener('click', (event) => {
            if (event.target !== dialog) {
                return;
            }
            const rect = dialog.getBoundingClientRect();
            const isDialogContent = (
                rect.top <= event.clientY
                && event.clientY <= rect.top + rect.height
                && rect.left <= event.clientX
                && event.clientX <= rect.left + rect.width
            );
            if (!isDialogContent) {
                closeTopLayer(dialog);
            }
        });
    }
});

confirmForm.addEventListener('submit', (event) => {
    if (!supportsTopLayerExit()) {
        event.preventDefault();
        closeTopLayer(confirmDialog, event.submitter?.value || 'cancel');
    }
});

[bookForm, categoryForm].forEach((form) => {
    form.addEventListener('blur', (event) => applyFieldValidation(event.target, true), true);
    form.addEventListener('change', (event) => applyFieldValidation(event.target, true), true);
    form.addEventListener('input', (event) => {
        if (event.target.getAttribute('aria-invalid') === 'true' && event.target.validity.valid) {
            clearFieldError(event.target);
        }
    });
});

bookForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    resetFormErrors(bookForm, bookFormAlert);

    const fieldMap = {
        title: bookTitleInput,
        author: bookAuthorInput,
        published_year: bookYearInput,
        category_id: bookCategorySelect,
        description: bookDescriptionInput,
    };

    const invalidFields = Object.values(fieldMap).filter((el) => !el.validity.valid);
    if (invalidFields.length > 0) {
        invalidFields.forEach((el) => setFieldError(el, el.validationMessage));
        invalidFields[0].focus();
        return;
    }

    const id = bookIdInput.value;
    const payload = {
        title: bookTitleInput.value.trim(),
        author: bookAuthorInput.value.trim(),
        published_year: Number(bookYearInput.value),
        category_id: Number(bookCategorySelect.value),
        description: bookDescriptionInput.value.trim() || null,
    };

    bookSubmitBtn.disabled = true;

    try {
        const result = await apiRequest(id ? `/api/books/${id}` : '/api/books', {
            method: id ? 'PUT' : 'POST',
            body: JSON.stringify(payload),
        });

        if (!result.ok) {
            applyServerValidationErrors(bookForm, bookFormAlert, fieldMap, result.data);
            return;
        }

        await closeTopLayer(bookDialog);
        await loadBooks(state.activeCategoryFilter);
        if (state.activeCategoryFilter !== '') {
            await refreshAllBookCounts();
        }
        showStatus(id ? 'Libro actualizado correctamente.' : 'Libro creado correctamente.');
    } finally {
        bookSubmitBtn.disabled = false;
    }
});

categoryForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    resetFormErrors(categoryForm, categoryFormAlert);

    if (!categoryNameInput.validity.valid) {
        setFieldError(categoryNameInput, categoryNameInput.validationMessage);
        categoryNameInput.focus();
        return;
    }

    const id = categoryIdInput.value;
    const payload = {
        name: categoryNameInput.value.trim(),
    };

    categorySubmitBtn.disabled = true;

    try {
        const result = await apiRequest(id ? `/api/categories/${id}` : '/api/categories', {
            method: id ? 'PUT' : 'POST',
            body: JSON.stringify(payload),
        });

        if (!result.ok) {
            applyServerValidationErrors(categoryForm, categoryFormAlert, { name: categoryNameInput }, result.data);
            return;
        }

        await closeTopLayer(categoryDialog);
        await Promise.all([loadCategories(), loadBooks(state.activeCategoryFilter)]);
        if (state.activeCategoryFilter !== '') {
            await refreshAllBookCounts();
        }
        showStatus(id ? 'Categoría actualizada correctamente.' : 'Categoría creada correctamente.');
    } finally {
        categorySubmitBtn.disabled = false;
    }
});

confirmDialog.addEventListener('close', async () => {
    const decision = confirmDialog.returnValue;
    confirmDialog.returnValue = '';

    const pending = state.pendingDelete;
    state.pendingDelete = null;

    if (decision !== 'confirm' || !pending) {
        return;
    }

    if (pending.type === 'book') {
        const result = await apiRequest(`/api/books/${pending.item.id}`, { method: 'DELETE' });
        if (!result.ok) {
            showStatus(result.data?.message || 'No se pudo eliminar el libro.', 'error');
            return;
        }

        await loadBooks(state.activeCategoryFilter);
        if (state.activeCategoryFilter !== '') {
            await refreshAllBookCounts();
        }
        showStatus('Libro eliminado correctamente.');
        return;
    }

    const result = await apiRequest(`/api/categories/${pending.item.id}`, { method: 'DELETE' });
    if (!result.ok) {
        showStatus(result.data?.message || 'No se pudo eliminar la categoría.', 'error');
        return;
    }

    await Promise.all([loadCategories(), loadBooks(state.activeCategoryFilter)]);
    if (state.activeCategoryFilter !== '') {
        await refreshAllBookCounts();
    }
    showStatus('Categoría eliminada correctamente.');
});

syncViewButtons();
await Promise.all([loadCategories(), loadBooks('')]);
