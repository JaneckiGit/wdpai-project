-- Wnioski o rolę gospodarza z formularza „Zostań gospodarzem”; decyzję podejmuje administrator.
-- ON DELETE CASCADE: wniosek nie ma sensu bez konta, które go złożyło.
CREATE TABLE host_applications (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    first_name VARCHAR(60) NOT NULL,
    last_name VARCHAR(60) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    company_name VARCHAR(120) NOT NULL,
    nip CHAR(10) NOT NULL,
    venue_name VARCHAR(120) NOT NULL,
    venue_type VARCHAR(20) NOT NULL REFERENCES venue_types (code) ON DELETE RESTRICT ON UPDATE CASCADE,
    address VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    decided_at TIMESTAMPTZ NULL,
    CONSTRAINT host_applications_nip_check
        CHECK (nip ~ '^[0-9]{10}$'),
    CONSTRAINT host_applications_status_check
        CHECK (status IN ('pending', 'approved', 'rejected'))
);

-- Jeden oczekujący wniosek na konto – kolejny można złożyć dopiero po decyzji administratora.
CREATE UNIQUE INDEX host_applications_one_pending
    ON host_applications (user_id)
    WHERE status = 'pending';
