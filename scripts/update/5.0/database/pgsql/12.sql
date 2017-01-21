-- leaderboard_entry_details

CREATE TABLE leaderboard_entry_details
(
  leaderboard_entry_details_id smallserial NOT NULL,
  details character varying(25) NOT NULL,
  CONSTRAINT pk_led_leaderboard_entry_details_id PRIMARY KEY (leaderboard_entry_details_id),
  CONSTRAINT uq_led_details UNIQUE (details)
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE leaderboard_entry_details_leaderboard_entry_details_id_seq
  RENAME TO leaderboard_entry_details_seq;