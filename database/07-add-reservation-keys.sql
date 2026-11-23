-- Laboratorium 8: lokale i konta są już w bazie, więc rezerwacja dostaje klucze obce do obu tabel.
-- ON DELETE CASCADE: rezerwacja nie ma sensu bez lokalu ani bez konta najemcy (jak okna i cele lokalu).
ALTER TABLE reservations ADD COLUMN user_id BIGINT NULL;

-- Prośby zapisane przed tym skryptem należą do konta demo, z którego aplikacja rezerwuje do czasu logowania (Laboratorium 9).
UPDATE reservations
SET user_id = (SELECT id FROM users WHERE email = 'piotr.nowak@example.com')
WHERE user_id IS NULL;

ALTER TABLE reservations
    ALTER COLUMN user_id SET NOT NULL,
    ADD CONSTRAINT reservations_user_id_fkey
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    ADD CONSTRAINT reservations_venue_id_fkey
        FOREIGN KEY (venue_id) REFERENCES venues (id) ON DELETE CASCADE;

-- Indeksy pod „Moje rezerwacje” (po najemcy) i prośby w lokalach gospodarza (JOIN po lokalu).
CREATE INDEX reservations_user_id_idx ON reservations (user_id);
CREATE INDEX reservations_venue_id_idx ON reservations (venue_id);
