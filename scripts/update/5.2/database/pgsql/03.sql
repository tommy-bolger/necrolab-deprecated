INSERT INTO characters (name, display_name, is_active, is_weighted, sort_order) 
VALUES ('nocturna', 'Nocturna', 1, 0, 11);

UPDATE characters SET sort_order = 12 WHERE character_id = 15;
UPDATE characters SET sort_order = 13 WHERE character_id = 14;