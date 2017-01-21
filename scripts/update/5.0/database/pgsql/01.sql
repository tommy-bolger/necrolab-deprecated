ALTER TABLE characters
    ADD COLUMN sort_order smallint;

UPDATE characters
SET sort_order = 1
WHERE character_id = 1;

UPDATE characters
SET sort_order = 2
WHERE character_id = 2;

UPDATE characters
SET sort_order = 3
WHERE character_id = 3;

UPDATE characters
SET sort_order = 4
WHERE character_id = 4;

UPDATE characters
SET sort_order = 5
WHERE character_id = 5;

UPDATE characters
SET sort_order = 6
WHERE character_id = 6;

UPDATE characters
SET sort_order = 7
WHERE character_id = 7;

UPDATE characters
SET sort_order = 8
WHERE character_id = 8;

UPDATE characters
SET sort_order = 9
WHERE character_id = 9;

UPDATE characters
SET sort_order = 10
WHERE character_id = 10;

UPDATE characters
SET sort_order = 11
WHERE character_id = 15;

UPDATE characters
SET sort_order = 12
WHERE character_id = 14;