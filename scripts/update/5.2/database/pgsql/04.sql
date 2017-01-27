CREATE TABLE run_results
(
   run_result_id smallserial NOT NULL, 
   name character varying(255) NOT NULL,
   is_win smallint NOT NULL,
   CONSTRAINT pk_rr_run_result_run_result_id PRIMARY KEY (run_result_id)
) 
WITH (
  OIDS = FALSE
);

ALTER SEQUENCE run_results_run_result_id_seq
RENAME TO run_results_seq;


ALTER TABLE steam_replays
ADD COLUMN run_result_id smallint;
  
ALTER TABLE steam_replays
ADD CONSTRAINT fk_sr_run_result_id FOREIGN KEY (run_result_id) REFERENCES run_results (run_result_id) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE INDEX idx_sr_run_result_id
  ON steam_replays
  USING btree
  (run_result_id);