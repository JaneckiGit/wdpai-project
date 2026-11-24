-- Kolejność pozycji słowników na stronach (np. cele: praca, spotkania, sesje, wydarzenia – jak w prototypie).
-- Kolejność to też dana, więc zapisujemy ją w bazie, zamiast sortować po nazwie albo trzymać listę kodów w PHP.
ALTER TABLE venue_types ADD COLUMN sort_order SMALLINT;
ALTER TABLE purposes ADD COLUMN sort_order SMALLINT;
ALTER TABLE amenities ADD COLUMN sort_order SMALLINT;

UPDATE venue_types
SET sort_order = list.sort_order
FROM (VALUES ('cafe', 1), ('bar', 2), ('restaurant', 3), ('workshop', 4), ('studio', 5), ('bookstore', 6), ('gallery', 7))
    AS list (code, sort_order)
WHERE venue_types.code = list.code;

UPDATE purposes
SET sort_order = list.sort_order
FROM (VALUES ('work', 1), ('meetings', 2), ('shoots', 3), ('events', 4)) AS list (code, sort_order)
WHERE purposes.code = list.code;

UPDATE amenities
SET sort_order = list.sort_order
FROM (VALUES ('wifi', 1), ('sockets', 2), ('daylight', 3), ('projector', 4), ('coffee', 5)) AS list (code, sort_order)
WHERE amenities.code = list.code;

ALTER TABLE venue_types ALTER COLUMN sort_order SET NOT NULL;
ALTER TABLE purposes ALTER COLUMN sort_order SET NOT NULL;
ALTER TABLE amenities ALTER COLUMN sort_order SET NOT NULL;
