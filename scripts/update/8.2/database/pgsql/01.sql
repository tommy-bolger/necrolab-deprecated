INSERT INTO releases (name, display_name, start_date, end_date, win_zone, win_level)
VALUES ('amplified_dlc_early_access', 'Amplified DLC Early Access', '2017-01-24', '2017-07-11', 5, 6);

UPDATE releases
SET start_date = '2017-07-12'
WHERE name = 'amplified_dlc';

UPDATE leaderboards
SET release_id = (
    SELECT release_id
    FROM releases
    WHERE name = 'amplified_dlc_early_access'
)
WHERE character_length(display_name) > 0;