ALTER TABLE leaderboards
ALTER COLUMN leaderboard_id TYPE smallint;

ALTER TABLE leaderboard_snapshots
ALTER COLUMN leaderboard_id TYPE smallint;

ALTER TABLE leaderboards_blacklist
ALTER COLUMN leaderboard_id TYPE smallint;