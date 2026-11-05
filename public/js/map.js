/* Mapa w widoku Odkrywaj (Mapbox GL JS): pinezki z ceną lokali, które podąża za przewijanymi kartami. */
const mapCanvas = document.querySelector('.map__canvas');
const venueCards = document.querySelectorAll('.venue-card');

function createPin(map, venue) {
    const pin = document.createElement('a');
    pin.className = 'map-pin';
    pin.href = `/venues/${venue.id}`;
    pin.textContent = venue.price;

    new mapboxgl.Marker({ element: pin, anchor: 'bottom', offset: [0, -6] })
        .setLngLat([venue.lng, venue.lat])
        .addTo(map);

    // Mapbox nadaje znacznikowi role="img", co ukryłoby link przed czytnikiem ekranu.
    pin.removeAttribute('role');
    pin.setAttribute('aria-label', `${venue.name}, ${venue.price}`);

    return pin;
}

function fitToVenues(map, venues) {
    if (venues.length === 0) {
        return;
    }

    const bounds = new mapboxgl.LngLatBounds();
    venues.forEach((venue) => bounds.extend([venue.lng, venue.lat]));
    map.fitBounds(bounds, { padding: 80, maxZoom: 15, duration: 0 });
}

/* Karta widoczna co najmniej w 60% wyróżnia pinezkę swojego lokalu i przesuwa do niej mapę. */
function followCards(map, pins, venues) {
    const observer = new IntersectionObserver((entries) => {
        entries.filter((entry) => entry.isIntersecting).forEach((entry) => {
            const id = Number(entry.target.dataset.venueId);
            const venue = venues.find((item) => item.id === id);

            pins.forEach((pin, pinId) => pin.classList.toggle('map-pin--active', pinId === id));
            map.easeTo({ center: [venue.lng, venue.lat] });
        });
    }, { threshold: 0.6 });

    venueCards.forEach((card) => observer.observe(card));
}

function startMap() {
    const venues = JSON.parse(mapCanvas.dataset.venues);

    mapboxgl.accessToken = mapCanvas.dataset.token;
    const map = new mapboxgl.Map({ container: mapCanvas, style: mapCanvas.dataset.style, center: [19.94, 50.06], zoom: 13 });

    map.addControl(new mapboxgl.NavigationControl({ showCompass: false }));
    map.addControl(new mapboxgl.GeolocateControl());

    map.on('load', () => {
        const pins = new Map(venues.map((venue) => [venue.id, createPin(map, venue)]));
        fitToVenues(map, venues);
        followCards(map, pins, venues);
    });

    // Na telefonie mapa jest ukryta do kliknięcia „Mapa”, więc po pokazaniu trzeba przeliczyć jej rozmiar.
    new ResizeObserver(() => {
        map.resize();
        fitToVenues(map, venues);
    }).observe(mapCanvas);
}

if (mapCanvas && window.mapboxgl) {
    startMap();
}
