ALTER TABLE steam_replays
ALTER COLUMN steam_replay_id DROP DEFAULT;

ALTER TABLE steam_user_pbs
ALTER COLUMN steam_user_pb_id DROP DEFAULT;

ALTER TABLE steam_users
ALTER COLUMN steam_user_id DROP DEFAULT;