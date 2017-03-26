CREATE TABLE leaderboard_ranks
(
  rank integer NOT NULL,
  points double precision NOT NULL,
  CONSTRAINT pk_leaderboard_ranks_rank PRIMARY KEY (rank)
)
WITH (
  OIDS=FALSE
);