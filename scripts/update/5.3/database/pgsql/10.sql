ALTER TABLE releases
ADD COLUMN win_zone smallint;

ALTER TABLE releases
ADD COLUMN win_level smallint;

UPDATE releases
SET
    win_zone = 3,
    win_level = 5
WHERE name = 'early_access';

UPDATE releases
SET
    win_zone = 4,
    win_level = 6
WHERE name = 'original_release';

UPDATE releases
SET
    win_zone = 5,
    win_level = 6
WHERE name = 'amplified_dlc_early_access';

UPDATE releases
SET
    win_zone = 5,
    win_level = 6
WHERE name = 'amplified_dlc';