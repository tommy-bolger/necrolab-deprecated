CREATE TABLE cms_cache
(
  category character varying(255) NOT NULL,
  key character varying(255) NOT NULL,
  value text,
  expires timestamp without time zone,
  CONSTRAINT pk_cc_cache_category_key PRIMARY KEY (category, key)
)
WITH (
  OIDS=FALSE
);