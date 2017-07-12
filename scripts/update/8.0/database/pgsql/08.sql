INSERT INTO characters (name, display_name, is_active, is_weighted, sort_order)
VALUES ('mary', 'Mary', 1, 0, 13);

INSERT INTO characters (name, display_name, is_active, is_weighted, sort_order)
VALUES ('tempo', 'Tempo', 1, 0, 14);

INSERT INTO characters (name, display_name, is_active, is_weighted, sort_order)
VALUES ('all_dlc', 'All Chars DLC', 1, 0, 17);

UPDATE characters
SET display_name = 'Story'
WHERE name = 'story';

UPDATE characters
SET sort_order = 15
WHERE name = 'story';

UPDATE characters
SET sort_order = 16
WHERE name = 'all';