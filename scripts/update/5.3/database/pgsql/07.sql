CREATE TABLE external_sites (
    external_site_id serial NOT NULL,
    name character varying(100) NOT NULL,
    display_name character varying(100) NOT NULL,
    active smallint NOT NULL,
    CONSTRAINT pk_es_external_site_id PRIMARY KEY (external_site_id)
);

ALTER SEQUENCE external_sites_external_site_id_seq
RENAME TO external_sites_seq;

INSERT INTO external_sites (name, display_name, active)
VALUES ('twitch', 'Twitch', 1);

INSERT INTO external_sites (name, display_name, active)
VALUES ('discord', 'Discord', 1);

INSERT INTO external_sites (name, display_name, active)
VALUES ('reddit', 'Reddit', 1);

INSERT INTO external_sites (name, display_name, active)
VALUES ('youtube', 'Youtube', 1);

INSERT INTO external_sites (name, display_name, active)
VALUES ('twitter', 'Twitter', 1);

INSERT INTO external_sites (name, display_name, active)
VALUES ('beampro', 'Beam.pro', 1);

INSERT INTO external_sites (name, display_name, active)
VALUES ('hitbox', 'Hitbox', 1);