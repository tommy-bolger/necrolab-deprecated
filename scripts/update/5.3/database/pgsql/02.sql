ALTER TABLE power_rankings
ADD COLUMN latest_steam_replay_version_id smallint;
  
ALTER TABLE power_rankings
ADD CONSTRAINT fk_pr_latest_steam_replay_version_id FOREIGN KEY (latest_steam_replay_version_id) REFERENCES steam_replay_versions (steam_replay_version_id) ON UPDATE CASCADE ON DELETE CASCADE;