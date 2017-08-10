DELETE FROM achievements
WHERE achievement_id >= 31;

VACUUM ANALYZE achievements;
VACUUM ANALYZE steam_user_achievements;