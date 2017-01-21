-- steam_replays

CREATE TABLE steam_replays
(
  steam_replay_id serial NOT NULL,
  steam_user_id integer NOT NULL,
  ugcid bigint NOT NULL,
  seed bigint,
  downloaded smallint NOT NULL,
  invalid smallint NOT NULL,
  CONSTRAINT pk_sr_replay_id PRIMARY KEY (steam_replay_id),
  CONSTRAINT fk_sr_steam_user_id FOREIGN KEY (steam_user_id)
      REFERENCES steam_users (steam_user_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT uq_sr_ugcid UNIQUE (ugcid)
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE steam_replays_steam_replay_id_seq
  RENAME TO steam_replays_seq;

CREATE INDEX idx_sr_steam_user_id
  ON steam_replays
  USING btree
  (steam_user_id);
  
CREATE INDEX idx_sr_downloaded
  ON steam_replays
  USING btree
  (downloaded);
  
CREATE INDEX idx_sr_invalid
  ON steam_replays
  USING btree
  (invalid);
  
CREATE INDEX idx_sr_downloaded_invalid
  ON steam_replays
  USING btree
  (downloaded, invalid);