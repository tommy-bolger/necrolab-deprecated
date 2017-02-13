CREATE TABLE youtube_users (
  youtube_user_id serial NOT NULL,
  youtube_id character varying(255) NOT NULL,
  etag character varying(255) NOT NULL,
  title character varying(255) NOT NULL,
  description text,
  default_thumbnail text,
  medium_thumbnail text,
  high_thumbnail text,
  updated timestamp without time zone NOT NULL,
  CONSTRAINT pk_yu_youtube_user_id PRIMARY KEY (youtube_user_id),
  CONSTRAINT uq_youtube_users_youtube_id UNIQUE (youtube_id)
);

ALTER SEQUENCE youtube_users_youtube_user_id_seq
RENAME TO youtube_users_seq;

CREATE INDEX idx_yu_updated
ON youtube_users (updated);

ALTER TABLE steam_users
ADD COLUMN youtube_user_id integer;

ALTER TABLE steam_users
ADD CONSTRAINT fk_steam_users_youtube_user_id FOREIGN KEY (youtube_user_id) REFERENCES youtube_users (youtube_user_id) ON UPDATE CASCADE ON DELETE SET NULL;

CREATE INDEX idx_su_youtube_user_id
ON steam_users
USING btree (youtube_user_id);



CREATE TABLE youtube_user_tokens (
  youtube_user_token_id serial NOT NULL,
  youtube_user_id integer NOT NULL,
  token text NOT NULL,
  refresh_token text,
  expires timestamp without time zone DEFAULT NULL,
  created timestamp without time zone NOT NULL,
  expired timestamp without time zone DEFAULT NULL,
  CONSTRAINT pk_yut_youtube_user_token_id PRIMARY KEY (youtube_user_token_id),
  CONSTRAINT uq_yut_youtube_user_id_token UNIQUE (youtube_user_id, token),
  CONSTRAINT fk_yut_youtube_user_id FOREIGN KEY (youtube_user_id) REFERENCES youtube_users (youtube_user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER SEQUENCE youtube_user_tokens_youtube_user_token_id_seq
RENAME TO youtube_users_tokens_seq;

CREATE INDEX idx_tut_youtube_user_id
ON youtube_user_tokens
USING btree (youtube_user_id);


INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'youtube_client_id', 12, 1, 'Youtube Client ID', 0);

INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'youtube_client_secret', 13, 1, 'Youtube Client Secret', 0); 
