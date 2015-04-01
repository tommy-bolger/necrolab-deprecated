<?php
    $module_id = db()->insert('cms_modules', array(
        'module_name' => 'necrolab',
        'display_name' => 'Necrolab',
        'sort_order' => $sort_order,
        'enabled' => 1
    ));
?>
PRAGMA foreign_keys=OFF;
-- ----------------------------
-- Records of cms_configuration_parameters
-- ----------------------------
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'version', null, '1.0', 1, 1, null, 'Version', 0);
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'theme', NULL, 'default', 2, 1, NULL, 'Theme', 0);
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'steam_api_key', null, null, 1, 1, null, 'Steam API Key', 0);
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'leaderboard_url', null, 'http://steamcommunity.com/stats/247080/leaderboards/?xml=1', 1, 1, null, 'Leaderboard URL', 0);

CREATE TABLE characters (
    character_id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
    name VARCHAR(100)  NOT NULL,
    display_name VARCHAR(255)  NOT NULL,
    is_active INTEGER DEFAULT '1' NOT NULL,
    is_weighted INTEGER DEFAULT '0' NOT NULL
);

INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('cadence', 'Cadence', 1, 1);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('bard', 'Bard', 1, 1);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('aria', 'Aria', 1, 0);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('bolt', 'Bolt', 1, 0);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('monk', 'Monk', 1, 0);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('dove', 'Dove', 1, 0);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('eli', 'Eli', 1, 0);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('melody', 'Melody', 1, 0);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('dorian', 'Dorian', 1, 0);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('coda', 'Coda', 0, 0);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('ghost', 'Ghost', 0, 0);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('pacifist', 'Pacifist', 0, 0);
INSERT INTO characters (name, display_name, is_active, is_weighted) VALUES ('thief', 'thief', 0, 0);

CREATE TABLE steam_users (
    steam_user_id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
    steamid VARCHAR(255)  NOT NULL,
    communityvisibilitystate INTEGER  NULL,
    profilestate INTEGER  NULL,
    personaname VARCHAR(255)  NULL,
    lastlogoff VARCHAR(255)  NULL,
    profileurl TEXT  NULL,
    avatar TEXT  NULL,
    avatarmedium TEXT  NULL,
    avatarfull TEXT  NULL,
    personastate INTEGER  NULL,
    realname VARCHAR(255)  NULL,
    primaryclanid VARCHAR(255)  NULL,
    timecreated VARCHAR(255)  NULL,
    personastateflags INTEGER  NULL,
    loccountrycode VARCHAR(255)  NULL,
    locstatecode VARCHAR(255)  NULL,
    loccityid INTEGER  NULL,
    updated TIMESTAMP  NULL
);

CREATE TABLE leaderboards (
    leaderboard_id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
    last_snapshot_id INTEGER  NULL,
    name VARCHAR(255)  NULL,
    url TEXT  NULL,
    lbid INTEGER  NULL,
    display_name TEXT  NULL,
    entries INTEGER  NULL,
    sortmethod INTEGER  NULL,
    displaytype INTEGER  NULL,
    onlytrustedwrites INTEGER  NULL,
    onlyfriendsreads INTEGER  NULL,
    character_id INTEGER  NOT NULL,
    is_speedrun INTEGER DEFAULT '0' NOT NULL,
    is_custom INTEGER DEFAULT '0' NOT NULL,
    is_co_op INTEGER DEFAULT '0' NOT NULL,
    is_seeded INTEGER DEFAULT '0' NOT NULL,
    is_daily INTEGER DEFAULT '0' NOT NULL,
    daily_date DATE  NULL,
    is_score_run INTEGER DEFAULT '0' NOT NULL,
    is_all_character INTEGER DEFAULT '0' NOT NULL,
    is_deathless INTEGER DEFAULT '0' NOT NULL,
    is_story_mode INTEGER DEFAULT '0' NOT NULL,
    FOREIGN KEY (last_snapshot_id) REFERENCES leaderboard_snapshots (leaderboard_snapshot_id),
    FOREIGN KEY (character_id) REFERENCES characters (character_id)
);

CREATE TABLE leaderboard_snapshots (
    leaderboard_snapshot_id INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
    leaderboard_id INTEGER  NOT NULL,
    created TIMESTAMP  NOT NULL,
    FOREIGN KEY (leaderboard_id) REFERENCES leaderboards (leaderboard_id)
);

CREATE TABLE leaderboard_entries (
    leaderboard_entry_id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
    leaderboard_id INTEGER  NOT NULL,
    leaderboard_snapshot_id INTEGER  NOT NULL,
    steam_user_id INTEGER  NOT NULL,
    score INTEGER  NOT NULL,
    rank INTEGER  NOT NULL,
    ugcid VARCHAR(255)  NOT NULL,
    details VARCHAR(255)  NOT NULL,
    time INTEGER  NULL,
    is_win INTEGER DEFAULT '0' NOT NULL,
    FOREIGN KEY (leaderboard_id) REFERENCES leaderboards (leaderboard_id),
    FOREIGN KEY (leaderboard_snapshot_id) REFERENCES leaderboard_snapshots (leaderboard_snapshot_id),
    FOREIGN KEY (steam_user_id) REFERENCES steam_users (steam_user_id)
);

CREATE TABLE power_rankings (
    power_ranking_id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
    name VARCHAR(255)  NULL,
    display_name VARCHAR(255)  NULL,
    created TIMESTAMP  NOT NULL,
    latest INTEGER DEFAULT '0' NOT NULL
);

CREATE TABLE power_ranking_leaderboard_snapshots (
    power_ranking_leaderboard_snapshot_id INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
    power_ranking_id INTEGER  NOT NULL,
    leaderboard_snapshot_id INTEGER  NOT NULL,
    FOREIGN KEY (power_ranking_id) REFERENCES power_rankings (power_ranking_id),
    FOREIGN KEY (leaderboard_snapshot_id) REFERENCES leaderboard_snapshots (leaderboard_snapshot_id)
);

CREATE TABLE power_ranking_entries (
    power_ranking_entry_id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
    power_ranking_id INTEGER  NOT NULL,
    steam_user_id INTEGER  NOT NULL,
    rank INTEGER  NOT NULL,
    cadence_speed_rank INTEGER  NULL,
    bard_speed_rank INTEGER  NULL,
    monk_speed_rank INTEGER  NULL,
    aria_speed_rank INTEGER  NULL,
    bolt_speed_rank INTEGER  NULL,
    dove_speed_rank INTEGER  NULL,
    eli_speed_rank INTEGER  NULL,
    melody_speed_rank INTEGER  NULL,
    dorian_speed_rank INTEGER  NULL,
    coda_speed_rank INTEGER  NULL,
    cadence_score_rank INTEGER  NULL,
    bard_score_rank INTEGER  NULL,
    monk_score_rank INTEGER  NULL,
    aria_score_rank INTEGER  NULL,
    bolt_score_rank INTEGER  NULL,
    dove_score_rank INTEGER  NULL,
    eli_score_rank INTEGER  NULL,
    melody_score_rank INTEGER  NULL,
    dorian_score_rank INTEGER  NULL,
    coda_score_rank INTEGER  NULL,
    speed_total INTEGER  NULL,
    score_total INTEGER  NULL,
    base INTEGER  NULL,
    weighted INTEGER  NULL,
    top_10_bonus INTEGER  NULL,
    cadence_speed_leaderboard_entry_id INTEGER  NULL,
    bard_speed_leaderboard_entry_id INTEGER  NULL,
    monk_speed_leaderboard_entry_id INTEGER  NULL,
    aria_speed_leaderboard_entry_id INTEGER  NULL,
    bolt_speed_leaderboard_entry_id INTEGER  NULL,
    dove_speed_leaderboard_entry_id INTEGER  NULL,
    eli_speed_leaderboard_entry_id INTEGER  NULL,
    melody_speed_leaderboard_entry_id INTEGER  NULL,
    dorian_speed_leaderboard_entry_id INTEGER  NULL,
    coda_speed_leaderboard_entry_id INTEGER  NULL,
    cadence_score_leaderboard_entry_id INTEGER  NULL,
    bard_score_leaderboard_entry_id INTEGER  NULL,
    monk_score_leaderboard_entry_id INTEGER  NULL,
    aria_score_leaderboard_entry_id INTEGER  NULL,
    bolt_score_leaderboard_entry_id INTEGER  NULL,
    dove_score_leaderboard_entry_id INTEGER  NULL,
    eli_score_leaderboard_entry_id INTEGER  NULL,
    melody_score_leaderboard_entry_id INTEGER  NULL,
    dorian_score_leaderboard_entry_id INTEGER  NULL,
    coda_score_leaderboard_entry_id INTEGER  NULL,
    cadence_speed_time INTEGER  NULL,
    bard_speed_time INTEGER  NULL,
    monk_speed_time INTEGER  NULL,
    aria_speed_time INTEGER  NULL,
    bolt_speed_time INTEGER  NULL,
    dove_speed_time INTEGER  NULL,
    eli_speed_time INTEGER  NULL,
    melody_speed_time INTEGER  NULL,
    dorian_speed_time INTEGER  NULL,
    coda_speed_time INTEGER  NULL,
    cadence_score INTEGER  NULL,
    bard_score INTEGER  NULL,
    monk_score INTEGER  NULL,
    aria_score INTEGER  NULL,
    bolt_score INTEGER  NULL,
    dove_score INTEGER  NULL,
    eli_score INTEGER  NULL,
    melody_score INTEGER  NULL,
    dorian_score INTEGER  NULL,
    coda_score INTEGER  NULL,
    FOREIGN KEY (power_ranking_id) REFERENCES power_rankings (power_ranking_id),
    FOREIGN KEY (steam_user_id) REFERENCES steam_users (steam_user_id),
    FOREIGN KEY (cadence_speed_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (bard_speed_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (monk_speed_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (aria_speed_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (bolt_speed_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (dove_speed_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (eli_speed_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (melody_speed_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (dorian_speed_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (coda_speed_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (cadence_score_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (bard_score_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (monk_score_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (aria_score_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (bolt_score_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (dove_score_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (eli_score_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (melody_score_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (dorian_score_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id),
    FOREIGN KEY (coda_score_leaderboard_entry_id) REFERENCES leaderboard_entries (leaderboard_entry_id)
);

CREATE TABLE daily_rankings (
    daily_ranking_id INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255)  NULL,
    display_name VARCHAR(255)  NULL,
    created TIMESTAMP  NOT NULL,
    latest INTEGER DEFAULT '0' NOT NULL
);

CREATE TABLE daily_ranking_leaderboard_snapshots (
    daily_ranking_leaderboard_snapshot_id INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
    daily_ranking_id INTEGER  NOT NULL,
    leaderboard_snapshot_id INTEGER  NOT NULL,
    FOREIGN KEY (daily_ranking_id) REFERENCES daily_rankings (daily_ranking_id),
    FOREIGN KEY (leaderboard_snapshot_id) REFERENCES leaderboard_snapshots (leaderboard_snapshot_id)
);

CREATE TABLE daily_ranking_entries (
    daily_ranking_entry_id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
    daily_ranking_id INTEGER  NOT NULL,
    steam_user_id INTEGER  NOT NULL,
    first_place_ranks INTEGER DEFAULT '0' NOT NULL,
    top_5_ranks INTEGER DEFAULT '0' NOT NULL,
    top_10_ranks INTEGER DEFAULT '0' NOT NULL,
    top_20_ranks INTEGER DEFAULT '0' NOT NULL,
    top_50_ranks INTEGER DEFAULT '0' NOT NULL,
    top_100_ranks INTEGER DEFAULT '0' NOT NULL,
    total_points INTEGER DEFAULT '0' NOT NULL,
    points_per_day INTEGER DEFAULT '0' NOT NULL,
    total_dailies INTEGER DEFAULT '0' NOT NULL,
    total_wins INTEGER DEFAULT '0' NOT NULL,
    average_place INTEGER DEFAULT '0' NOT NULL,
    sum_of_ranks INTEGER DEFAULT '0' NOT NULL,
    number_of_ranks INTEGER DEFAULT '0' NOT NULL,
    rank INTEGER  NOT NULL,
    FOREIGN KEY (daily_ranking_id) REFERENCES daily_rankings (daily_ranking_id),
    FOREIGN KEY (steam_user_id) REFERENCES steam_users (steam_user_id)
);

CREATE INDEX IDX_LEADERBOARD_ENTRIES_leaderboard_id ON leaderboard_entries(
    leaderboard_id  ASC
);

CREATE INDEX IDX_LEADERBOARD_ENTRIES_snapshot_id ON leaderboard_entries(
    leaderboard_snapshot_id  ASC
);

CREATE INDEX IDX_LEADERBOARD_ENTRIES_steam_user_id ON leaderboard_entries(
    steam_user_id  ASC
);

CREATE INDEX IDX_LEADERBOARD_ENTRIES_score ON leaderboard_entries(
    score  ASC
);

CREATE INDEX IDX_LEADERBOARD_ENTRIES_rank ON leaderboard_entries(
    rank  ASC
);

CREATE INDEX IDX_LEADERBOARD_ENTRIES_time ON leaderboard_entries(
    time  ASC
);

CREATE UNIQUE INDEX IDX_LEADERBOARD_ENTRIES_leaderboard_entry_id ON leaderboard_entries(
    leaderboard_entry_id  ASC
);

CREATE INDEX IDX_LEADERBOARD_SNAPSHOTS_leaderboard_id ON leaderboard_snapshots(
    leaderboard_id  ASC
);

CREATE UNIQUE INDEX IDX_LEADERBOARD_SNAPSHOTS_leaderboard_snapshot_id ON leaderboard_snapshots(
    leaderboard_snapshot_id  ASC
);

CREATE INDEX IDX_LEADERBOARDS_last_snapshot_id ON leaderboards(
    last_snapshot_id  ASC
);

CREATE INDEX IDX_LEADERBOARDS_name ON leaderboards(
    name  ASC
);

CREATE INDEX IDX_LEADERBOARDS_characer_id ON leaderboards(
    character_id  ASC
);

CREATE INDEX IDX_LEADERBOARDS_is_speedrun ON leaderboards(
    is_speedrun  ASC
);

CREATE INDEX IDX_LEADERBOARDS_is_custom ON leaderboards(
    is_custom  ASC
);

CREATE INDEX IDX_LEADERBOARDS_is_co_op ON leaderboards(
    is_co_op  ASC
);

CREATE INDEX IDX_LEADERBOARDS_is_seeded ON leaderboards(
    is_seeded  ASC
);

CREATE INDEX IDX_LEADERBOARDS_is_daily ON leaderboards(
    is_daily  ASC
);

CREATE INDEX IDX_LEADERBOARDS_daily_date ON leaderboards(
    daily_date  ASC
);

CREATE INDEX IDX_LEADERBOARDS_is_score_run ON leaderboards(
    is_score_run  ASC
);

CREATE INDEX IDX_LEADERBOARDS_is_all_character ON leaderboards(
    is_all_character  ASC
);

CREATE INDEX IDX_LEADERBOARDS_is_deathless ON leaderboards(
    is_deathless  ASC
);

CREATE INDEX IDX_LEADERBOARDS_is_story_mode ON leaderboards(
    is_story_mode  ASC
);

CREATE UNIQUE INDEX IDX_LEADERBOARDS_lbid ON leaderboards(
    lbid  ASC
);

CREATE UNIQUE INDEX IDX_LEADERBOARDS_leaderboard_id ON leaderboards(
    leaderboard_id  ASC
);

CREATE INDEX IDX_POWER_RANKINGS_ranking_id ON power_rankings(
    power_ranking_id  ASC
);

CREATE INDEX IDX_POWER_RANKINGS_name ON power_rankings(
    name  ASC
);

CREATE INDEX IDX_POWER_RANKINGS_latest ON power_rankings(
    latest  ASC
);

CREATE INDEX IDX_POWER_RANKINGS_LEADERBOARD_SNAPSHOTS_power_ranking_id ON power_ranking_leaderboard_snapshots(
    power_ranking_id  ASC
);

CREATE INDEX IDX_POWER_RANKINGS_LEADERBOARD_SNAPSHOTS_leaderboard_snapshot_id ON power_ranking_leaderboard_snapshots(
    leaderboard_snapshot_id  ASC
);

CREATE INDEX IDX_POWER_RANKING_ENTRIES_rank ON power_ranking_entries(
    rank  ASC
)

CREATE INDEX IDX_POWER_RANKING_ENTRIES_power_ranking_id ON power_ranking_entries(
    power_ranking_id  ASC
)

CREATE INDEX IDX_POWER_RANKING_ENTRIES_steam_user_id ON power_ranking_entries(
    steam_user_id  ASC
)

CREATE INDEX IDX_POWER_RANKING_ENTRIES_weighted_asc ON power_ranking_entries(
    weighted  ASC
)

CREATE INDEX IDX_POWER_RANKING_ENTRIES_weighted_desc ON power_ranking_entries(
    weighted  DESC
)

CREATE UNIQUE INDEX IDX_STEAM_USERS_steam_user_id ON steam_users(
    steam_user_id  ASC
);

CREATE INDEX IDX_STEAM_USERS_steamid ON steam_users(
    steamid  ASC
);

CREATE INDEX IDX_STEAM_USERS_personaname ON steam_users(
    personaname  ASC
);

CREATE INDEX IDX_STEAM_USERS_updated ON steam_users (
    updated  ASC
);

CREATE INDEX IDX_DAILY_RANKINGS_latest ON daily_rankings (
    latest  ASC
);

CREATE INDEX IDX_DAILY_RANKING_LEADERBOARD_SNAPSHOTS_daily_ranking_id ON daily_ranking_leaderboard_snapshots (
    daily_ranking_id  ASC
);

CREATE INDEX IDX_DAILY_RANKING_LEADERBOARD_SNAPSHOTS_leaderboard_snapshot_id ON daily_ranking_leaderboard_snapshots (
    leaderboard_snapshot_id  ASC
);

CREATE INDEX IDX_DAILY_RANKING_ENTRIES_daily_ranking_id ON daily_ranking_entries (
    daily_ranking_id  ASC
);

CREATE INDEX IDX_DAILY_RANKING_ENTRIES_steam_user_id ON daily_ranking_entries (
    steam_user_id  ASC
);

CREATE INDEX IDX_DAILY_RANKING_ENTRIES_rank_asc ON daily_ranking_entries (
    rank  ASC
);

CREATE INDEX IDX_DAILY_RANKING_ENTRIES_rank_desc ON daily_ranking_entries (
    rank  DESC
);