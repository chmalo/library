const state = {
    categories: [],
    books: [],
    activeCategoryFilter: '',
    pendingDelete: null,
};

const statusBanner = document.getElementById('status-banner');
const fieldAnnouncer = document.getElementById('field-announcer');

const tabs = [
    document.getElementById('tab-books'),
    document.getElementById('tab-categories'),
];
const panels = new Map([
    ['tab-books', document.getElementById('panel-books')],
    ['tab-categories', document.getElementById('panel-categories')],
]);

const filterForm = document.getElementById('books-filter-form');
const filterCategorySelect = document.getElementById('filter-category');
const clearFilterBtn = document.getElementById('clear-filter-btn');
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
const confirmDialogMessage = document.getElementById('confirm-dialog-message');

let announceTimer = null;

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
    statusBanner.textContent = message;
    statusBanner.className = `status-banner ${type === 'error' ? 'is-error' : 'is-success'}`;
    statusBanner.hidden = false;
}

function clearStatus() {
    statusBanner.hidden = true;
    statusBanner.textContent = '';
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

function populateCategorySelects() {
    const previousFilter = filterCategorySelect.value;
    const previousBookCategory = bookCategorySelect.value;

    filterCategorySelect.replaceChildren(new Option('Todas las categorías', ''));
    bookCategorySelect.replaceChildren(new Option('Selecciona una categoría', ''));

    for (const category of state.categories) {
        filterCategorySelect.append(new Option(category.name, String(category.id)));
        bookCategorySelect.append(new Option(category.name, String(category.id)));
    }

    if (state.categories.some((c) => String(c.id) === previousFilter)) {
        filterCategorySelect.value = previousFilter;
    } else {
        filterCategorySelect.value = '';
        state.activeCategoryFilter = '';
    }

    if (state.categories.some((c) => String(c.id) === previousBookCategory)) {
        bookCategorySelect.value = previousBookCategory;
    }
}

function renderBooks() {
    if (state.books.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 6;
        cell.className = 'empty-cell';
        cell.textContent = 'No hay libros registrados para mostrar.';
        row.append(cell);
        booksTbody.replaceChildren(row);
        return;
    }

    const rows = state.books.map((book) => {
        const row = document.createElement('tr');

        const titleCell = document.createElement('th');
        titleCell.scope = 'row';
        titleCell.textContent = book.title;

        const authorCell = document.createElement('td');
        authorCell.textContent = book.author;

        const yearCell = document.createElement('td');
        yearCell.textContent = String(book.published_year);

        const categoryCell = document.createElement('td');
        categoryCell.textContent = book.category?.name || `#${book.category_id}`;

        const descCell = document.createElement('td');
        descCell.className = 'desc-cell';
        descCell.textContent = book.description || '—';

        const actionsCell = document.createElement('td');
        const actionsWrap = document.createElement('div');
        actionsWrap.className = 'row-actions';

        const editBtn = document.createElement('button');
        editBtn.type = 'button';
        editBtn.className = 'btn btn-secondary btn-sm';
        editBtn.textContent = 'Editar';
        editBtn.addEventListener('click', () => openBookDialog(book));

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-danger btn-sm';
        deleteBtn.textContent = 'Eliminar';
        deleteBtn.addEventListener('click', () => requestDeleteBook(book));

        actionsWrap.append(editBtn, deleteBtn);
        actionsCell.append(actionsWrap);

        row.append(titleCell, authorCell, yearCell, categoryCell, descCell, actionsCell);
        return row;
    });

    booksTbody.replaceChildren(...rows);
}

function renderCategories() {
    if (state.categories.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 2;
        cell.className = 'empty-cell';
        cell.textContent = 'No hay categorías registradas.';
        row.append(cell);
        categoriesTbody.replaceChildren(row);
        return;
    }

    const rows = state.categories.map((category) => {
        const row = document.createElement('tr');

        const nameCell = document.createElement('th');
        nameCell.scope = 'row';
        nameCell.textContent = category.name;

        const actionsCell = document.createElement('td');
        const actionsWrap = document.createElement('div');
        actionsWrap.className = 'row-actions';

        const editBtn = document.createElement('button');
        editBtn.type = 'button';
        editBtn.className = 'btn btn-secondary btn-sm';
        editBtn.textContent = 'Editar';
        editBtn.addEventListener('click', () => openCategoryDialog(category));

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-danger btn-sm';
        deleteBtn.textContent = 'Eliminar';
        deleteBtn.addEventListener('click', () => requestDeleteCategory(category));

        actionsWrap.append(editBtn, deleteBtn);
        actionsCell.append(actionsWrap);

        row.append(nameCell, actionsCell);
        return row;
    });

    categoriesTbody.replaceChildren(...rows);
}

async function loadCategories() {
    const result = await apiRequest('/api/categories');
    if (!result.ok) {
        showStatus('No se pudieron cargar las categorías.', 'error');
        return;
    }

    state.categories = result.data?.data || [];
    populateCategorySelects();
    renderCategories();
}

async function loadBooks(categoryId = state.activeCategoryFilter) {
    const query = categoryId ? `?category=${encodeURIComponent(categoryId)}` : '';
    const result = await apiRequest(`/api/books${query}`);
    if (!result.ok) {
        showStatus('No se pudieron cargar los libros.', 'error');
        return;
    }

    state.books = result.data?.data || [];
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

filterForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearStatus();
    state.activeCategoryFilter = filterCategorySelect.value;
    await loadBooks(state.activeCategoryFilter);
});

clearFilterBtn.addEventListener('click', async () => {
    clearStatus();
    filterCategorySelect.value = '';
    state.activeCategoryFilter = '';
    await loadBooks('');
});

document.getElementById('new-book-btn').addEventListener('click', () => openBookDialog());
document.getElementById('new-category-btn').addEventListener('click', () => openCategoryDialog());

document.querySelectorAll('[data-close-dialog]').forEach((button) => {
    button.addEventListener('click', () => {
        document.getElementById(button.dataset.closeDialog)?.close();
    });
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

        bookDialog.close();
        await loadBooks(state.activeCategoryFilter);
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

        categoryDialog.close();
        await Promise.all([loadCategories(), loadBooks(state.activeCategoryFilter)]);
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
        showStatus('Libro eliminado correctamente.');
        return;
    }

    const result = await apiRequest(`/api/categories/${pending.item.id}`, { method: 'DELETE' });
    if (!result.ok) {
        showStatus(result.data?.message || 'No se pudo eliminar la categoría.', 'error');
        return;
    }

    await Promise.all([loadCategories(), loadBooks(state.activeCategoryFilter)]);
    showStatus('Categoría eliminada correctamente.');
});

await Promise.all([loadCategories(), loadBooks('')]);
