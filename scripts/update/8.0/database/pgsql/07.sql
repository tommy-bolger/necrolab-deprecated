DELETE FROM releases 
WHERE name = 'amplified_dlc';

UPDATE releases 
SET 
    name = 'amplified_dlc',
    display_name = 'Amplified DLC'
WHERE name = 'amplified_dlc_early_access';

UPDATE releases 
SET 
    name = 'original',
    display_name = 'Original'
WHERE name = 'original_release';