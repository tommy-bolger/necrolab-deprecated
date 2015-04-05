-- Leaderboard Snapshots --

ALTER TABLE leaderboard_snapshots RENAME TO leaderboard_snapshots_backup;

CREATE TABLE leaderboard_snapshots (
    leaderboard_snapshot_id INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
    leaderboard_id INTEGER  NOT NULL,
    created TIMESTAMP  NOT NULL,
    [date] DATE NOT NULL,
    updated TIMESTAMP NULL
);

INSERT INTO leaderboard_snapshots
SELECT *
FROM leaderboard_snapshots_backup;

CREATE INDEX IDX_LEADERBOARD_SNAPSHOTS_date ON leaderboard_snapshots (
    date  ASC
);

DROP TABLE leaderboard_snapshots_backup;

-- Power Rankings --

ALTER TABLE power_rankings RENAME TO power_rankings_backup;

CREATE TABLE power_rankings (
    power_ranking_id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
    name VARCHAR(255)  NULL,
    display_name VARCHAR(255)  NULL,
    created TIMESTAMP  NOT NULL,
    latest INTEGER DEFAULT '0' NOT NULL,
    [date] DATE NOT NULL,
    updated TIMESTAMP NULL
);

INSERT INTO power_rankings
SELECT *
FROM power_rankings_backup;

CREATE INDEX IDX_POWER_RANKINGS_date ON power_rankings (
    date  ASC
);

DROP TABLE power_rankings_backup;

-- Daily Rankings --

ALTER TABLE daily_rankings RENAME TO daily_rankings_backup;

CREATE TABLE daily_rankings (
    daily_ranking_id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
    name VARCHAR(255)  NULL,
    display_name VARCHAR(255)  NULL,
    created TIMESTAMP  NOT NULL,
    latest INTEGER DEFAULT '0' NOT NULL,
    [date] DATE NOT NULL,
    updated TIMESTAMP NULL
);

INSERT INTO daily_rankings
SELECT *
FROM daily_rankings_backup;

CREATE INDEX IDX_DAILY_RANKINGS_date ON daily_rankings (
    date  ASC
);

DROP TABLE daily_rankings_backup;

VACUUM;