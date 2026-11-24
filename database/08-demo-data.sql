-- Dane demonstracyjne z przykładowych danych aplikacji: konta, lokale z oknami dostępności i wnioski gospodarzy.
-- Wszystkie osoby są fikcyjne. Każde konto demo ma hasło Demo1234 (hash wygenerowany przez password_hash() w PHP).
-- Identyfikatory nadaje baza w kolejności wstawiania, a powiązania wiersze znajdują po e-mailu albo nazwie lokalu.
\set demo_password_hash '$2y$10$PxIyDmL9ZrN28lJcLU6H4uY5ZS1LdbLGPMAXYv/EXmUhVMi.r8CHq'

INSERT INTO users (first_name, last_name, email, password_hash, is_host, is_admin, blocked_at, last_seen_at) VALUES
    ('Administrator', 'PoGodzinach', 'admin@example.com', :'demo_password_hash', FALSE, TRUE, NULL, CURRENT_TIMESTAMP - INTERVAL '1 hour'),
    ('Piotr', 'Nowak', 'piotr.nowak@example.com', :'demo_password_hash', TRUE, FALSE, NULL, CURRENT_TIMESTAMP - INTERVAL '5 hours'),
    ('Tomasz', 'Wrona', 'tomasz.wrona@example.com', :'demo_password_hash', TRUE, FALSE, NULL, CURRENT_TIMESTAMP - INTERVAL '1 day'),
    ('Joanna', 'Wrona', 'joanna.wrona@example.com', :'demo_password_hash', TRUE, FALSE, NULL, CURRENT_TIMESTAMP - INTERVAL '2 days'),
    ('Anna', 'Kowalska', 'anna.kowalska@example.com', :'demo_password_hash', FALSE, FALSE, NULL, CURRENT_TIMESTAMP - INTERVAL '2 hours'),
    ('Paweł', 'Kruk', 'pawel.kruk@example.com', :'demo_password_hash', FALSE, FALSE, NULL, CURRENT_TIMESTAMP - INTERVAL '17 hours'),
    ('Monika', 'Sowa', 'monika.sowa@example.com', :'demo_password_hash', FALSE, FALSE, NULL, CURRENT_TIMESTAMP - INTERVAL '31 hours'),
    ('Marta', 'Zielińska', 'marta.zielinska@example.com', :'demo_password_hash', FALSE, FALSE, NULL, CURRENT_TIMESTAMP - INTERVAL '1 day'),
    ('Tomasz', 'Wójcik', 'tomasz.wojcik@example.com', :'demo_password_hash', FALSE, FALSE, NULL, CURRENT_TIMESTAMP - INTERVAL '29 hours'),
    ('Adam', 'Nowicki', 'adam.nowicki@example.com', :'demo_password_hash', FALSE, FALSE, CURRENT_TIMESTAMP - INTERVAL '10 days', CURRENT_TIMESTAMP - INTERVAL '11 days');

-- Trzy opublikowane lokale (kolejno id 1–3, jak w dotychczasowych adresach) i dwa czekające na akceptację.
INSERT INTO venues (host_id, name, type, street, building_number, postal_code, city, district, latitude, longitude, description, entry_mode, entry_instructions, status, created_at) VALUES
    ((SELECT id FROM users WHERE email = 'piotr.nowak@example.com'), 'Bar Lampion', 'bar', 'ul. Szpitalna', '9', '31-024', 'Kraków', 'Stare Miasto', 50.062300, 19.934300,
        'Bar z lampami nad długim stołem i ceglaną ścianą. Do popołudnia, zanim zaczyna się wieczorna obsługa, sala jest cicha i jasna, a w niedziele można ją wynająć na wyłączność, np. na sesję zdjęciową.',
        'key', 'Klucz odbierzesz w sklepie obok.', 'published', CURRENT_TIMESTAMP - INTERVAL '60 days'),
    ((SELECT id FROM users WHERE email = 'piotr.nowak@example.com'), 'Kawiarnia Ziarno', 'cafe', 'ul. Estery', '8', '31-056', 'Kraków', 'Kazimierz', 50.051200, 19.946600,
        'Kameralna kawiarnia na Kazimierzu z długim dębowym stołem, ceglaną ścianą i oknem na podwórko. Wieczorami, gdy kończymy obsługę gości, oddajemy salę do cichej pracy, a w poniedziałki – gdy kawiarnia jest nieczynna – cały lokal na wyłączność.',
        'staff', NULL, 'published', CURRENT_TIMESTAMP - INTERVAL '45 days'),
    ((SELECT id FROM users WHERE email = 'tomasz.wrona@example.com'), 'Kawiarnia Pod Zegarem', 'cafe', 'ul. Długa', '15', '31-147', 'Kraków', 'Kleparz', 50.066800, 19.942000,
        'Jasna kawiarnia z dużym zegarem nad barem i stołami przy oknach. Od rana do popołudnia jest tu miejsce do cichej pracy i krótkich spotkań.',
        'staff', NULL, 'published', CURRENT_TIMESTAMP - INTERVAL '30 days'),
    ((SELECT id FROM users WHERE email = 'piotr.nowak@example.com'), 'Piwnica Lampion', 'bar', 'ul. Szewska', '14', '31-009', 'Kraków', 'Stare Miasto', 50.062000, 19.934500,
        'Ceglana piwnica pod Barem Lampion z długimi stołami i nagłośnieniem. Od poniedziałku do czwartku, przed wieczornym otwarciem, można ją wynająć na spotkania i kameralne wydarzenia.',
        'staff', NULL, 'pending', CURRENT_TIMESTAMP - INTERVAL '5 hours'),
    ((SELECT id FROM users WHERE email = 'joanna.wrona@example.com'), 'Atelier Len', 'studio', 'ul. Krakowska', '30', '31-062', 'Kraków', 'Kazimierz', 50.048000, 19.942000,
        'Pracownia krawiecka z dużym stołem, manekinami i oknami na dwie strony. W tygodniu miejsca do pracy przy stole, w soboty cała pracownia na sesje zdjęciowe.',
        'key', 'Kod do domofonu dostaniesz po akceptacji rezerwacji.', 'pending', CURRENT_TIMESTAMP - INTERVAL '18 hours');

INSERT INTO venue_purposes (venue_id, purpose)
SELECT venues.id, purpose.value
FROM (VALUES
    ('Bar Lampion', 'work'), ('Bar Lampion', 'meetings'), ('Bar Lampion', 'shoots'),
    ('Kawiarnia Ziarno', 'work'), ('Kawiarnia Ziarno', 'meetings'), ('Kawiarnia Ziarno', 'events'),
    ('Kawiarnia Pod Zegarem', 'work'), ('Kawiarnia Pod Zegarem', 'meetings'),
    ('Piwnica Lampion', 'meetings'), ('Piwnica Lampion', 'events'),
    ('Atelier Len', 'work'), ('Atelier Len', 'shoots')
) AS purpose (venue_name, value)
JOIN venues ON venues.name = purpose.venue_name;

INSERT INTO venue_amenities (venue_id, amenity, note)
SELECT venues.id, amenity.value, amenity.note
FROM (VALUES
    ('Bar Lampion', 'wifi', 'hasło w instrukcji po akceptacji'), ('Bar Lampion', 'sockets', 'przy stołach'),
    ('Bar Lampion', 'daylight', 'dobre światło do zdjęć'),
    ('Kawiarnia Ziarno', 'wifi', 'hasło w instrukcji po akceptacji'), ('Kawiarnia Ziarno', 'sockets', 'przy każdym miejscu'),
    ('Kawiarnia Ziarno', 'projector', 'na spotkania i szkolenia'), ('Kawiarnia Ziarno', 'coffee', 'do dyspozycji najemców'),
    ('Kawiarnia Pod Zegarem', 'wifi', 'hasło w instrukcji po akceptacji'), ('Kawiarnia Pod Zegarem', 'sockets', 'przy oknach'),
    ('Piwnica Lampion', 'wifi', NULL), ('Piwnica Lampion', 'projector', 'ekran na ścianie z cegły'),
    ('Atelier Len', 'wifi', NULL), ('Atelier Len', 'sockets', 'przy dużym stole'), ('Atelier Len', 'daylight', 'okna na dwie strony')
) AS amenity (venue_name, value, note)
JOIN venues ON venues.name = amenity.venue_name;

-- Okna: dni od–do rozpisuje generate_series na osobne wiersze window_days.
INSERT INTO availability_windows (venue_id, mode, start_hour, end_hour, capacity, price)
SELECT venues.id, w.mode, w.start_hour, w.end_hour, w.capacity, w.price
FROM (VALUES
    ('Bar Lampion', 'seats', 9, 15, 10, 15.00),
    ('Bar Lampion', 'exclusive', 10, 16, 25, 160.00),
    ('Kawiarnia Ziarno', 'seats', 18, 22, 12, 15.00),
    ('Kawiarnia Ziarno', 'exclusive', 9, 21, 20, 150.00),
    ('Kawiarnia Pod Zegarem', 'seats', 8, 16, 8, 20.00),
    ('Piwnica Lampion', 'exclusive', 10, 17, 24, 180.00),
    ('Atelier Len', 'seats', 9, 17, 8, 16.00),
    ('Atelier Len', 'exclusive', 10, 18, 12, 140.00)
) AS w (venue_name, mode, start_hour, end_hour, capacity, price)
JOIN venues ON venues.name = w.venue_name;

INSERT INTO window_days (window_id, weekday)
SELECT availability_windows.id, weekday
FROM (VALUES
    ('Bar Lampion', 'seats', 1, 5),
    ('Bar Lampion', 'exclusive', 7, 7),
    ('Kawiarnia Ziarno', 'seats', 2, 5),
    ('Kawiarnia Ziarno', 'exclusive', 1, 1),
    ('Kawiarnia Pod Zegarem', 'seats', 1, 6),
    ('Piwnica Lampion', 'exclusive', 1, 4),
    ('Atelier Len', 'seats', 1, 5),
    ('Atelier Len', 'exclusive', 6, 6)
) AS d (venue_name, mode, first_day, last_day)
JOIN venues ON venues.name = d.venue_name
JOIN availability_windows ON availability_windows.venue_id = venues.id AND availability_windows.mode = d.mode
CROSS JOIN LATERAL generate_series(d.first_day, d.last_day) AS weekday;

INSERT INTO host_applications (user_id, first_name, last_name, phone, company_name, nip, venue_name, venue_type, street, building_number, postal_code, city, message, created_at) VALUES
    ((SELECT id FROM users WHERE email = 'anna.kowalska@example.com'), 'Anna', 'Kowalska', '+48 601 234 567', 'Witraż Anna Kowalska', '6762481035',
        'Pracownia Szkła Witraż', 'workshop', 'ul. Józefińska', '12', '30-529', 'Kraków',
        'Prowadzę pracownię witrażu w Podgórzu. Od poniedziałku do środy po 16:00 pracownia stoi pusta – chcę ją udostępniać na warsztaty, sesje zdjęciowe i spotkania do 10 osób.',
        CURRENT_TIMESTAMP - INTERVAL '2 hours'),
    ((SELECT id FROM users WHERE email = 'pawel.kruk@example.com'), 'Paweł', 'Kruk', '+48 512 880 341', 'Echo Studio sp. z o.o.', '9452217760',
        'Studio Nagrań Echo', 'studio', 'ul. Grzegórzecka', '21', '31-532', 'Kraków',
        'Studio nagrań z salą 40 m². W weekendy do 14:00 stoi wolne – na podcasty, sesje i małe szkolenia.',
        CURRENT_TIMESTAMP - INTERVAL '17 hours'),
    ((SELECT id FROM users WHERE email = 'monika.sowa@example.com'), 'Monika', 'Sowa', '+48 698 117 402', 'Księgarnia Strona s.c.', '6772395108',
        'Księgarnia Strona', 'bookstore', 'ul. Miodowa', '9', '31-055', 'Kraków',
        'Po 19:00 księgarnia jest zamknięta. Chcemy wynajmować antresolę na spotkania autorskie i kluby książki.',
        CURRENT_TIMESTAMP - INTERVAL '31 hours');
