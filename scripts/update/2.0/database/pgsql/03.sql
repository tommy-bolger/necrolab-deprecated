DROP TABLE power_ranking_entries;

CREATE SEQUENCE power_ranking_entries_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE TABLE power_ranking_entries
(
  power_ranking_entry_id bigint NOT NULL DEFAULT nextval('power_ranking_entries_seq'::regclass),
  power_ranking_id integer NOT NULL,
  steam_user_id integer NOT NULL,
  
  cadence_score_rank integer,
  cadence_score_rank_points double precision,
  cadence_score integer,
  
  bard_score_rank integer,
  bard_score_rank_points double precision,
  bard_score integer,
  
  monk_score_rank integer,
  monk_score_rank_points double precision,
  monk_score integer,
  
  aria_score_rank integer,
  aria_score_rank_points double precision,
  aria_score integer,
  
  bolt_score_rank integer,
  bolt_score_rank_points double precision,
  bolt_score integer,
  
  dove_score_rank integer,
  dove_score_rank_points double precision,
  dove_score integer,
  
  eli_score_rank integer,
  eli_score_rank_points double precision,
  eli_score integer,
  
  melody_score_rank integer,
  melody_score_rank_points double precision,
  melody_score integer,
  
  dorian_score_rank integer,
  dorian_score_rank_points double precision,
  dorian_score integer,
  
  coda_score_rank integer,
  coda_score_rank_points double precision,
  coda_score integer,
  
  all_score_rank integer,
  all_score_rank_points double precision,
  all_score integer,
  
  story_score_rank integer,
  story_score_rank_points double precision,
  story_score integer,
  
  score_rank_points_total double precision,
  
  
  
  cadence_deathless_score_rank integer,
  cadence_deathless_score_rank_points double precision,
  cadence_deathless_score integer,
  
  bard_deathless_score_rank integer,
  bard_deathless_score_rank_points double precision,
  bard_deathless_score integer,
  
  monk_deathless_score_rank integer,
  monk_deathless_score_rank_points double precision,
  monk_deathless_score integer,
  
  aria_deathless_score_rank integer,
  aria_deathless_score_rank_points double precision,
  aria_deathless_score integer,
  
  bolt_deathless_score_rank integer,
  bolt_deathless_score_rank_points double precision,
  bolt_deathless_score integer,
  
  dove_deathless_score_rank integer,
  dove_deathless_score_rank_points double precision,
  dove_deathless_score integer,
  
  eli_deathless_score_rank integer,
  eli_deathless_score_rank_points double precision,
  eli_deathless_score integer,
  
  melody_deathless_score_rank integer,
  melody_deathless_score_rank_points double precision,
  melody_deathless_score integer,
  
  dorian_deathless_score_rank integer,
  dorian_deathless_score_rank_points double precision,
  dorian_deathless_score integer,
  
  coda_deathless_score_rank integer,
  coda_deathless_score_rank_points double precision,
  coda_deathless_score integer,
  
  all_deathless_score_rank integer,
  all_deathless_score_rank_points double precision,
  all_deathless_score integer,
  
  story_deathless_score_rank integer,
  story_deathless_score_rank_points double precision,
  story_deathless_score integer,
  
  deathless_score_rank_points_total double precision,
  
  
  
  cadence_speed_rank integer,
  cadence_speed_rank_points double precision,
  cadence_speed_time integer,
  
  bard_speed_rank integer,
  bard_speed_rank_points double precision,
  bard_speed_time integer,
  
  monk_speed_rank integer,
  monk_speed_rank_points double precision,
  monk_speed_time integer,
  
  aria_speed_rank integer,
  aria_speed_rank_points double precision,
  aria_speed_time integer,
  
  bolt_speed_rank integer,
  bolt_speed_rank_points double precision,
  bolt_speed_time integer,
  
  dove_speed_rank integer,
  dove_speed_rank_points double precision,
  dove_speed_time integer,
  
  eli_speed_rank integer,
  eli_speed_rank_points double precision,
  eli_speed_time integer,
  
  melody_speed_rank integer,
  melody_speed_rank_points double precision,
  melody_speed_time integer,
  
  dorian_speed_rank integer,
  dorian_speed_rank_points double precision,
  dorian_speed_time integer,
  
  coda_speed_rank integer,
  coda_speed_rank_points double precision,
  coda_speed_time integer,
  
  all_speed_rank integer,
  all_speed_rank_points double precision,
  all_speed_time integer,
  
  story_speed_rank integer,
  story_speed_rank_points double precision,
  story_speed_time integer,   
  
  speed_rank_points_total double precision,
  


  cadence_deathless_speed_rank integer,
  cadence_deathless_speed_rank_points double precision,
  cadence_deathless_speed_time integer,
  
  bard_deathless_speed_rank integer,
  bard_deathless_speed_rank_points double precision,
  bard_deathless_speed_time integer,
  
  monk_deathless_speed_rank integer,
  monk_deathless_speed_rank_points double precision,
  monk_deathless_speed_time integer,
  
  aria_deathless_speed_rank integer,
  aria_deathless_speed_rank_points double precision,
  aria_deathless_speed_time integer,
  
  bolt_deathless_speed_rank integer,
  bolt_deathless_speed_rank_points double precision,
  bolt_deathless_speed_time integer,
  
  dove_deathless_speed_rank integer,
  dove_deathless_speed_rank_points double precision,
  dove_deathless_speed_time integer,
  
  eli_deathless_speed_rank integer,
  eli_deathless_speed_rank_points double precision,
  eli_deathless_speed_time integer,
  
  melody_deathless_speed_rank integer,
  melody_deathless_speed_rank_points double precision,
  melody_deathless_speed_time integer,
  
  dorian_deathless_speed_rank integer,
  dorian_deathless_speed_rank_points double precision,
  dorian_deathless_speed_time integer,
  
  coda_deathless_speed_rank integer,
  coda_deathless_speed_rank_points double precision,
  coda_deathless_speed_time integer,
  
  all_deathless_speed_rank integer,
  all_deathless_speed_rank_points double precision,
  all_deathless_speed_time integer,
  
  story_deathless_speed_rank integer,
  story_deathless_speed_rank_points double precision,
  story_deathless_speed_time integer,
  
  deathless_speed_rank_points_total double precision,
  total_points double precision,
  speed_rank integer,
  deathless_speed_rank integer,
  score_rank integer,
  deathless_score_rank integer,
  
  rank integer NOT NULL,
  CONSTRAINT pk_power_ranking_entries_power_ranking_entry_id PRIMARY KEY (power_ranking_entry_id),
  CONSTRAINT fk_power_ranking_entries_power_ranking_id FOREIGN KEY (power_ranking_id)
      REFERENCES power_rankings (power_ranking_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_power_ranking_entries_steam_user_id FOREIGN KEY (steam_user_id)
      REFERENCES steam_users (steam_user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
  
ALTER SEQUENCE power_ranking_entries_seq OWNED BY power_ranking_entries.power_ranking_entry_id;

-- Index: idx_power_ranking_entries_power_ranking_id

-- DROP INDEX idx_power_ranking_entries_power_ranking_id;

CREATE INDEX idx_power_ranking_entries_power_ranking_id
  ON power_ranking_entries
  USING btree
  (power_ranking_id);

-- Index: idx_power_ranking_entries_rank_asc

-- DROP INDEX idx_power_ranking_entries_rank_asc;

CREATE INDEX idx_power_ranking_entries_rank_asc
  ON power_ranking_entries
  USING btree
  (rank);

-- Index: idx_power_ranking_entries_rank_desc

-- DROP INDEX idx_power_ranking_entries_rank_desc;

CREATE INDEX idx_power_ranking_entries_rank_desc
  ON power_ranking_entries
  USING btree
  (rank DESC);

-- Index: idx_power_ranking_entries_steam_user_id

-- DROP INDEX idx_power_ranking_entries_steam_user_id;

CREATE INDEX idx_power_ranking_entries_steam_user_id
  ON power_ranking_entries
  USING btree
  (steam_user_id);
  
  
-- Index: idx_power_ranking_entries_speed_rank_asc

-- DROP INDEX idx_power_ranking_entries_speed_rank_asc;

CREATE INDEX idx_power_ranking_entries_speed_rank_asc
  ON power_ranking_entries
  USING btree
  (speed_rank);

-- Index: idx_power_ranking_entries_deathless_speed_rank_desc

-- DROP INDEX idx_power_ranking_entries_deathless_speed_rank_desc;

CREATE INDEX idx_power_ranking_entries_deathless_speed_rank_desc
  ON power_ranking_entries
  USING btree
  (deathless_speed_rank DESC);
  
-- Index: idx_power_ranking_entries_score_rank_desc

-- DROP INDEX idx_power_ranking_entries_score_rank_desc;

CREATE INDEX idx_power_ranking_entries_score_rank_desc
  ON power_ranking_entries
  USING btree
  (score_rank DESC);  
  
-- Index: idx_power_ranking_entries_deathless_score_rank_desc

-- DROP INDEX idx_power_ranking_entries_deathless_score_rank_desc;

CREATE INDEX idx_power_ranking_entries_deathless_score_rank_desc
  ON power_ranking_entries
  USING btree
  (deathless_score_rank DESC);  