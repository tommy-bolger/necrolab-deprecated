CREATE TABLE reddit_users (
  reddit_user_id serial NOT NULL,
  reddit_id character varying(10) NOT NULL,
  username character varying(20) NOT NULL,
  comment_karma integer NOT NULL,
  link_karma integer NOT NULL,
  over_18 smallint NOT NULL,
  has_gold smallint NOT NULL,
  is_employee smallint NOT NULL,
  reddit_created timestamp without time zone NOT NULL,
  updated timestamp without time zone NOT NULL,
  CONSTRAINT pk_ru_reddit_user_id PRIMARY KEY (reddit_user_id),
  CONSTRAINT uq_reddit_users_reddit_id UNIQUE (reddit_id)
);

ALTER SEQUENCE reddit_users_reddit_user_id_seq
RENAME TO reddit_users_seq;

CREATE INDEX idx_ru_username
ON reddit_users
USING btree (username);

CREATE INDEX idx_ru_updated
ON reddit_users (updated);

ALTER TABLE steam_users
ADD COLUMN reddit_user_id integer;

ALTER TABLE steam_users
ADD CONSTRAINT fk_steam_users_reddit_user_id FOREIGN KEY (reddit_user_id) REFERENCES reddit_users (reddit_user_id) ON UPDATE CASCADE ON DELETE SET NULL;

CREATE INDEX idx_su_reddit_user_id
ON steam_users
USING btree (reddit_user_id);



CREATE TABLE reddit_user_tokens (
  reddit_user_token_id serial NOT NULL,
  reddit_user_id integer NOT NULL,
  token text NOT NULL,
  refresh_token text NOT NULL,
  expires timestamp without time zone DEFAULT NULL,
  created timestamp without time zone NOT NULL,
  expired timestamp without time zone DEFAULT NULL,
  CONSTRAINT pk_rut_reddit_user_token_id PRIMARY KEY (reddit_user_token_id),
  CONSTRAINT uq_rut_reddit_user_id_token UNIQUE (reddit_user_id, token),
  CONSTRAINT fk_rut_reddit_user_id FOREIGN KEY (reddit_user_id) REFERENCES reddit_users (reddit_user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER SEQUENCE reddit_user_tokens_reddit_user_token_id_seq
RENAME TO reddit_users_tokens_seq;

CREATE INDEX idx_tut_reddit_user_id
ON reddit_user_tokens
USING btree (reddit_user_id);


INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'reddit_client_id', 8, 1, 'Reddit Client ID', 0);

INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'reddit_client_secret', 9, 1, 'Reddit Client Secret', 0);