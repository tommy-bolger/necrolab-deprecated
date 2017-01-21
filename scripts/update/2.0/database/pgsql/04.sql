ALTER TABLE steam_users
ADD COLUMN latest_power_ranking_entry_id bigint;

ALTER TABLE steam_users
ADD CONSTRAINT fk_steam_users_power_ranking_id FOREIGN KEY (latest_power_ranking_entry_id) REFERENCES power_ranking_entries (power_ranking_entry_id) ON DELETE NO ACTION ON UPDATE NO ACTION;