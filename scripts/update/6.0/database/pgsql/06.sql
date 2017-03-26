ALTER TABLE leaderboards
ADD COLUMN is_hard_mode smallint NOT NULL DEFAULT 0;

ALTER TABLE leaderboards
ADD COLUMN is_no_return smallint NOT NULL DEFAULT 0;