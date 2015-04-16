SELECT pg_catalog.setval('characters_seq', 14, false);

INSERT INTO characters (name, display_name, is_active, is_weighted)
VALUES ('all', 'All', 1, 0);

UPDATE leaderboards
SET character_id = 14
WHERE character_id = 1 
    AND name LIKE '%All Chars%';