CREATE TABLE twitter_users (
  twitter_user_id serial NOT NULL,
  twitter_id bigint NOT NULL,
  nickname character varying(255),
  name character varying(255),
  email text,
  description text,
  avatar text,
  followers_count integer,
  friends_count integer,
  statuses_count integer,
  verified smallint,
  updated timestamp without time zone NOT NULL,
  CONSTRAINT pk_twu_twitter_user_id PRIMARY KEY (twitter_user_id),
  CONSTRAINT uq_twitter_users_twitter_id UNIQUE (twitter_id)
);

ALTER SEQUENCE twitter_users_twitter_user_id_seq
RENAME TO twitter_users_seq;

CREATE INDEX idx_twu_updated
ON twitter_users (updated);

ALTER TABLE steam_users
ADD COLUMN twitter_user_id integer;

ALTER TABLE steam_users
ADD CONSTRAINT fk_steam_users_twitter_user_id FOREIGN KEY (twitter_user_id) REFERENCES twitter_users (twitter_user_id) ON UPDATE CASCADE ON DELETE SET NULL;

CREATE INDEX idx_su_twitter_user_id
ON steam_users
USING btree (twitter_user_id);



CREATE TABLE twitter_user_tokens (
  twitter_user_token_id serial NOT NULL,
  twitter_user_id integer NOT NULL,
  identifier text NOT NULL,
  secret text,
  expires timestamp without time zone DEFAULT NULL,
  created timestamp without time zone NOT NULL,
  expired timestamp without time zone DEFAULT NULL,
  CONSTRAINT pk_twut_twitter_user_token_id PRIMARY KEY (twitter_user_token_id),
  CONSTRAINT uq_twut_twitter_user_id_token UNIQUE (twitter_user_id, identifier),
  CONSTRAINT fk_twut_twitter_user_id FOREIGN KEY (twitter_user_id) REFERENCES twitter_users (twitter_user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER SEQUENCE twitter_user_tokens_twitter_user_token_id_seq
RENAME TO twitter_users_tokens_seq;

CREATE INDEX idx_twut_twitter_user_id
ON twitter_user_tokens
USING btree (twitter_user_id);


INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'twitter_client_id', 14, 1, 'Twitter Client ID', 0);

INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'twitter_client_secret', 15, 1, 'Twitter Client Secret', 0); 
