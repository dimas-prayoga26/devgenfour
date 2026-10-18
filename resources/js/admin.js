/**
 * Live icon preview: asks the server which icon a category gets while the admin types.
 */
function initIconPreview() {
    const category = document.getElementById('category');
    const preview = document.getElementById('icon-preview');

    if (! category || ! preview) {
        return;
    }

    const box = preview.querySelector('[data-icon-box]');
    const name = preview.querySelector('[data-icon-name]');
    let timer = null;
    let latestRequest = 0;

    const refresh = async () => {
        const request = ++latestRequest;
        box.classList.add('opacity-40');

        try {
            const response = await fetch(preview.dataset.url + '?' + new URLSearchParams({ category: category.value }), {
                headers: { Accept: 'application/json' },
            });

            // Ignore slow answers to earlier keystrokes.
            if (! response.ok || request !== latestRequest) {
                return;
            }

            const result = await response.json();
            preview.classList.toggle('hidden', result.icon === null);

            if (result.icon !== null) {
                box.innerHTML = '<svg class="size-4.5 stroke-[1.6]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + result.svg + '</svg>';
                name.textContent = result.icon;
            }
        } catch {
            // Keep showing the previous icon when the preview cannot be loaded.
        } finally {
            if (request === latestRequest) {
                box.classList.remove('opacity-40');
            }
        }
    };

    category.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(refresh, 400);
    });

    if (category.value.trim() !== '' && category.value !== preview.dataset.savedCategory) {
        refresh();
    }
}

/**
 * Turns the comma-separated tag field into chips with suggestions; unknown tags are added as new ones.
 *
 * Without JavaScript the original text field keeps working, so the form never depends on this script.
 */
function initTagInput(wrapper) {
    const source = wrapper.querySelector('[data-tag-source]');
    const max = Number(wrapper.dataset.max);
    const suggestions = JSON.parse(wrapper.dataset.suggestions);
    const listId = source.id + '-options';
    const tags = source.value.split(',').map((tag) => tag.trim()).filter(Boolean);
    const sameTag = (first, second) => first.toLocaleLowerCase() === second.toLocaleLowerCase();
    let options = [];
    let activeOption = -1;

    const box = document.createElement('div');
    box.className = 'mt-1.5 flex min-h-11.5 cursor-text flex-wrap items-center gap-1.5 rounded-md border border-line bg-white px-2 py-1.5 focus-within:border-brand focus-within:ring-3 focus-within:ring-brand/15';

    const chips = document.createElement('div');
    chips.className = 'contents';

    const input = document.createElement('input');
    input.type = 'text';
    input.id = source.id;
    input.className = 'min-w-40 flex-1 border-0 bg-transparent px-1.5 py-1 text-[14px] text-navy outline-none placeholder:text-[#9aa8b5]';
    input.placeholder = source.placeholder;
    input.autocomplete = 'off';
    input.setAttribute('role', 'combobox');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-controls', listId);
    input.setAttribute('aria-expanded', 'false');

    const list = document.createElement('ul');
    list.id = listId;
    list.setAttribute('role', 'listbox');
    list.className = 'absolute inset-x-0 top-full z-10 mt-1 hidden max-h-60 overflow-auto rounded-md border border-line bg-white py-1 shadow-[0_12px_30px_#0c304514]';

    const hiddenInputs = document.createElement('div');

    source.removeAttribute('name');
    source.removeAttribute('id');
    source.required = false;
    source.hidden = true;
    wrapper.classList.add('relative');
    box.append(chips, input);
    wrapper.append(box, list, hiddenInputs);

    const closeList = () => {
        list.classList.add('hidden');
        input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant');
        activeOption = -1;
    };

    const renderList = () => {
        const query = input.value.trim();
        const lowerQuery = query.toLocaleLowerCase();
        const remaining = suggestions.filter((tag) => ! tags.some((chosen) => sameTag(chosen, tag)));

        // Tags that start with the typed text come first, then tags with a word that starts with it.
        const rank = (tag) => {
            const lowerTag = tag.toLocaleLowerCase();

            if (lowerTag.startsWith(lowerQuery)) {
                return 0;
            }

            return lowerTag.split(/\s+/).some((word) => word.startsWith(lowerQuery)) ? 1 : 2;
        };

        options = remaining
            .filter((tag) => tag.toLocaleLowerCase().includes(lowerQuery))
            .sort((first, second) => rank(first) - rank(second))
            .slice(0, 8)
            .map((tag) => ({ value: tag, label: tag, isNew: false }));

        if (query !== '' && ! [...suggestions, ...tags].some((tag) => sameTag(tag, query))) {
            options.unshift({ value: query, label: `Tambah "${query}"`, isNew: true });
        }

        list.replaceChildren(...options.map((option, index) => {
            const item = document.createElement('li');
            item.id = `${listId}-${index}`;
            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', String(index === activeOption));
            item.className = 'cursor-pointer px-3 py-2 text-[13px] ' + (option.isNew ? 'font-semibold text-brand ' : 'text-navy ') + (index === activeOption ? 'bg-[#eaf6fc]' : 'hover:bg-[#f4f9fc]');
            item.textContent = option.label;
            item.addEventListener('mousedown', (event) => {
                event.preventDefault();
                add(option.value);
            });

            return item;
        }));

        const isOpen = document.activeElement === input && options.length > 0 && tags.length < max;
        list.classList.toggle('hidden', ! isOpen);
        input.setAttribute('aria-expanded', String(isOpen));

        if (activeOption >= 0) {
            input.setAttribute('aria-activedescendant', `${listId}-${activeOption}`);
        } else {
            input.removeAttribute('aria-activedescendant');
        }
    };

    const render = () => {
        chips.replaceChildren(...tags.map((tag, index) => {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1 rounded-md bg-[#eaf6fc] py-1 pr-1 pl-2.5 text-[13px] font-semibold text-brand';
            chip.append(tag);

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'grid size-5 cursor-pointer place-items-center rounded text-[15px] leading-none hover:bg-[#d8eef9]';
            remove.setAttribute('aria-label', `Hapus tag ${tag}`);
            remove.textContent = '×';
            remove.addEventListener('click', () => {
                tags.splice(index, 1);
                render();
                input.focus();
            });

            chip.append(remove);

            return chip;
        }));

        hiddenInputs.replaceChildren(...tags.map((tag) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'tags[]';
            hidden.value = tag;

            return hidden;
        }));

        input.disabled = tags.length >= max;
        input.placeholder = tags.length >= max ? `Maksimal ${max} tag` : source.placeholder;
        renderList();
    };

    function add(value) {
        const tag = value.trim();

        if (tag !== '' && tags.length < max && ! tags.some((chosen) => sameTag(chosen, tag))) {
            tags.push(suggestions.find((suggestion) => sameTag(suggestion, tag)) ?? tag);
        }

        input.value = '';
        activeOption = -1;
        render();
        input.focus();
    }

    input.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            const step = event.key === 'ArrowDown' ? 1 : -1;
            activeOption = options.length === 0 ? -1 : (activeOption + step + options.length) % options.length;
            renderList();
        } else if (event.key === 'Enter' || event.key === ',') {
            event.preventDefault();
            add(activeOption >= 0 ? options[activeOption].value : input.value);
        } else if (event.key === 'Backspace' && input.value === '' && tags.length > 0) {
            tags.pop();
            render();
        } else if (event.key === 'Escape') {
            closeList();
        }
    });

    input.addEventListener('input', () => {
        activeOption = -1;
        renderList();
    });
    input.addEventListener('focus', renderList);
    input.addEventListener('blur', closeList);
    box.addEventListener('click', (event) => {
        if (event.target === box) {
            input.focus();
        }
    });

    // A tag that was typed but not confirmed with Enter should not be lost on save.
    wrapper.closest('form').addEventListener('submit', () => {
        if (input.value.trim() !== '') {
            add(input.value);
        }
    });

    render();
}

initIconPreview();
document.querySelectorAll('[data-tag-input]').forEach(initTagInput);
