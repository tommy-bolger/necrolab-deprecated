CREATE TABLE leaderboards_blacklist
(
  leaderboards_blacklist_id serial NOT NULL,
  leaderboard_id integer,
  CONSTRAINT pk_leaderboards_blacklist_id PRIMARY KEY (leaderboards_blacklist_id),
  CONSTRAINT fk_lb_leaderboard_id FOREIGN KEY (leaderboard_id)
      REFERENCES leaderboards (leaderboard_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE leaderboards_blacklist_leaderboards_blacklist_id_seq
  RENAME TO leaderboards_blacklist_seq;