UPDATE leaderboards
SET is_power_ranking = 1
WHERE leaderboard_id IN (
    SELECT l.leaderboard_id
    FROM leaderboards l
    JOIN characters c ON c.character_id = l.character_id
    WHERE is_seeded = 1
        AND c.is_active = 1
        AND l.name NOT ILIKE '%custom%'
        AND l.name NOT ILIKE '%co_op%'
)