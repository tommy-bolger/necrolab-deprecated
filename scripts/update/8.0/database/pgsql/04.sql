ALTER TABLE power_rankings
ADD COLUMN seeded smallint;

UPDATE power_rankings
SET seeded = 0;

ALTER TABLE power_rankings
ALTER COLUMN seeded SET NOT NULL;

ALTER TABLE power_rankings
DROP CONSTRAINT uq_power_ranking_record;

ALTER TABLE power_rankings
ADD CONSTRAINT uq_power_ranking_record UNIQUE (date, release_id, mode_id, seeded);