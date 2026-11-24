-- Rezerwacja należy do konta najemcy: „Moje rezerwacje” pokazuje tylko jego prośby, a gospodarz widzi, kto rezerwuje.
-- ON DELETE CASCADE: rezerwacja nie ma sensu bez konta najemcy (jak bez lokalu – skrypt 07).
ALTER TABLE reservations ADD COLUMN user_id BIGINT NULL;

-- Prośby zapisane przed tym skryptem należą do konta demo, z którego aplikacja rezerwuje do czasu logowania (Laboratorium 9).
UPDATE reservations
SET user_id = (SELECT id FROM users WHERE email = 'piotr.nowak@example.com')
WHERE user_id IS NULL;

ALTER TABLE reservations
    ALTER COLUMN user_id SET NOT NULL,
    ADD CONSTRAINT reservations_user_id_fkey
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

-- Indeks pod „Moje rezerwacje” (rezerwacje jednego najemcy).
CREATE INDEX reservations_user_id_idx ON reservations (user_id);
