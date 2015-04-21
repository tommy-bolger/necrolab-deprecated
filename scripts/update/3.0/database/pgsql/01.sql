ALTER TABLE steam_users
  ADD COLUMN latest_daily_ranking_entry_id bigint;
  
ALTER TABLE steam_users
  ADD CONSTRAINT fk_steam_users_latest_daily_ranking_entry_id FOREIGN KEY (latest_daily_ranking_entry_id) REFERENCES daily_ranking_entries (daily_ranking_entry_id) ON UPDATE CASCADE ON DELETE SET NULL;

CREATE INDEX idx_steam_users_latest_daily_ranking_entry_id
   ON steam_users (latest_daily_ranking_entry_id ASC NULLS LAST);