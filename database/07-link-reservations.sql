-- Tabela rezerwacji z Laboratorium 7 (01) powstała, zanim w bazie były lokale i słownik celów.
-- Teraz oba istnieją, więc rezerwacja wskazuje na nie kluczami obcymi zamiast przechowywać „luźne” wartości.
-- Najemca (user_id) dojdzie razem z kodem, który zapisuje rezerwację na konto.

-- ON DELETE CASCADE: rezerwacja nie ma sensu bez lokalu (jak okna i cele lokalu).
ALTER TABLE reservations
    ADD CONSTRAINT reservations_venue_id_fkey
        FOREIGN KEY (venue_id) REFERENCES venues (id) ON DELETE CASCADE;

-- Słownik celów zastępuje listę w CHECK: lista wartości jest teraz w jednym miejscu dla lokali i rezerwacji.
ALTER TABLE reservations
    DROP CONSTRAINT reservations_purpose_check,
    ADD CONSTRAINT reservations_purpose_fkey
        FOREIGN KEY (purpose) REFERENCES purposes (code) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Indeks pod prośby w lokalach gospodarza (JOIN rezerwacji z lokalami po venue_id).
CREATE INDEX reservations_venue_id_idx ON reservations (venue_id);
