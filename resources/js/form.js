const formElement = document.querySelector('#risk-form');
const formContent = document.querySelector('#form-content');
const loadingElement = document.querySelector('#loading');
const noticeElement = document.querySelector('#notice');

function showNotice(message, type = 'success') {
    noticeElement.textContent = message;
    noticeElement.className = `notice notice--${type}`;
    noticeElement.hidden = false;
}

function addField(sectionElement, field) {
    const wrapper = document.createElement('fieldset');
    wrapper.className = 'field';

    const legend = document.createElement('legend');
    legend.className = 'field-label field-label-row';
    legend.textContent = field.label;
    const typeLabel = document.createElement('span');
    typeLabel.className = 'field-type';
    typeLabel.textContent = field.type === 'radio_button'
        ? 'Pilih satu'
        : field.type === 'checkbox'
            ? 'Bisa pilih lebih dari satu'
            : field.sub_type === 'date'
                ? 'Tanggal'
                : 'Isian';
    legend.append(typeLabel);
    wrapper.append(legend);

    if (field.description) {
        const hint = document.createElement('p');
        hint.className = 'field-hint';
        hint.id = `hint-${field.id}`;
        hint.textContent = field.description;
        wrapper.append(hint);
    }

    if (field.type === 'radio_button' || field.type === 'checkbox') {
        const choices = document.createElement('div');
        choices.className = 'choices';
        choices.setAttribute('role', 'group');
        choices.setAttribute('aria-label', field.label);
        if (field.description) {
            choices.setAttribute('aria-describedby', `hint-${field.id}`);
        }

        for (const option of field.options ?? []) {
            const label = document.createElement('label');
            label.className = 'choice';

            const input = document.createElement('input');
            input.type = field.type === 'radio_button' ? 'radio' : 'checkbox';
            input.name = `field-${field.id}${input.type === 'checkbox' ? '[]' : ''}`;
            input.value = option.id;
            input.dataset.fieldId = field.id;
            input.setAttribute('aria-label', `${field.label}: ${option.label}`);
            const text = document.createElement('span');
            text.textContent = option.label;
            label.append(input, text);
            choices.append(label);
        }

        wrapper.append(choices);
    } else {
        const input = field.type === 'long_text'
            ? document.createElement('textarea')
            : document.createElement('input');

        if (input instanceof HTMLInputElement) {
            input.type = field.sub_type === 'date'
                ? 'date'
                : field.sub_type === 'amount'
                    ? 'text'
                    : 'text';
            if (field.sub_type === 'amount') {
                input.inputMode = 'decimal';
            }
        }

        input.name = `field-${field.id}`;
        input.dataset.fieldId = field.id;
        input.value = '';
        input.className = 'text-input';
        if (field.description) {
            input.setAttribute('aria-describedby', `hint-${field.id}`);
        }
        wrapper.append(input);
    }

    sectionElement.append(wrapper);
}

function renderForm(form) {
    for (const [index, section] of form.schema.entries()) {
        const sectionElement = document.createElement('section');
        sectionElement.className = 'form-section';

        const sectionHeader = document.createElement('div');
        sectionHeader.className = 'form-section__header';

        const sectionNumber = document.createElement('span');
        sectionNumber.className = 'form-section__number';
        sectionNumber.textContent = String(index + 1).padStart(2, '0');
        sectionNumber.setAttribute('aria-hidden', 'true');

        const title = document.createElement('h2');
        title.textContent = section.name;
        sectionHeader.append(sectionNumber, title);
        sectionElement.append(sectionHeader);

        for (const field of section.payloads ?? []) {
            addField(sectionElement, field);
        }

        formContent.append(sectionElement);
    }

    loadingElement.hidden = true;
    formElement.hidden = false;
}

async function loadForm() {
    try {
        const response = await fetch(formElement.dataset.apiUrl, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Form tidak dapat dimuat. Pastikan database sudah di-seed.');
        }

        const result = await response.json();
        renderForm(result.data);
    } catch (error) {
        loadingElement.hidden = true;
        showNotice(error.message, 'error');
    }
}

formElement.addEventListener('submit', async (event) => {
    event.preventDefault();
    const answers = {};

    for (const field of formElement.querySelectorAll('[data-field-id]')) {
        const { fieldId } = field.dataset;

        if (field instanceof HTMLInputElement && field.type === 'checkbox') {
            answers[fieldId] ??= [];
            if (field.checked) {
                answers[fieldId].push(field.value);
            }
        } else if (field instanceof HTMLInputElement && field.type === 'radio') {
            if (field.checked) {
                answers[fieldId] = field.value;
            } else {
                answers[fieldId] ??= null;
            }
        } else {
            answers[fieldId] = field.value || null;
        }
    }

    const button = formElement.querySelector('button[type="submit"]');
    button.disabled = true;
    button.textContent = 'Mengirim...';
    noticeElement.hidden = true;

    try {
        const response = await fetch(formElement.dataset.submitUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ answers }),
        });
        const result = await response.json();

        if (!response.ok) {
            const messages = Object.values(result.errors ?? {}).flat();
            throw new Error(messages.join(' ') || result.message || 'Submission gagal disimpan.');
        }

        showNotice(result.message || 'Laporan berhasil dikirim.');
        if (result.redirect_url) {
            window.location.assign(result.redirect_url);
        }
    } catch (error) {
        showNotice(error.message, 'error');
    } finally {
        button.disabled = false;
        button.textContent = 'Kirim laporan';
    }
});

loadForm();
