ALTER TABLE leaderboard_snapshots
ADD COLUMN [date] DATE;

ALTER TABLE leaderboard_snapshots
ADD COLUMN updated TIMESTAMP;

ALTER TABLE power_rankings
ADD COLUMN [date] DATE;

ALTER TABLE power_rankings
ADD COLUMN updated TIMESTAMP;

ALTER TABLE daily_rankings
ADD COLUMN [date] DATE;

ALTER TABLE daily_rankings
ADD COLUMN updated TIMESTAMP;