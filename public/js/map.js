/*
Mapa w widoku Odkrywaj (Mapbox GL JS). Pinezki i przyciski są już w HTML – skrypt tylko przenosi je na mapę.
Gdy nie ma tokenu, biblioteka się nie wczyta albo przeglądarka nie obsługuje WebGL, zostaje statyczna ilustracja.
*/
const mapSection = document.querySelector('.map');
const mapCanvas = mapSection?.querySelector('.map__canvas') ?? null;
const pins = [...(mapSection?.querySelectorAll('.map-pin[data-lng]') ?? [])];
const reels = [...document.querySelectorAll('.reel[data-venue-id]')];

/* Odstęp od krawędzi przy dopasowaniu widoku: u góry przycisk wyszukiwania, po prawej przyciski, na dole legenda. */
const fitPadding = { top: 80, right: 80, bottom: 130, left: 40 };

function pinPosition(pin) {
    return [Number(pin.dataset.lng), Number(pin.dataset.lat)];
}

function venueBounds() {
    const bounds = new mapboxgl.LngLatBounds();
    pins.forEach((pin) => bounds.extend(pinPosition(pin)));

    return bounds;
}

function createMap() {
    mapboxgl.accessToken = mapCanvas.dataset.token;

    return new mapboxgl.Map({
        container: mapCanvas,
        style: mapCanvas.dataset.style,
        bounds: venueBounds(),
        fitBoundsOptions: { padding: fitPadding },
    });
}

/* Każda pinezka z HTML trafia do znacznika Mapboxa, więc na mapie wygląda tak samo jak na ilustracji. */
function addMarkers(map) {
    const markers = new Map();

    pins.forEach((pin) => {
        const element = document.createElement('div');
        element.className = 'map__marker';
        element.append(pin);

        new mapboxgl.Marker({ element, anchor: 'bottom', offset: [0, -7] })
            .setLngLat(pinPosition(pin))
            .addTo(map);

        // Mapbox oznacza znacznik jako obrazek (role="img"), co ukryłoby link pinezki przed czytnikiem ekranu.
        element.removeAttribute('role');
        element.removeAttribute('aria-label');

        markers.set(pin.dataset.venueId, element);
    });

    return markers;
}

function selectVenue(map, markers, venueId) {
    pins.forEach((pin) => {
        const isSelected = pin.dataset.venueId === venueId;

        pin.classList.toggle('map-pin--selected', isSelected);
        markers.get(pin.dataset.venueId)?.classList.toggle('map__marker--selected', isSelected);

        if (isSelected) {
            pin.setAttribute('aria-current', 'true');
            map.easeTo({ center: pinPosition(pin) });
        } else {
            pin.removeAttribute('aria-current');
        }
    });
}

/* „Mapa podąża za Tobą”: rolka widoczna co najmniej w 60% wybiera swoją pinezkę i przesuwa do niej mapę. */
function followReels(map, markers) {
    const observer = new IntersectionObserver((entries) => {
        entries
            .filter((entry) => entry.isIntersecting)
            .forEach((entry) => selectVenue(map, markers, entry.target.dataset.venueId));
    }, { threshold: 0.6 });

    reels.forEach((reel) => observer.observe(reel));
}

function showUserLocation(map) {
    navigator.geolocation?.getCurrentPosition(
        (position) => {
            const lngLat = [position.coords.longitude, position.coords.latitude];
            const dot = document.createElement('div');
            dot.className = 'map__me';

            new mapboxgl.Marker({ element: dot }).setLngLat(lngLat).addTo(map);
            dot.setAttribute('aria-label', 'Twoja lokalizacja');
            map.flyTo({ center: lngLat, zoom: 14 });
        },
        () => {
            // Brak zgody na lokalizację – mapa zostaje w tym samym miejscu.
        },
    );
}

function connectControls(map) {
    const actions = {
        'zoom-in': () => map.zoomIn(),
        'zoom-out': () => map.zoomOut(),
        locate: () => showUserLocation(map),
    };

    mapSection.querySelectorAll('[data-map-action]').forEach((button) => {
        button.addEventListener('click', actions[button.dataset.mapAction]);
    });
}

/* Na telefonie mapa jest warstwą ukrytą do kliknięcia „Mapa”, więc po pokazaniu trzeba przeliczyć jej rozmiar i widok. */
function resizeWithContainer(map) {
    let wasVisible = false;

    new ResizeObserver(([entry]) => {
        const isVisible = entry.contentRect.width > 0;

        map.resize();

        if (isVisible && !wasVisible) {
            map.fitBounds(venueBounds(), { padding: fitPadding, duration: 0 });
        }

        wasVisible = isVisible;
    }).observe(mapCanvas);
}

function startMap() {
    if (!mapCanvas || !window.mapboxgl || pins.length === 0) {
        return;
    }

    let map;

    try {
        map = createMap();
    } catch (error) {
        return;
    }

    map.on('load', () => {
        mapSection.classList.add('map--live');

        const markers = addMarkers(map);
        connectControls(map);
        followReels(map, markers);
    });

    resizeWithContainer(map);
}

startMap();
