CREATE TABLE discord_users (
  discord_user_id serial NOT NULL,
  discord_id bigint NOT NULL,
  username character varying(255) NOT NULL,
  email text NOT NULL,
  discriminator smallint NOT NULL,
  avatar text,
  updated timestamp without time zone NOT NULL,
  CONSTRAINT pk_du_discord_user_id PRIMARY KEY (discord_user_id),
  CONSTRAINT uq_discord_users_discord_id UNIQUE (discord_id)
);

ALTER SEQUENCE discord_users_discord_user_id_seq
RENAME TO discord_users_seq;

CREATE INDEX idx_du_username
ON discord_users
USING btree (username);

CREATE INDEX idx_du_updated
ON discord_users (updated);

ALTER TABLE steam_users
ADD COLUMN discord_user_id integer;

ALTER TABLE steam_users
ADD CONSTRAINT fk_steam_users_discord_user_id FOREIGN KEY (discord_user_id) REFERENCES discord_users (discord_user_id) ON UPDATE CASCADE ON DELETE SET NULL;

CREATE INDEX idx_su_discord_user_id
ON steam_users
USING btree (discord_user_id);



CREATE TABLE discord_user_tokens (
  discord_user_token_id serial NOT NULL,
  discord_user_id integer NOT NULL,
  token text NOT NULL,
  refresh_token text NOT NULL,
  expires timestamp without time zone DEFAULT NULL,
  created timestamp without time zone NOT NULL,
  expired timestamp without time zone DEFAULT NULL,
  CONSTRAINT pk_dut_discord_user_token_id PRIMARY KEY (discord_user_token_id),
  CONSTRAINT uq_dut_discord_user_id_token UNIQUE (discord_user_id, token),
  CONSTRAINT fk_dut_discord_user_id FOREIGN KEY (discord_user_id) REFERENCES discord_users (discord_user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER SEQUENCE discord_user_tokens_discord_user_token_id_seq
RENAME TO discord_users_tokens_seq;

CREATE INDEX idx_tut_discord_user_id
ON discord_user_tokens
USING btree (discord_user_id);


INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'discord_client_id', 10, 1, 'Discord Client ID', 0);

INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'discord_client_secret', 11, 1, 'Discord Client Secret', 0); 
