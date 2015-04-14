ALTER TABLE leaderboards
  ADD COLUMN is_dev smallint NOT NULL DEFAULT 0;
  
UPDATE leaderboards
SET is_dev = 1
WHERE name LIKE '%DEV%';