CREATE TABLE achievements
(
  achievement_id smallserial NOT NULL,
  name character varying(255) NOT NULL,
  display_name character varying(255) NOT NULL,
  description text NOT NULL,
  icon_url text NOT NULL,
  icon_gray_url text NOT NULL,
  CONSTRAINT pk_achievements_achievement_id PRIMARY KEY (achievement_id)
)
WITH (
  OIDS=FALSE
);

ALTER SEQUENCE achievements_achievement_id_seq
RENAME TO achievements_seq;



CREATE TABLE steam_user_achievements
(
  steam_user_id integer NOT NULL,
  achievement_id smallint NOT NULL,
  achieved timestamp without time zone NOT NULL,
  CONSTRAINT pk_steam_user_achievement PRIMARY KEY (steam_user_id, achievement_id),
  CONSTRAINT fk_sua_achievement_id FOREIGN KEY (achievement_id)
      REFERENCES achievements (achievement_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_sua_steam_user_id FOREIGN KEY (steam_user_id)
      REFERENCES steam_users (steam_user_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);

CREATE INDEX idx_sua_steam_user_id
ON steam_user_achievements
USING btree
(steam_user_id);

CREATE INDEX idx_sua_achievement_id
ON steam_user_achievements
USING btree
(achievement_id);