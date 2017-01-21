INSERT INTO characters (name, display_name, is_active, is_weighted)
VALUES ('story', 'Cadence, Melody, and Aria', 1, 0);

UPDATE leaderboards
SET character_id = 15
WHERE is_story_mode = 1;