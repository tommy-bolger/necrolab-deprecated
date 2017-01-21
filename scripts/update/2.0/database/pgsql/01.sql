CREATE INDEX idx_leaderboards_is_dev ON leaderboards USING btree (is_dev);
CREATE INDEX idx_leaderboards_is_prod ON leaderboards USING btree (is_prod);