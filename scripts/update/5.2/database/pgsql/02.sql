CREATE TABLE releases
(
   release_id smallserial NOT NULL, 
   name character varying(100) NOT NULL, 
   display_name character varying(100) NOT NULL,
   start_date date NOT NULL,
   end_date date,
   CONSTRAINT pk_r_releases_release_type_id PRIMARY KEY (release_id)
) 
WITH (
  OIDS = FALSE
);

ALTER SEQUENCE releases_release_id_seq
RENAME TO releases_seq;

INSERT INTO releases (name, display_name, start_date, end_date)
VALUES ('alpha', 'Alpha', '2000-01-01', '2014-07-30');

INSERT INTO releases (name, display_name, start_date, end_date)
VALUES ('early_access', 'Early Access', '2014-07-30', '2015-04-22');

INSERT INTO releases (name, display_name, start_date)
VALUES ('original_release', 'Original Release', '2015-04-23');

INSERT INTO releases (name, display_name, start_date)
VALUES ('amplified_dlc_early_access', 'Amplified DLC Early Access', '2017-01-24');

INSERT INTO releases (name, display_name, start_date)
VALUES ('amplified_dlc', 'Amplified DLC', '2020-12-31');


ALTER TABLE power_rankings
ADD COLUMN release_id smallint NOT NULL;

ALTER TABLE power_rankings
DROP CONSTRAINT uq_power_rankings_date;

ALTER TABLE power_rankings
ADD CONSTRAINT uq_power_ranking_record UNIQUE (date, release_id);
  
ALTER TABLE power_rankings
ADD CONSTRAINT fk_pr_release_id FOREIGN KEY (release_id) REFERENCES releases (release_id) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE INDEX idx_pr_release_id
  ON power_rankings
  USING btree
  (release_id);


ALTER TABLE daily_rankings
ADD COLUMN release_id smallint NOT NULL;

ALTER TABLE daily_rankings
DROP CONSTRAINT uq_daily_rankings_date_daily_ranking_day_type_id;

ALTER TABLE daily_rankings
ADD CONSTRAINT uq_daily_ranking_record UNIQUE (date, release_id, daily_ranking_day_type_id);
  
ALTER TABLE daily_rankings
ADD CONSTRAINT fk_dr_release_id FOREIGN KEY (release_id) REFERENCES releases (release_id) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE INDEX idx_dr_release_id
  ON daily_rankings
  USING btree
  (release_id);


ALTER TABLE leaderboards
ADD COLUMN release_id smallint;
  
ALTER TABLE leaderboards
ADD CONSTRAINT fk_l_release_id FOREIGN KEY (release_id) REFERENCES releases (release_id) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE INDEX idx_l_release_id
  ON leaderboards
  USING btree
  (release_id);

UPDATE leaderboards
SET release_id = (
    SELECT release_id
    FROM releases
    WHERE name = 'early_access'
)
WHERE is_dev = 1;

UPDATE leaderboards
SET release_id = (
    SELECT release_id
    FROM releases
    WHERE name = 'original_release'
)
WHERE is_prod = 1;

UPDATE leaderboards
SET release_id = (
    SELECT release_id
    FROM releases
    WHERE name = 'amplified_dlc_early_access'
)
WHERE is_dlc = 1;