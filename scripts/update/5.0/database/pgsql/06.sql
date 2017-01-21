CREATE TABLE daily_ranking_day_types
(
  daily_ranking_day_type_id smallserial NOT NULL,
  number_of_days smallint NOT NULL,
  enabled smallint NOT NULL DEFAULT 0,
  CONSTRAINT pk_drdt_daily_ranking_day_type_id PRIMARY KEY (daily_ranking_day_type_id)
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE daily_ranking_day_types_daily_ranking_day_type_id_seq
  RENAME TO daily_ranking_day_types_seq;
  
INSERT INTO daily_ranking_day_types (number_of_days, enabled)
VALUES (0, 1);

INSERT INTO daily_ranking_day_types (number_of_days, enabled)
VALUES (30, 1);

INSERT INTO daily_ranking_day_types (number_of_days, enabled)
VALUES (100, 1);

ALTER TABLE daily_rankings
ADD COLUMN daily_ranking_day_type_id integer;
  
ALTER TABLE daily_rankings
ADD CONSTRAINT fk_dr_daily_ranking_day_type_id FOREIGN KEY (daily_ranking_day_type_id) REFERENCES daily_ranking_day_types (daily_ranking_day_type_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE daily_rankings
  DROP CONSTRAINT pk_daily_rankings_date;
ALTER TABLE daily_rankings
  ADD CONSTRAINT uq_daily_rankings_date_daily_ranking_day_type_id UNIQUE (date, daily_ranking_day_type_id);

ALTER TABLE daily_ranking_entries RENAME average_place TO average_rank;

ALTER TABLE daily_ranking_entries
   ALTER COLUMN points_per_day TYPE double precision;

ALTER TABLE daily_ranking_entries
   ALTER COLUMN average_rank TYPE double precision;
   
ALTER TABLE daily_ranking_entries
  DROP COLUMN number_of_ranks;

CREATE TABLE steam_users_latest_daily_rankings
(
  steam_users_latest_daily_ranking_id serial NOT NULL,
  steam_user_id integer NOT NULL,
  daily_ranking_id integer,
  daily_ranking_entry_id bigint NOT NULL,
  CONSTRAINT pk_suldr_steam_users_latest_daily_ranking_id PRIMARY KEY (steam_users_latest_daily_ranking_id),
  CONSTRAINT fk_suldr_steam_user_id FOREIGN KEY (steam_user_id)
      REFERENCES steam_users (steam_user_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_suldr_daily_ranking_id FOREIGN KEY (daily_ranking_id)
      REFERENCES daily_rankings (daily_ranking_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_suldr_daily_ranking_entry_id FOREIGN KEY (daily_ranking_entry_id)
      REFERENCES daily_ranking_entries (daily_ranking_entry_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT uq_suldr_steam_user_id_daily_ranking_id UNIQUE (steam_user_id, daily_ranking_id)
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE steam_users_latest_daily_rank_steam_users_latest_daily_rank_seq
  RENAME TO steam_users_latest_daily_rankings_seq;
  
CREATE INDEX idx_suldr_steam_user_id
  ON steam_users_latest_daily_rankings
  USING btree
  (steam_user_id);
  
CREATE INDEX idx_suldr_daily_ranking_id
  ON steam_users_latest_daily_rankings
  USING btree
  (daily_ranking_id);
  
CREATE INDEX idx_suldr_daily_ranking_entry_id
  ON steam_users_latest_daily_rankings
  USING btree
  (daily_ranking_entry_id);