-- Laboratorium 7: prośby o rezerwację lokalu (zamiast tabeli projects z instrukcji).
-- venue_id dostanie klucz obcy, gdy lokale trafią do bazy (Laboratorium 8), a user_id – z kontami (Laboratorium 9).
CREATE TABLE reservations (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    venue_id BIGINT NOT NULL,
    mode VARCHAR(20) NOT NULL,
    purpose VARCHAR(20) NOT NULL,
    reservation_date DATE NOT NULL,
    start_hour SMALLINT NOT NULL,
    end_hour SMALLINT NOT NULL,
    seats SMALLINT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT reservations_mode_check
        CHECK (mode IN ('seats', 'exclusive')),
    CONSTRAINT reservations_purpose_check
        CHECK (purpose IN ('work', 'meetings', 'shoots', 'events')),
    CONSTRAINT reservations_hours_check
        CHECK (start_hour >= 0 AND end_hour <= 24 AND start_hour < end_hour),
    CONSTRAINT reservations_status_check
        CHECK (status IN ('pending', 'accepted', 'rejected', 'cancelled'))
);

CREATE INDEX reservations_status_idx ON reservations (status);
