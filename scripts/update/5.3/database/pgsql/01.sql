CREATE TABLE twitch_users (
  twitch_user_id serial NOT NULL,
  twitch_id bigint NOT NULL,
  email character varying(255),
  partnered smallint NOT NULL,
  logo text,
  bio text,
  username character varying(255) NOT NULL,
  user_display_name character varying(255) NOT NULL,
  updated timestamp without timezone NOT NULL,
  CONSTRAINT pk_tu_twitch_user_id PRIMARY KEY (twitch_user_id),
  CONSTRAINT uq_twitch_users_twitch_id UNIQUE (twitch_id)
);

ALTER SEQUENCE twitch_users_twitch_user_id_seq
RENAME TO twitch_users_seq;

CREATE INDEX idx_tu_username
ON twitch_users
USING btree (username);

CREATE INDEX idx_tu_updated
ON twitch_users (updated);

ALTER TABLE steam_users
ADD COLUMN twitch_user_id integer;

ALTER TABLE steam_users
ADD CONSTRAINT fk_steam_users_twitch_user_id FOREIGN KEY (twitch_user_id) REFERENCES twitch_users (twitch_user_id) ON UPDATE CASCADE ON DELETE SET NULL;

CREATE INDEX idx_su_twitch_user_id
ON steam_users
USING btree (twitch_user_id);



CREATE TABLE twitch_user_tokens (
  twitch_user_token_id serial NOT NULL,
  twitch_user_id integer NOT NULL,
  token text NOT NULL,
  refresh_token text NOT NULL,
  expires timestamp without time zone DEFAULT NULL,
  created timestamp without time zone NOT NULL,
  expired timestamp without time zone DEFAULT NULL,
  CONSTRAINT pk_tut_twitch_user_token_id PRIMARY KEY (twitch_user_token_id),
  CONSTRAINT uq_tut_twitch_user_id_token UNIQUE (twitch_user_id, token),
  CONSTRAINT fk_ctk_twitch_user_id FOREIGN KEY (twitch_user_id) REFERENCES twitch_users (twitch_user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER SEQUENCE twitch_user_tokens_twitch_user_token_id_seq
RENAME TO twitch_users_tokens_seq;

CREATE INDEX idx_tut_twitch_user_id
ON twitch_user_tokens
USING btree (twitch_user_id);


INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'twitch_client_id', 6, 1, 'Twitch Client ID', 0);

INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'twitch_client_secret', 7, 1, 'Twitch Client Secret', 0);