-- Lokale gospodarzy oraz tabele, które bez lokalu nie mają sensu (cele, udogodnienia, okna dostępności).
-- ON DELETE CASCADE: usunięcie konta gospodarza usuwa jego lokale, a usunięcie lokalu – jego cele, udogodnienia i okna.
CREATE TABLE venues (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    host_id BIGINT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    name VARCHAR(120) NOT NULL,
    -- Szkic wymaga tylko nazwy, dlatego pozostałe dane mogą być puste do wysłania do weryfikacji.
    type VARCHAR(20) NULL,
    address VARCHAR(255) NULL,
    district VARCHAR(80) NULL,
    latitude NUMERIC(9, 6) NULL,
    longitude NUMERIC(9, 6) NULL,
    description TEXT NULL,
    entry_mode VARCHAR(10) NOT NULL DEFAULT 'staff',
    entry_instructions TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT venues_type_check
        CHECK (type IN ('cafe', 'bar', 'restaurant', 'workshop', 'studio', 'bookstore', 'gallery')),
    CONSTRAINT venues_entry_mode_check
        CHECK (entry_mode IN ('staff', 'key')),
    CONSTRAINT venues_status_check
        CHECK (status IN ('draft', 'pending', 'published', 'rejected')),
    -- Lokal wysłany do weryfikacji (albo już oceniony) musi mieć komplet danych z formularza „Dodaj lokal”.
    -- Dzielnica jest nieobowiązkowa – formularz jej nie zbiera, mają ją tylko lokale z danych demonstracyjnych.
    CONSTRAINT venues_complete_check
        CHECK (status = 'draft' OR (type IS NOT NULL AND address IS NOT NULL
            AND latitude IS NOT NULL AND longitude IS NOT NULL AND description IS NOT NULL)),
    CONSTRAINT venues_coordinates_check
        CHECK (latitude BETWEEN -90 AND 90 AND longitude BETWEEN -180 AND 180)
);

CREATE INDEX venues_host_id_idx ON venues (host_id);
CREATE INDEX venues_status_idx ON venues (status);

CREATE TRIGGER venues_set_updated_at
    BEFORE UPDATE ON venues
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- Cele, na które gospodarz pozwala (praca, spotkania, sesje, wydarzenia).
CREATE TABLE venue_purposes (
    venue_id BIGINT NOT NULL REFERENCES venues (id) ON DELETE CASCADE,
    purpose VARCHAR(20) NOT NULL,
    PRIMARY KEY (venue_id, purpose),
    CONSTRAINT venue_purposes_purpose_check
        CHECK (purpose IN ('work', 'meetings', 'shoots', 'events'))
);

-- Udogodnienia ze stałej listy (etykiety i ikony są w widoku, jak przy celach).
CREATE TABLE venue_amenities (
    venue_id BIGINT NOT NULL REFERENCES venues (id) ON DELETE CASCADE,
    amenity VARCHAR(20) NOT NULL,
    PRIMARY KEY (venue_id, amenity),
    CONSTRAINT venue_amenities_amenity_check
        CHECK (amenity IN ('wifi', 'sockets', 'daylight', 'projector', 'coffee'))
);

-- Okno dostępności: tryb, godziny, liczba miejsc (albo osób przy wyłączności) i cena za godzinę.
CREATE TABLE availability_windows (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    venue_id BIGINT NOT NULL REFERENCES venues (id) ON DELETE CASCADE,
    mode VARCHAR(20) NOT NULL,
    start_hour SMALLINT NOT NULL,
    end_hour SMALLINT NOT NULL,
    capacity SMALLINT NOT NULL,
    price NUMERIC(8, 2) NOT NULL,
    min_hours SMALLINT NOT NULL DEFAULT 2,
    CONSTRAINT availability_windows_mode_check
        CHECK (mode IN ('seats', 'exclusive')),
    CONSTRAINT availability_windows_hours_check
        CHECK (start_hour >= 0 AND end_hour <= 24 AND start_hour < end_hour),
    CONSTRAINT availability_windows_capacity_check
        CHECK (capacity > 0),
    CONSTRAINT availability_windows_price_check
        CHECK (price > 0),
    CONSTRAINT availability_windows_min_hours_check
        CHECK (min_hours > 0 AND min_hours <= end_hour - start_hour)
);

CREATE INDEX availability_windows_venue_id_idx ON availability_windows (venue_id);

-- Dni tygodnia okna (1 = poniedziałek … 7 = niedziela) – osobne wiersze zamiast listy w jednej kolumnie.
CREATE TABLE window_days (
    window_id BIGINT NOT NULL REFERENCES availability_windows (id) ON DELETE CASCADE,
    weekday SMALLINT NOT NULL,
    PRIMARY KEY (window_id, weekday),
    CONSTRAINT window_days_weekday_check
        CHECK (weekday BETWEEN 1 AND 7)
);
