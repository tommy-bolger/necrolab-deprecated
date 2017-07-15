UPDATE leaderboards
SET release_id = NULL
WHERE character_length(display_name) > 0;