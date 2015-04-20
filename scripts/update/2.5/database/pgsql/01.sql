ALTER TABLE steam_users
  DROP CONSTRAINT fk_steam_users_power_ranking_id;
ALTER TABLE steam_users
  ADD CONSTRAINT fk_steam_users_latest_power_ranking_entry_id FOREIGN KEY (latest_power_ranking_entry_id) REFERENCES power_ranking_entries (power_ranking_entry_id) ON UPDATE CASCADE ON DELETE SET NULL;

CREATE UNIQUE INDEX idx_steam_users_latest_power_ranking_entry_id
   ON steam_users (latest_power_ranking_entry_id ASC NULLS LAST);