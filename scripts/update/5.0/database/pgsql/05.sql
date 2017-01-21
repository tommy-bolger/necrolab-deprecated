ALTER TABLE leaderboards
  ADD COLUMN is_power_ranking smallint NOT NULL DEFAULT 0;
ALTER TABLE leaderboards
  ADD COLUMN is_daily_ranking smallint NOT NULL DEFAULT 0;
  
UPDATE leaderboards l
SET is_daily_ranking = 0;
  
UPDATE leaderboards l
SET is_daily_ranking = 1
WHERE (
        SELECT leaderboards_blacklist_id
        FROM leaderboards_blacklist
        WHERE leaderboard_id = l.leaderboard_id
) IS NULL
    AND l.is_daily = 1
    AND l.daily_date >= '2015-04-23'
    AND l.character_id = 1
    AND l.is_power_ranking = 0
    AND l.is_prod = 1
    AND l.is_dev = 0
    AND l.is_co_op = 0
    AND l.is_story_mode = 0;