/* Dodaj lokal: licznik znaków opisu, dodawanie i usuwanie okien dostępności oraz mapa z pinezką do przeciągnięcia. */
const form = document.querySelector('.add-venue__form');
const description = form.querySelector('#description');
const windows = form.querySelector('.windows');

description.addEventListener('input', () => {
    form.querySelector('[data-count]').textContent = description.value.length;
});

function windowRows() {
    return windows.querySelectorAll('.window-row:not(.window-row--header)');
}

// Serwer czyta okna jako windows[0], windows[1]…, więc po każdej zmianie numerujemy nazwy pól od nowa.
function renumberWindows() {
    windowRows().forEach((row, index) => {
        row.querySelectorAll('[name]').forEach((field) => {
            field.name = field.name.replace(/windows\[\d+\]/, `windows[${index}]`);
        });
    });
}

// Nowe okno to kopia pierwszego wiersza z pustymi polami (minimum godzin domyślnie 2).
form.querySelector('.add-venue__add-window').addEventListener('click', () => {
    const row = windowRows()[0].cloneNode(true);

    row.querySelectorAll('input, select').forEach((field) => {
        field.value = field.name.endsWith('[minHours]') ? '2' : '';
        field.removeAttribute('aria-invalid');
    });
    row.querySelectorAll('select').forEach((select) => {
        select.selectedIndex = 0;
    });

    windows.append(row);
    renumberWindows();
});

// Ostatniego okna nie usuwamy – lokal potrzebuje co najmniej jednego.
windows.addEventListener('click', (event) => {
    const button = event.target.closest('.window-row__remove');

    if (button && windowRows().length > 1) {
        button.closest('.window-row').remove();
        renumberWindows();
    }
});

// Pinezkę można przeciągnąć, a jej współrzędne trafiają do ukrytych pól formularza.
const mapElement = form.querySelector('.location-map');

if (mapElement && window.mapboxgl) {
    const latitude = form.querySelector('[name="latitude"]');
    const longitude = form.querySelector('[name="longitude"]');
    const position = [Number(longitude.value), Number(latitude.value)];
    const pinColor = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim();

    mapboxgl.accessToken = mapElement.dataset.token;
    const map = new mapboxgl.Map({ container: mapElement, style: mapElement.dataset.style, center: position, zoom: 14 });
    map.addControl(new mapboxgl.NavigationControl({ showCompass: false }));

    const pin = new mapboxgl.Marker({ draggable: true, color: pinColor }).setLngLat(position).addTo(map);
    pin.on('dragend', () => {
        const { lng, lat } = pin.getLngLat();
        longitude.value = lng.toFixed(6);
        latitude.value = lat.toFixed(6);
    });
}
