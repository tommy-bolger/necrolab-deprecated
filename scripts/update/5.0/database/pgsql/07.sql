-- daily_ranking_season_leaderboards

CREATE TABLE daily_ranking_season_leaderboards
(
  daily_ranking_season_leaderboard_id serial NOT NULL,
  daily_ranking_season_id integer NOT NULL,
  leaderboard_id integer NOT NULL,
  daily_date date NOT NULL,
  CONSTRAINT pk_drsl_daily_ranking_season_leaderboard_id PRIMARY KEY (daily_ranking_season_leaderboard_id),
  CONSTRAINT fk_drsl_daily_ranking_season_id FOREIGN KEY (daily_ranking_season_id)
      REFERENCES daily_ranking_seasons (daily_ranking_season_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_drsl_leaderboard_id FOREIGN KEY (leaderboard_id)
      REFERENCES leaderboards (leaderboard_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT uq_drsl_daily_ranking_season_id_leaderboard_id UNIQUE (daily_ranking_season_id, leaderboard_id)
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE daily_ranking_season_leaderbo_daily_ranking_season_leaderbo_seq
  RENAME TO daily_ranking_season_leaderboards_seq;

CREATE INDEX idx_drsl_daily_ranking_season_id
  ON daily_ranking_season_leaderboards
  USING btree
  (daily_ranking_season_id);

CREATE INDEX idx_drsl_leaderboard_id
  ON daily_ranking_season_leaderboards
  USING btree
  (leaderboard_id);

CREATE INDEX idx_drsl_daily_date
  ON daily_ranking_season_leaderboards
  USING btree
  (daily_date);
  

-- daily_ranking_season_leaderboard_entries  
  
CREATE TABLE daily_ranking_season_leaderboard_entries
(
  daily_ranking_season_leaderboard_entry_id serial NOT NULL,
  daily_ranking_season_leaderboard_id integer NOT NULL,
  leaderboard_entry_id integer NOT NULL,
  steam_user_id integer NOT NULL,
  rank integer NOT NULL,
  CONSTRAINT pk_drsle_daily_ranking_season_leaderboard_entry_id PRIMARY KEY (daily_ranking_season_leaderboard_entry_id),
  CONSTRAINT fk_drsle_daily_ranking_season_leaderboard_id FOREIGN KEY (daily_ranking_season_leaderboard_id)
      REFERENCES daily_ranking_season_leaderboards (daily_ranking_season_leaderboard_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_drsle_leaderboard_entry_id FOREIGN KEY (leaderboard_entry_id)
      REFERENCES leaderboard_entries (leaderboard_entry_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_drsle_steam_user_id FOREIGN KEY (steam_user_id)
      REFERENCES steam_users (steam_user_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT uq_drsle_daily_ranking_season_leaderboard_id_leaderboard_entry_id UNIQUE (daily_ranking_season_leaderboard_id, leaderboard_entry_id)
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE daily_ranking_season_leaderbo_daily_ranking_season_leaderbo_seq
  RENAME TO daily_ranking_season_leaderboard_entries_seq;
  
CREATE INDEX idx_drsle_daily_ranking_season_leaderboard_id
  ON daily_ranking_season_leaderboard_entries
  USING btree
  (daily_ranking_season_leaderboard_id);
  
CREATE INDEX idx_drsle_leaderboard_entry_id
  ON daily_ranking_season_leaderboard_entries
  USING btree
  (leaderboard_entry_id);
  
CREATE INDEX idx_drsle_steam_user_id
  ON daily_ranking_season_leaderboard_entries
  USING btree
  (steam_user_id);