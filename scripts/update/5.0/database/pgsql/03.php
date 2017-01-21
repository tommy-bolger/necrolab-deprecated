<?php
//Add dev leaderboards into blacklist
db()->query("
    INSERT INTO leaderboards_blacklist (leaderboard_id)
    SELECT leaderboard_id
    FROM leaderboards
    WHERE is_dev = 1
");

//Add deathless speed to blacklist
db()->query("
    INSERT INTO leaderboards_blacklist (leaderboard_id)
    SELECT leaderboard_id
    FROM leaderboards
    WHERE is_deathless = 1
        AND is_speedrun = 1
");

//Add deathless story mode to blacklist
db()->query("
    INSERT INTO leaderboards_blacklist (leaderboard_id)
    SELECT leaderboard_id
    FROM leaderboards
    WHERE is_deathless = 1
        AND is_story_mode = 1
");

//Add deathless all character mode to blacklist
db()->query("
    INSERT INTO leaderboards_blacklist (leaderboard_id)
    SELECT leaderboard_id
    FROM leaderboards
    WHERE is_deathless = 1
        AND is_all_character = 1
");

//Add unsupported character leaderboards to blacklist
db()->query("
    INSERT INTO leaderboards_blacklist (leaderboard_id)
    SELECT l.leaderboard_id
    FROM leaderboards l
    JOIN characters c ON c.character_id = l.character_id
    WHERE c.name IN ('ghost', 'pacifist', 'thief')
");

//Add prerelease leaderboards to blacklist
db()->query("
    INSERT INTO leaderboards_blacklist (leaderboard_id)
    SELECT leaderboard_id
    FROM leaderboards
    WHERE is_prod = 0
        AND is_dev = 0
");