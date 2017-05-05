DROP TABLE IF EXISTS steam_users_latest_daily_rankings;

DROP TABLE IF EXISTS daily_ranking_entries;

DROP TABLE IF EXISTS daily_ranking_season_leaderboard_entries;
DROP TABLE IF EXISTS daily_ranking_season_leaderboards;
DROP TABLE IF EXISTS daily_ranking_season_enrollment;
DROP TABLE IF EXISTS daily_ranking_season_entries;

ALTER TABLE daily_ranking_seasons
DROP CONSTRAINT fk_daily_ranking_seasons_latest_snapshot_id;

DROP TABLE IF EXISTS daily_ranking_season_snapshots;
DROP TABLE IF EXISTS daily_ranking_seasons;

DROP TABLE IF EXISTS leaderboards_temp;

DROP TABLE IF EXISTS leaderboard_entries;

DROP TABLE IF EXISTS power_ranking_entries;