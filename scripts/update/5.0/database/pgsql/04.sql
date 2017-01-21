CREATE TABLE daily_ranking_seasons
(
  daily_ranking_season_id serial NOT NULL,
  season_number smallint NOT NULL,
  start_date date NOT NULL,
  end_date date NOT NULL,
  enrollment_start_date date NOT NULL,
  enrollment_end_date date NOT NULL,
  prize_payment_date date NOT NULL,
  is_latest smallint NOT NULL DEFAULT 0,
  CONSTRAINT pk_daily_ranking_seasons_daily_ranking_season_id PRIMARY KEY (daily_ranking_season_id)
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE daily_ranking_seasons_daily_ranking_season_id_seq
  RENAME TO daily_ranking_seasons_seq;
  
CREATE TABLE daily_ranking_season_snapshots
(
  daily_ranking_season_snapshot_id serial NOT NULL,
  date date NOT NULL,
  is_latest smallint NOT NULL DEFAULT 0,
  daily_ranking_season_id integer NOT NULL,
  created timestamp without time zone NOT NULL,
  updated timestamp without time zone,
  CONSTRAINT pk_daily_ranking_season_snapshot_daily_ranking_season_snapshot_ PRIMARY KEY (daily_ranking_season_snapshot_id),
  CONSTRAINT fk_daily_ranking_season_daily_ranking_season_id FOREIGN KEY (daily_ranking_season_id)
      REFERENCES daily_ranking_seasons (daily_ranking_season_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE daily_ranking_season_snapshots_daily_ranking_season_snapshot_id
  RENAME TO daily_ranking_season_snapshots_seq;
  
  
CREATE TABLE daily_ranking_season_entries
(
  daily_ranking_season_entry_id serial NOT NULL,
  daily_ranking_season_id integer NOT NULL,
  daily_ranking_season_snapshot_id integer NOT NULL,
  steam_user_id integer NOT NULL,
  first_place_ranks smallint NOT NULL DEFAULT 0,
  top_5_ranks smallint NOT NULL DEFAULT 0,
  top_10_ranks smallint NOT NULL DEFAULT 0,
  top_20_ranks smallint NOT NULL DEFAULT 0,
  top_50_ranks smallint NOT NULL DEFAULT 0,
  top_100_ranks smallint NOT NULL DEFAULT 0,
  total_points double precision NOT NULL DEFAULT 0,
  points_per_day smallint NOT NULL DEFAULT 0,
  total_dailies smallint NOT NULL DEFAULT 0,
  total_wins smallint NOT NULL DEFAULT 0,
  average_rank smallint NOT NULL DEFAULT 0,
  sum_of_ranks integer NOT NULL DEFAULT 0,
  number_of_ranks smallint NOT NULL DEFAULT 0,
  rank integer NOT NULL,
  CONSTRAINT pk_daily_ranking_season_entries_daily_ranking_season_entry_id PRIMARY KEY (daily_ranking_season_entry_id),
  CONSTRAINT fk_daily_ranking_season_entries_daily_ranking_season_id FOREIGN KEY (daily_ranking_season_id)
      REFERENCES daily_ranking_seasons (daily_ranking_season_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_daily_ranking_season_entries_daily_ranking_season_snapshot_i FOREIGN KEY (daily_ranking_season_snapshot_id)
      REFERENCES daily_ranking_season_snapshots (daily_ranking_season_snapshot_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_daily_ranking_season_entries_steam_user_id FOREIGN KEY (steam_user_id)
      REFERENCES steam_users (steam_user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE daily_ranking_season_entries_daily_ranking_season_entry_id
  RENAME TO daily_ranking_season_entries_seq;
  
  
CREATE TABLE daily_ranking_season_enrollment
(
  daily_ranking_season_enrollment_id serial NOT NULL,
  daily_ranking_season_id integer NOT NULL,
  steam_user_id integer NOT NULL,
  enrolled date NOT NULL,
  unenrolled date NOT NULL,
  CONSTRAINT pk_daily_ranking_season_enrollment_id PRIMARY KEY (daily_ranking_season_enrollment_id),
  CONSTRAINT daily_ranking_season_enrollment_steam_user_id FOREIGN KEY (steam_user_id)
      REFERENCES steam_users (steam_user_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_daily_ranking_season_enrollment_daily_ranking_season_id FOREIGN KEY (daily_ranking_season_id)
      REFERENCES daily_ranking_seasons (daily_ranking_season_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT uq_drse_daily_ranking_season_id_steam_user_id UNIQUE (daily_ranking_season_id, steam_user_id);
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE daily_ranking_season_enrollment_daily_ranking_season_enrollment_id
  RENAME TO daily_ranking_season_enrollment_seq;