ALTER TABLE leaderboards 
DROP COLUMN last_snapshot_id;

ALTER TABLE power_rankings 
DROP COLUMN latest;

ALTER TABLE power_rankings
ALTER COLUMN power_ranking_id TYPE smallint;

DROP TABLE power_ranking_leaderboard_snapshots;

ALTER TABLE steam_users
ALTER COLUMN steamid TYPE bigint USING (steamid::bigint);

ALTER TABLE steam_users
ALTER COLUMN lastlogoff TYPE integer USING (lastlogoff::integer);

ALTER TABLE steam_users
ALTER COLUMN primaryclanid TYPE bigint USING (primaryclanid::bigint);

ALTER TABLE steam_users
ALTER COLUMN timecreated TYPE integer USING (timecreated::integer);

ALTER TABLE steam_users
ALTER COLUMN loccountrycode TYPE character varying(3);

ALTER TABLE steam_users
ALTER COLUMN locstatecode TYPE character varying(3);

ALTER TABLE steam_users 
DROP COLUMN latest_power_ranking_entry_id;

ALTER TABLE steam_users 
DROP COLUMN latest_daily_ranking_entry_id;

ALTER TABLE steam_users
ALTER COLUMN updated SET NOT NULL;

ALTER TABLE daily_rankings 
DROP COLUMN latest;

ALTER TABLE daily_rankings
ALTER COLUMN daily_ranking_id TYPE smallint;

ALTER TABLE daily_rankings
ALTER COLUMN daily_ranking_day_type_id SET NOT NULL;

DROP TABLE daily_ranking_leaderboard_snapshots;

ALTER TABLE daily_ranking_day_types
ALTER COLUMN daily_ranking_day_type_id TYPE smallint;

ALTER TABLE daily_rankings
ALTER COLUMN daily_ranking_day_type_id TYPE smallint;