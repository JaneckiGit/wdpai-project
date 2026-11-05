/* Panel rezerwacji: pokazuje dni i godziny wybranego trybu, zaznacza ciągły zakres godzin, obsługuje −/+ i liczy kwotę. */
const panel = document.querySelector('.reservation-panel');
const form = panel.querySelector('form');
const money = new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN' });

function selectedMode() {
    return form.querySelector('[name="mode"]:checked');
}

function activeSlots() {
    return form.querySelector(`.slots[data-mode="${selectedMode().value}"]`);
}

// Pola niewybranego trybu są wyłączone (disabled), więc formularz wyśle tylko dzień i godziny wybranego trybu.
function showMode() {
    form.querySelectorAll('.slots').forEach((slots) => {
        slots.disabled = slots !== activeSlots();
        slots.hidden = slots.disabled;
    });
    panel.querySelector('[data-price]').textContent = `${selectedMode().dataset.price} zł/h`;
    panel.querySelector('[data-unit]').textContent = selectedMode().dataset.unit;
}

// Zaznacza wszystkie godziny między najwcześniejszą a najpóźniejszą zaznaczoną, np. 18 i 21 → 18, 19, 20, 21.
function fillHours() {
    const boxes = [...activeSlots().querySelectorAll('[name="hours[]"]')];
    const checked = boxes.flatMap((box, index) => (box.checked ? [index] : []));
    boxes.forEach((box, index) => {
        box.checked = index >= Math.min(...checked) && index <= Math.max(...checked);
    });
}

function updateSummary() {
    const slots = activeSlots();
    const day = slots.querySelector('[name="date"]:checked');
    const hours = [...slots.querySelectorAll('[name="hours[]"]:checked')].map((box) => Number(box.value));
    const seats = slots.querySelector('[name="seats"]');
    const count = seats ? Number(seats.value) : 1;
    const price = Number(selectedMode().dataset.price);
    const range = hours.length > 0 ? `${hours[0]}:00–${hours[hours.length - 1] + 1}:00` : '';
    const summary = form.querySelector('.summary');

    slots.querySelector('[data-selected]').textContent = range === '' ? '' : `zaznaczono ${range}`;

    if (seats) {
        slots.querySelector('[data-seats-label]').textContent = count;
        slots.querySelector('[data-step="-1"]').disabled = count <= Number(seats.min);
        slots.querySelector('[data-step="1"]').disabled = count >= Number(seats.max);
    }

    summary.hidden = !day || hours.length === 0;
    if (summary.hidden) {
        return;
    }

    summary.querySelector('[data-term]').textContent = `${day.dataset.label}, ${range} (${hours.length} h)`;
    summary.querySelector('[data-calc]').textContent = `${seats ? `${count} × ` : ''}${hours.length} h × ${price} zł`;
    summary.querySelectorAll('[data-total]').forEach((total) => {
        total.textContent = money.format(count * hours.length * price);
    });
}

form.addEventListener('change', (event) => {
    if (event.target.name === 'mode') {
        showMode();
    } else if (event.target.name === 'hours[]') {
        fillHours();
    } else if (event.target.name === 'purpose') {
        form.querySelector('[data-purpose-icon]').className = `fa-solid ${event.target.selectedOptions[0].dataset.icon}`;
    }

    updateSummary();
});

// stepUp/stepDown pilnują atrybutów min i max pola liczby miejsc.
form.querySelectorAll('[data-step]').forEach((button) => {
    button.addEventListener('click', () => {
        activeSlots().querySelector('[name="seats"]').stepUp(Number(button.dataset.step));
        updateSummary();
    });
});

updateSummary();
