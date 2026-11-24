-- Funkcja wyzwalacza: przy każdej zmianie wiersza ustawia updated_at na bieżący czas,
-- więc zapytania UPDATE w repozytoriach nie muszą o tym pamiętać.
CREATE FUNCTION set_updated_at() RETURNS trigger AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER reservations_set_updated_at
    BEFORE UPDATE ON reservations
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
