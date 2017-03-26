CREATE TABLE modes
(
    mode_id smallserial NOT NULL,
    name character varying(100) NOT NULL,
    display_name character varying NOT NULL,
    sort_order smallint NOT NULL,
    CONSTRAINT pk_modes_mode_id PRIMARY KEY (mode_id),
    CONSTRAINT uq_modes_name UNIQUE (name)
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE modes_mode_id_seq
RENAME TO modes_seq;

INSERT INTO modes (name, display_name, sort_order)
VALUES ('normal', 'Normal', 1);

INSERT INTO modes (name, display_name, sort_order)
VALUES ('hard', 'Hard', 2);

INSERT INTO modes (name, display_name, sort_order)
VALUES ('no_return', 'No Return', 3);

ALTER TABLE power_rankings
DROP CONSTRAINT uq_power_ranking_record;

ALTER TABLE power_rankings
ADD COLUMN mode_id smallint;

ALTER TABLE power_rankings
ADD CONSTRAINT fk_pr_mode_id FOREIGN KEY (mode_id) REFERENCES modes (mode_id);

UPDATE power_rankings
SET mode_id = 1;

ALTER TABLE power_rankings
ALTER COLUMN mode_id SET NOT NULL;

ALTER TABLE power_rankings
ADD CONSTRAINT uq_power_ranking_record UNIQUE (date, release_id, mode_id);


ALTER TABLE leaderboards
ADD COLUMN mode_id smallint;

ALTER TABLE leaderboards
ADD CONSTRAINT fk_l_mode_id FOREIGN KEY (mode_id) REFERENCES modes (mode_id);

UPDATE leaderboards
SET mode_id = 1
WHERE is_hard_mode = 0
    AND is_no_return = 0;
    
UPDATE leaderboards
SET mode_id = 2
WHERE is_hard_mode = 1;

UPDATE leaderboards
SET mode_id = 3
WHERE is_no_return = 1;

ALTER TABLE leaderboards
ALTER COLUMN mode_id SET NOT NULL;


ALTER TABLE daily_rankings
DROP CONSTRAINT uq_daily_ranking_record;

ALTER TABLE daily_rankings
ADD COLUMN mode_id smallint;

ALTER TABLE daily_rankings
ADD CONSTRAINT fk_dr_mode_id FOREIGN KEY (mode_id) REFERENCES modes (mode_id);

UPDATE daily_rankings
SET mode_id = 1;

ALTER TABLE daily_rankings
ALTER COLUMN mode_id SET NOT NULL;

ALTER TABLE daily_rankings
ADD CONSTRAINT uq_daily_ranking_record UNIQUE (date, release_id, mode_id, daily_ranking_day_type_id);