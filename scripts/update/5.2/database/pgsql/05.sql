CREATE TABLE steam_replay_versions
(
   steam_replay_version_id smallserial NOT NULL, 
   name character varying(10) NOT NULL
   CONSTRAINT pk_srv_steam_replay_versions_steam_replay_version_id PRIMARY KEY (steam_replay_version_id)
) 
WITH (
  OIDS = FALSE
);

ALTER SEQUENCE steam_replay_versions_steam_replay_version_id_seq
RENAME TO steam_replay_versions_seq;


ALTER TABLE steam_replays
ADD COLUMN steam_replay_version_id smallint;
  
ALTER TABLE steam_replays
ADD CONSTRAINT fk_sr_steam_replay_version_id FOREIGN KEY (steam_replay_version_id) REFERENCES steam_replay_versions (steam_replay_version_id) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE INDEX idx_sr_steam_replay_version_id
  ON steam_replays
  USING btree
  (steam_replay_version_id);