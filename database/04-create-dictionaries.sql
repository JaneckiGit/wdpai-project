-- Słowniki: stałe listy wartości, na które wskazują inne tabele kluczem obcym (zamiast powtarzać listę w CHECK).
-- Kluczem głównym jest kod używany w aplikacji (np. 'cafe'), a nazwa to etykieta pokazywana użytkownikowi.
-- Tryby rezerwacji i statusy zostają w CHECK, bo zależy od nich logika w kodzie, a nie tylko lista wartości.

CREATE TABLE venue_types (
    code VARCHAR(20) PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
);

-- Cele, na które gospodarz pozwala w lokalu i które najemca wybiera przy rezerwacji.
CREATE TABLE purposes (
    code VARCHAR(20) PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
);

CREATE TABLE amenities (
    code VARCHAR(20) PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
);

INSERT INTO venue_types (code, name) VALUES
    ('cafe', 'Kawiarnia'),
    ('bar', 'Bar'),
    ('restaurant', 'Restauracja'),
    ('workshop', 'Pracownia'),
    ('studio', 'Studio'),
    ('bookstore', 'Księgarnia z kawiarnią'),
    ('gallery', 'Galeria');

INSERT INTO purposes (code, name) VALUES
    ('work', 'Praca'),
    ('meetings', 'Spotkania i szkolenia'),
    ('shoots', 'Sesje foto i wideo'),
    ('events', 'Kameralne wydarzenia');

INSERT INTO amenities (code, name) VALUES
    ('wifi', 'Wi-Fi'),
    ('sockets', 'Gniazdka'),
    ('daylight', 'Duże okno'),
    ('projector', 'Rzutnik'),
    ('coffee', 'Ekspres do kawy');
