-- Konta użytkowników. Kolumny i unikalny indeks e-maila pochodzą z Laboratorium 9 (logowanie dojdzie tam) z jedną zmianą:
-- name z instrukcji jest rozbite na first_name i last_name, bo każda kolumna ma przechowywać daną atomową.
-- Dalsze kolumny opisują role i stan konta w PoGodzinach.
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    first_name VARCHAR(60) NOT NULL,
    last_name VARCHAR(60) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    -- Każde konto jest najemcą; gospodarzem zostaje po zatwierdzeniu wniosku przez administratora.
    is_host BOOLEAN NOT NULL DEFAULT FALSE,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE,
    -- NULL oznacza konto aktywne, data – zablokowane od tej chwili.
    blocked_at TIMESTAMPTZ NULL,
    last_seen_at TIMESTAMPTZ NULL
);

CREATE UNIQUE INDEX users_email_unique
    ON users (LOWER(email));
