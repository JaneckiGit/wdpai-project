-- Firmy gospodarzy: NIP jednoznacznie wskazuje firmę, więc jej nazwa zależy od NIP-u, a nie od wniosku.
-- Trzymanie nazwy we wniosku byłoby zależnością przechodnią (wniosek → NIP → nazwa firmy), dlatego firma ma własną tabelę.
CREATE TABLE companies (
    nip CHAR(10) PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    CONSTRAINT companies_nip_check
        CHECK (nip ~ '^[0-9]{10}$')
);

-- Wnioski o rolę gospodarza z formularza „Zostań gospodarzem”; decyzję podejmuje administrator.
-- Imię i nazwisko wnioskodawcy są na jego koncie (users), więc wniosek wskazuje tylko konto.
-- ON DELETE CASCADE: wniosek nie ma sensu bez konta, które go złożyło.
-- ON DELETE RESTRICT: firmy, na którą wskazuje wniosek, nie da się usunąć; ON UPDATE CASCADE: poprawiony NIP zmienia się też we wnioskach.
CREATE TABLE host_applications (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    phone VARCHAR(20) NOT NULL,
    company_nip CHAR(10) NOT NULL REFERENCES companies (nip) ON DELETE RESTRICT ON UPDATE CASCADE,
    venue_name VARCHAR(120) NOT NULL,
    venue_type VARCHAR(20) NOT NULL REFERENCES venue_types (code) ON DELETE RESTRICT ON UPDATE CASCADE,
    street VARCHAR(120) NOT NULL,
    building_number VARCHAR(10) NOT NULL,
    unit_number VARCHAR(10) NULL,
    postal_code CHAR(6) NOT NULL,
    city VARCHAR(80) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    decided_at TIMESTAMPTZ NULL,
    CONSTRAINT host_applications_postal_code_check
        CHECK (postal_code ~ '^[0-9]{2}-[0-9]{3}$'),
    CONSTRAINT host_applications_status_check
        CHECK (status IN ('pending', 'approved', 'rejected'))
);

CREATE INDEX host_applications_company_nip_idx ON host_applications (company_nip);

-- Jeden oczekujący wniosek na konto – kolejny można złożyć dopiero po decyzji administratora.
CREATE UNIQUE INDEX host_applications_one_pending
    ON host_applications (user_id)
    WHERE status = 'pending';
