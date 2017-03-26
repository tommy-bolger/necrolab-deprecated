INSERT INTO characters (name, display_name, is_active, is_weighted, sort_order) 
VALUES ('diamond', 'Diamond', 1, 0, 12);

UPDATE characters SET sort_order = 13 WHERE character_id = 15;
UPDATE characters SET sort_order = 14 WHERE character_id = 14;
