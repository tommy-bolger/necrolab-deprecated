-- CREATE EXTENSION IF NOT EXISTS pg_trgm;

-- CREATE INDEX IF NOT EXISTS idx_su_personaname_search
-- ON steam_users 
-- USING GIN (personaname gin_trgm_ops);