CREATE TABLE beampro_users (
  beampro_user_id serial NOT NULL,
  beampro_id bigint NOT NULL,
  username character varying(255),
  avatar_url text,
  description text,
  bio text,
  channel_title text,
  views integer,
  followers integer,
  updated timestamp without time zone NOT NULL,
  CONSTRAINT pk_bu_beampro_user_id PRIMARY KEY (beampro_user_id),
  CONSTRAINT uq_beampro_users_beampro_id UNIQUE (beampro_id)
);

ALTER SEQUENCE beampro_users_beampro_user_id_seq
RENAME TO beampro_users_seq;

CREATE INDEX idx_bu_updated
ON beampro_users (updated);

ALTER TABLE steam_users
ADD COLUMN beampro_user_id integer;

ALTER TABLE steam_users
ADD CONSTRAINT fk_steam_users_beampro_user_id FOREIGN KEY (beampro_user_id) REFERENCES beampro_users (beampro_user_id) ON UPDATE CASCADE ON DELETE SET NULL;

CREATE INDEX idx_su_beampro_user_id
ON steam_users
USING btree (beampro_user_id);



CREATE TABLE beampro_user_tokens (
  beampro_user_token_id serial NOT NULL,
  beampro_user_id integer NOT NULL,
  token text NOT NULL,
  refresh_token text NOT NULL,
  expires timestamp without time zone DEFAULT NULL,
  created timestamp without time zone NOT NULL,
  expired timestamp without time zone DEFAULT NULL,
  CONSTRAINT pk_but_beampro_user_token_id PRIMARY KEY (beampro_user_token_id),
  CONSTRAINT uq_but_beampro_user_id_token UNIQUE (beampro_user_id, token),
  CONSTRAINT fk_but_beampro_user_id FOREIGN KEY (beampro_user_id) REFERENCES beampro_users (beampro_user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER SEQUENCE beampro_user_tokens_beampro_user_token_id_seq
RENAME TO beampro_users_tokens_seq;

CREATE INDEX idx_but_beampro_user_id
ON beampro_user_tokens
USING btree (beampro_user_id);


INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'beampro_client_id', 16, 1, 'Beampro Client ID', 0);

INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'beampro_client_secret', 17, 1, 'Beampro Client Secret', 0); 
