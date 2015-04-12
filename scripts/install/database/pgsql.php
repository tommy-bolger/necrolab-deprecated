<?php
    $module_id = db()->insert('cms_modules', array(
        'module_name' => 'necrolab',
        'display_name' => 'Necrolab',
        'sort_order' => $sort_order,
        'enabled' => 1
    ));
?>
-- Set module configuration parameters
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'version', NULL, '1.0', 1, 1, NULL, 'Version', 0);
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'theme', NULL, 'default', 2, 1, NULL, 'Theme', 0);
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'steam_api_key', NULL, NULL, 1, 1, NULL, 'Steam API Key', 0);
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'leaderboard_url', NULL, 'http://steamcommunity.com/stats/247080/leaderboards/?xml=1', 1, 1, NULL, 'Leaderboard URL', 0);


SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

--
-- TOC entry 173 (class 1259 OID 16405)
-- Name: characters; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE characters (
    character_id smallint NOT NULL,
    name character varying(100) NOT NULL,
    display_name character varying(255) NOT NULL,
    is_active smallint DEFAULT 1 NOT NULL,
    is_weighted smallint DEFAULT 0 NOT NULL
);

--
-- TOC entry 172 (class 1259 OID 16403)
-- Name: characters_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE characters_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2161 (class 0 OID 0)
-- Dependencies: 172
-- Name: characters_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE characters_seq OWNED BY characters.character_id;


--
-- TOC entry 191 (class 1259 OID 16683)
-- Name: daily_ranking_entries; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE daily_ranking_entries (
    daily_ranking_entry_id bigint NOT NULL,
    daily_ranking_id integer NOT NULL,
    steam_user_id integer NOT NULL,
    first_place_ranks smallint DEFAULT 0 NOT NULL,
    top_5_ranks smallint DEFAULT 0 NOT NULL,
    top_10_ranks smallint DEFAULT 0 NOT NULL,
    top_20_ranks smallint DEFAULT 0 NOT NULL,
    top_50_ranks smallint DEFAULT 0 NOT NULL,
    top_100_ranks smallint DEFAULT 0 NOT NULL,
    total_points smallint DEFAULT 0 NOT NULL,
    points_per_day smallint DEFAULT 0 NOT NULL,
    total_dailies smallint DEFAULT 0 NOT NULL,
    total_wins smallint DEFAULT 0 NOT NULL,
    average_place smallint DEFAULT 0 NOT NULL,
    sum_of_ranks integer DEFAULT 0 NOT NULL,
    number_of_ranks smallint DEFAULT 0 NOT NULL,
    rank integer NOT NULL
);

--
-- TOC entry 190 (class 1259 OID 16681)
-- Name: daily_ranking_entries_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE daily_ranking_entries_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2162 (class 0 OID 0)
-- Dependencies: 190
-- Name: daily_ranking_entries_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE daily_ranking_entries_seq OWNED BY daily_ranking_entries.daily_ranking_entry_id;


--
-- TOC entry 189 (class 1259 OID 16662)
-- Name: daily_ranking_leaderboard_snapshots; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE daily_ranking_leaderboard_snapshots (
    daily_ranking_leaderboard_snapshot_id bigint NOT NULL,
    daily_ranking_id integer NOT NULL,
    leaderboard_snapshot_id integer NOT NULL
);

--
-- TOC entry 188 (class 1259 OID 16660)
-- Name: daily_ranking_leaderboard_snapshots_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE daily_ranking_leaderboard_snapshots_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2163 (class 0 OID 0)
-- Dependencies: 188
-- Name: daily_ranking_leaderboard_snapshots_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE daily_ranking_leaderboard_snapshots_seq OWNED BY daily_ranking_leaderboard_snapshots.daily_ranking_leaderboard_snapshot_id;


--
-- TOC entry 187 (class 1259 OID 16648)
-- Name: daily_rankings; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE daily_rankings (
    daily_ranking_id integer NOT NULL,
    created timestamp without time zone NOT NULL,
    latest smallint DEFAULT 0 NOT NULL,
    date date NOT NULL,
    updated timestamp without time zone
);

--
-- TOC entry 186 (class 1259 OID 16646)
-- Name: daily_rankings_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE daily_rankings_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2164 (class 0 OID 0)
-- Dependencies: 186
-- Name: daily_rankings_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE daily_rankings_seq OWNED BY daily_rankings.daily_ranking_id;


--
-- TOC entry 179 (class 1259 OID 16465)
-- Name: leaderboard_entries; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE leaderboard_entries (
    leaderboard_entry_id bigint NOT NULL,
    leaderboard_id integer NOT NULL,
    leaderboard_snapshot_id integer NOT NULL,
    steam_user_id integer NOT NULL,
    score integer NOT NULL,
    rank integer NOT NULL,
    ugcid character varying(255) NOT NULL,
    details character varying(255) NOT NULL,
    "time" integer,
    is_win smallint DEFAULT 0 NOT NULL
);

--
-- TOC entry 178 (class 1259 OID 16463)
-- Name: leaderboard_entries_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE leaderboard_entries_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2165 (class 0 OID 0)
-- Dependencies: 178
-- Name: leaderboard_entries_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE leaderboard_entries_seq OWNED BY leaderboard_entries.leaderboard_entry_id;


--
-- TOC entry 177 (class 1259 OID 16450)
-- Name: leaderboard_snapshots; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE leaderboard_snapshots (
    leaderboard_snapshot_id integer NOT NULL,
    leaderboard_id integer NOT NULL,
    created timestamp without time zone NOT NULL,
    date date NOT NULL,
    updated timestamp without time zone
);

--
-- TOC entry 176 (class 1259 OID 16448)
-- Name: leaderboard_snapshots_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE leaderboard_snapshots_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2166 (class 0 OID 0)
-- Dependencies: 176
-- Name: leaderboard_snapshots_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE leaderboard_snapshots_seq OWNED BY leaderboard_snapshots.leaderboard_snapshot_id;


--
-- TOC entry 175 (class 1259 OID 16417)
-- Name: leaderboards; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE leaderboards (
    leaderboard_id integer NOT NULL,
    name character varying(255) NOT NULL,
    url text NOT NULL,
    lbid character varying(20) NOT NULL,
    display_name text,
    entries integer,
    sortmethod smallint,
    displaytype smallint,
    onlytrustedwrites smallint,
    onlyfriendsreads smallint,
    character_id smallint NOT NULL,
    is_speedrun smallint DEFAULT 0 NOT NULL,
    is_custom smallint DEFAULT 0 NOT NULL,
    is_co_op smallint DEFAULT 0 NOT NULL,
    is_seeded smallint DEFAULT 0 NOT NULL,
    is_daily smallint DEFAULT 0 NOT NULL,
    daily_date date,
    is_score_run smallint DEFAULT 0 NOT NULL,
    is_all_character smallint DEFAULT 0 NOT NULL,
    is_deathless smallint DEFAULT 0 NOT NULL,
    is_story_mode smallint DEFAULT 0 NOT NULL,
    last_snapshot_id integer
);

--
-- TOC entry 174 (class 1259 OID 16415)
-- Name: leaderboards_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE leaderboards_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2167 (class 0 OID 0)
-- Dependencies: 174
-- Name: leaderboards_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE leaderboards_seq OWNED BY leaderboards.leaderboard_id;


--
-- TOC entry 185 (class 1259 OID 16530)
-- Name: power_ranking_entries; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE power_ranking_entries (
    power_ranking_entry_id bigint NOT NULL,
    power_ranking_id integer NOT NULL,
    steam_user_id integer NOT NULL,
    rank integer NOT NULL,
    cadence_speed_rank integer,
    bard_speed_rank integer,
    monk_speed_rank integer,
    aria_speed_rank integer,
    bolt_speed_rank integer,
    dove_speed_rank integer,
    eli_speed_rank integer,
    melody_speed_rank integer,
    dorian_speed_rank integer,
    coda_speed_rank integer,
    cadence_score_rank integer,
    bard_score_rank integer,
    monk_score_rank integer,
    aria_score_rank integer,
    bolt_score_rank integer,
    dove_score_rank integer,
    eli_score_rank integer,
    melody_score_rank integer,
    dorian_score_rank integer,
    coda_score_rank integer,
    speed_total integer,
    score_total integer,
    base integer,
    weighted integer,
    top_10_bonus integer,
    cadence_speed_leaderboard_entry_id integer,
    bard_speed_leaderboard_entry_id integer,
    monk_speed_leaderboard_entry_id integer,
    aria_speed_leaderboard_entry_id integer,
    bolt_speed_leaderboard_entry_id integer,
    dove_speed_leaderboard_entry_id integer,
    eli_speed_leaderboard_entry_id integer,
    melody_speed_leaderboard_entry_id integer,
    dorian_speed_leaderboard_entry_id integer,
    coda_speed_leaderboard_entry_id integer,
    cadence_score_leaderboard_entry_id integer,
    bard_score_leaderboard_entry_id integer,
    monk_score_leaderboard_entry_id integer,
    aria_score_leaderboard_entry_id integer,
    bolt_score_leaderboard_entry_id integer,
    dove_score_leaderboard_entry_id integer,
    eli_score_leaderboard_entry_id integer,
    melody_score_leaderboard_entry_id integer,
    dorian_score_leaderboard_entry_id integer,
    coda_score_leaderboard_entry_id integer,
    cadence_speed_time integer,
    bard_speed_time integer,
    monk_speed_time integer,
    aria_speed_time integer,
    bolt_speed_time integer,
    dove_speed_time integer,
    eli_speed_time integer,
    melody_speed_time integer,
    dorian_speed_time integer,
    coda_speed_time integer,
    cadence_score integer,
    bard_score integer,
    monk_score integer,
    aria_score integer,
    bolt_score integer,
    dove_score integer,
    eli_score integer,
    melody_score integer,
    dorian_score integer,
    coda_score integer
);

--
-- TOC entry 184 (class 1259 OID 16528)
-- Name: power_ranking_entries_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE power_ranking_entries_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2168 (class 0 OID 0)
-- Dependencies: 184
-- Name: power_ranking_entries_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE power_ranking_entries_seq OWNED BY power_ranking_entries.power_ranking_entry_id;


--
-- TOC entry 183 (class 1259 OID 16510)
-- Name: power_ranking_leaderboard_snapshots; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE power_ranking_leaderboard_snapshots (
    power_ranking_leaderboard_snapshot_id bigint NOT NULL,
    power_ranking_id integer NOT NULL,
    leaderboard_snapshot_id integer NOT NULL
);

--
-- TOC entry 182 (class 1259 OID 16508)
-- Name: power_ranking_leaderboard_snapshots_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE power_ranking_leaderboard_snapshots_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2169 (class 0 OID 0)
-- Dependencies: 182
-- Name: power_ranking_leaderboard_snapshots_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE power_ranking_leaderboard_snapshots_seq OWNED BY power_ranking_leaderboard_snapshots.power_ranking_leaderboard_snapshot_id;


--
-- TOC entry 181 (class 1259 OID 16498)
-- Name: power_rankings; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE power_rankings (
    power_ranking_id integer NOT NULL,
    created timestamp without time zone NOT NULL,
    latest smallint DEFAULT 0 NOT NULL,
    date date NOT NULL,
    updated timestamp without time zone
);

--
-- TOC entry 180 (class 1259 OID 16496)
-- Name: power_rankings_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE power_rankings_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2170 (class 0 OID 0)
-- Dependencies: 180
-- Name: power_rankings_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE power_rankings_seq OWNED BY power_rankings.power_ranking_id;


--
-- TOC entry 170 (class 1259 OID 16386)
-- Name: steam_users; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE steam_users (
    steam_user_id integer NOT NULL,
    steamid character varying(255) NOT NULL,
    communityvisibilitystate smallint,
    profilestate smallint,
    personaname character varying(255),
    lastlogoff character varying(255),
    profileurl text,
    avatar text,
    avatarmedium text,
    avatarfull text,
    personastate smallint,
    realname character varying(255),
    primaryclanid character varying(255),
    timecreated character varying(255),
    personastateflags smallint,
    loccountrycode character varying(255),
    locstatecode character varying(255),
    loccityid integer,
    updated timestamp without time zone,
    twitch_username character varying(255),
    twitter_username character varying(255),
    website text
);

--
-- TOC entry 171 (class 1259 OID 16389)
-- Name: steam_users_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE steam_users_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- TOC entry 2171 (class 0 OID 0)
-- Dependencies: 171
-- Name: steam_users_seq; Type: SEQUENCE OWNED BY; Schema: public; 
--

ALTER SEQUENCE steam_users_seq OWNED BY steam_users.steam_user_id;


--
-- TOC entry 1891 (class 2604 OID 16408)
-- Name: character_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY characters ALTER COLUMN character_id SET DEFAULT nextval('characters_seq'::regclass);


--
-- TOC entry 1914 (class 2604 OID 16686)
-- Name: daily_ranking_entry_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY daily_ranking_entries ALTER COLUMN daily_ranking_entry_id SET DEFAULT nextval('daily_ranking_entries_seq'::regclass);


--
-- TOC entry 1913 (class 2604 OID 16665)
-- Name: daily_ranking_leaderboard_snapshot_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY daily_ranking_leaderboard_snapshots ALTER COLUMN daily_ranking_leaderboard_snapshot_id SET DEFAULT nextval('daily_ranking_leaderboard_snapshots_seq'::regclass);


--
-- TOC entry 1911 (class 2604 OID 16651)
-- Name: daily_ranking_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY daily_rankings ALTER COLUMN daily_ranking_id SET DEFAULT nextval('daily_rankings_seq'::regclass);


--
-- TOC entry 1905 (class 2604 OID 16468)
-- Name: leaderboard_entry_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY leaderboard_entries ALTER COLUMN leaderboard_entry_id SET DEFAULT nextval('leaderboard_entries_seq'::regclass);


--
-- TOC entry 1904 (class 2604 OID 16453)
-- Name: leaderboard_snapshot_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY leaderboard_snapshots ALTER COLUMN leaderboard_snapshot_id SET DEFAULT nextval('leaderboard_snapshots_seq'::regclass);


--
-- TOC entry 1894 (class 2604 OID 16420)
-- Name: leaderboard_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY leaderboards ALTER COLUMN leaderboard_id SET DEFAULT nextval('leaderboards_seq'::regclass);


--
-- TOC entry 1910 (class 2604 OID 16533)
-- Name: power_ranking_entry_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries ALTER COLUMN power_ranking_entry_id SET DEFAULT nextval('power_ranking_entries_seq'::regclass);


--
-- TOC entry 1909 (class 2604 OID 16513)
-- Name: power_ranking_leaderboard_snapshot_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_leaderboard_snapshots ALTER COLUMN power_ranking_leaderboard_snapshot_id SET DEFAULT nextval('power_ranking_leaderboard_snapshots_seq'::regclass);


--
-- TOC entry 1907 (class 2604 OID 16501)
-- Name: power_ranking_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY power_rankings ALTER COLUMN power_ranking_id SET DEFAULT nextval('power_rankings_seq'::regclass);


--
-- TOC entry 1890 (class 2604 OID 16391)
-- Name: steam_user_id; Type: DEFAULT; Schema: public; 
--

ALTER TABLE ONLY steam_users ALTER COLUMN steam_user_id SET DEFAULT nextval('steam_users_seq'::regclass);


--
-- TOC entry 2172 (class 0 OID 0)
-- Dependencies: 172
-- Name: characters_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('characters_seq', 11, false);


--
-- TOC entry 2173 (class 0 OID 0)
-- Dependencies: 190
-- Name: daily_ranking_entries_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('daily_ranking_entries_seq', 1, false);


--
-- TOC entry 2174 (class 0 OID 0)
-- Dependencies: 188
-- Name: daily_ranking_leaderboard_snapshots_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('daily_ranking_leaderboard_snapshots_seq', 1, false);

--
-- TOC entry 2175 (class 0 OID 0)
-- Dependencies: 186
-- Name: daily_rankings_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('daily_rankings_seq', 1, false);


--
-- TOC entry 2176 (class 0 OID 0)
-- Dependencies: 178
-- Name: leaderboard_entries_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('leaderboard_entries_seq', 1, false);

--
-- TOC entry 2177 (class 0 OID 0)
-- Dependencies: 176
-- Name: leaderboard_snapshots_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('leaderboard_snapshots_seq', 1, false);

--
-- TOC entry 2178 (class 0 OID 0)
-- Dependencies: 174
-- Name: leaderboards_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('leaderboards_seq', 1, false);


--
-- TOC entry 2179 (class 0 OID 0)
-- Dependencies: 184
-- Name: power_ranking_entries_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('power_ranking_entries_seq', 1, false);


--
-- TOC entry 2180 (class 0 OID 0)
-- Dependencies: 182
-- Name: power_ranking_leaderboard_snapshots_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('power_ranking_leaderboard_snapshots_seq', 1, false);


--
-- TOC entry 2181 (class 0 OID 0)
-- Dependencies: 180
-- Name: power_rankings_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('power_rankings_seq', 1, false);


--
-- TOC entry 2182 (class 0 OID 0)
-- Dependencies: 171
-- Name: steam_users_seq; Type: SEQUENCE SET; Schema: public; 
--

SELECT pg_catalog.setval('steam_users_seq', 1, false);


--
-- TOC entry 1934 (class 2606 OID 16412)
-- Name: pk_characters_character_id; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY characters
    ADD CONSTRAINT pk_characters_character_id PRIMARY KEY (character_id);


--
-- TOC entry 1989 (class 2606 OID 16701)
-- Name: pk_daily_ranking_entries_daily_ranking_entry_id; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY daily_ranking_entries
    ADD CONSTRAINT pk_daily_ranking_entries_daily_ranking_entry_id PRIMARY KEY (daily_ranking_entry_id);


--
-- TOC entry 1983 (class 2606 OID 16667)
-- Name: pk_daily_ranking_leaderboard_snapshots_daily_ranking_leaderboar; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY daily_ranking_leaderboard_snapshots
    ADD CONSTRAINT pk_daily_ranking_leaderboard_snapshots_daily_ranking_leaderboar PRIMARY KEY (daily_ranking_leaderboard_snapshot_id);


--
-- TOC entry 1977 (class 2606 OID 16654)
-- Name: pk_daily_rankings_daily_ranking_id; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY daily_rankings
    ADD CONSTRAINT pk_daily_rankings_daily_ranking_id PRIMARY KEY (daily_ranking_id);


--
-- TOC entry 1979 (class 2606 OID 16656)
-- Name: pk_daily_rankings_date; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY daily_rankings
    ADD CONSTRAINT pk_daily_rankings_date UNIQUE (date);


--
-- TOC entry 1961 (class 2606 OID 16474)
-- Name: pk_leaderboard_entries_leaderboard_entry_id; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY leaderboard_entries
    ADD CONSTRAINT pk_leaderboard_entries_leaderboard_entry_id PRIMARY KEY (leaderboard_entry_id);


--
-- TOC entry 1953 (class 2606 OID 16455)
-- Name: pk_leaderboard_snapshots_leaderboard_snapshot_id; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY leaderboard_snapshots
    ADD CONSTRAINT pk_leaderboard_snapshots_leaderboard_snapshot_id PRIMARY KEY (leaderboard_snapshot_id);


--
-- TOC entry 1947 (class 2606 OID 16434)
-- Name: pk_leaderboards_leaderboard_id; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY leaderboards
    ADD CONSTRAINT pk_leaderboards_leaderboard_id PRIMARY KEY (leaderboard_id);


--
-- TOC entry 1975 (class 2606 OID 16535)
-- Name: pk_power_ranking_entries_power_ranking_entry_id; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT pk_power_ranking_entries_power_ranking_entry_id PRIMARY KEY (power_ranking_entry_id);


--
-- TOC entry 1969 (class 2606 OID 16515)
-- Name: pk_power_ranking_leaderboard_snapshots_power_ranking_leaderboar; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY power_ranking_leaderboard_snapshots
    ADD CONSTRAINT pk_power_ranking_leaderboard_snapshots_power_ranking_leaderboar PRIMARY KEY (power_ranking_leaderboard_snapshot_id);


--
-- TOC entry 1963 (class 2606 OID 16504)
-- Name: pk_power_rankings_power_ranking_id; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY power_rankings
    ADD CONSTRAINT pk_power_rankings_power_ranking_id PRIMARY KEY (power_ranking_id);


--
-- TOC entry 1932 (class 2606 OID 16414)
-- Name: pk_steam_users_steam_user_id; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY steam_users
    ADD CONSTRAINT pk_steam_users_steam_user_id PRIMARY KEY (steam_user_id);


--
-- TOC entry 1997 (class 2606 OID 17833)
-- Name: uq_steam_users_steamid; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY steam_users
    ADD CONSTRAINT uq_steam_users_steamid UNIQUE (steamid);
    

--
-- TOC entry 1949 (class 2606 OID 16444)
-- Name: uq_leaderboards_lbid; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY leaderboards
    ADD CONSTRAINT uq_leaderboards_lbid UNIQUE (lbid);


--
-- TOC entry 1965 (class 2606 OID 16506)
-- Name: uq_power_rankings_date; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY power_rankings
    ADD CONSTRAINT uq_power_rankings_date UNIQUE (date);


--
-- TOC entry 1984 (class 1259 OID 16724)
-- Name: idx_daily_ranking_entries_daily_ranking_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_daily_ranking_entries_daily_ranking_id ON daily_ranking_entries USING btree (daily_ranking_id);


--
-- TOC entry 1985 (class 1259 OID 16725)
-- Name: idx_daily_ranking_entries_rank_asc; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_daily_ranking_entries_rank_asc ON daily_ranking_entries USING btree (rank);


--
-- TOC entry 1986 (class 1259 OID 16726)
-- Name: idx_daily_ranking_entries_rank_desc; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_daily_ranking_entries_rank_desc ON daily_ranking_entries USING btree (rank DESC);


--
-- TOC entry 1987 (class 1259 OID 16727)
-- Name: idx_daily_ranking_entries_steam_user_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_daily_ranking_entries_steam_user_id ON daily_ranking_entries USING btree (steam_user_id);


--
-- TOC entry 1980 (class 1259 OID 16680)
-- Name: idx_daily_ranking_leaderboard_snapshots_daily_ranking_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_daily_ranking_leaderboard_snapshots_daily_ranking_id ON daily_ranking_leaderboard_snapshots USING btree (daily_ranking_id);


--
-- TOC entry 1981 (class 1259 OID 16679)
-- Name: idx_daily_ranking_leaderboard_snapshots_leaderboard_snapshot_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_daily_ranking_leaderboard_snapshots_leaderboard_snapshot_id ON daily_ranking_leaderboard_snapshots USING btree (leaderboard_snapshot_id);


--
-- TOC entry 1954 (class 1259 OID 16490)
-- Name: idx_leaderboard_entries_leaderboard_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboard_entries_leaderboard_id ON leaderboard_entries USING btree (leaderboard_id);


--
-- TOC entry 1955 (class 1259 OID 16493)
-- Name: idx_leaderboard_entries_leaderboard_snapshot_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboard_entries_leaderboard_snapshot_id ON leaderboard_entries USING btree (leaderboard_snapshot_id);


--
-- TOC entry 1956 (class 1259 OID 16491)
-- Name: idx_leaderboard_entries_rank; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboard_entries_rank ON leaderboard_entries USING btree (rank);


--
-- TOC entry 1957 (class 1259 OID 16492)
-- Name: idx_leaderboard_entries_score; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboard_entries_score ON leaderboard_entries USING btree (score);


--
-- TOC entry 1958 (class 1259 OID 16494)
-- Name: idx_leaderboard_entries_steam_user_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboard_entries_steam_user_id ON leaderboard_entries USING btree (steam_user_id);


--
-- TOC entry 1959 (class 1259 OID 16495)
-- Name: idx_leaderboard_entries_time; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboard_entries_time ON leaderboard_entries USING btree ("time");


--
-- TOC entry 1950 (class 1259 OID 16462)
-- Name: idx_leaderboard_snapshots_date; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboard_snapshots_date ON leaderboard_snapshots USING btree (date);


--
-- TOC entry 1951 (class 1259 OID 16461)
-- Name: idx_leaderboard_snapshots_leaderboard_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboard_snapshots_leaderboard_id ON leaderboard_snapshots USING btree (leaderboard_id);


--
-- TOC entry 1935 (class 1259 OID 16435)
-- Name: idx_leaderboards_character_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_character_id ON leaderboards USING btree (character_id);


--
-- TOC entry 1936 (class 1259 OID 16436)
-- Name: idx_leaderboards_daily_date; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_daily_date ON leaderboards USING btree (daily_date);


--
-- TOC entry 1937 (class 1259 OID 16445)
-- Name: idx_leaderboards_is_all_character; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_is_all_character ON leaderboards USING btree (is_all_character);


--
-- TOC entry 1938 (class 1259 OID 16437)
-- Name: idx_leaderboards_is_co_op; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_is_co_op ON leaderboards USING btree (is_co_op);


--
-- TOC entry 1939 (class 1259 OID 16438)
-- Name: idx_leaderboards_is_custom; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_is_custom ON leaderboards USING btree (is_custom);


--
-- TOC entry 1940 (class 1259 OID 16439)
-- Name: idx_leaderboards_is_daily; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_is_daily ON leaderboards USING btree (is_daily);


--
-- TOC entry 1941 (class 1259 OID 16446)
-- Name: idx_leaderboards_is_deathless; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_is_deathless ON leaderboards USING btree (is_deathless);


--
-- TOC entry 1942 (class 1259 OID 16440)
-- Name: idx_leaderboards_is_score_run; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_is_score_run ON leaderboards USING btree (is_score_run);


--
-- TOC entry 1943 (class 1259 OID 16441)
-- Name: idx_leaderboards_is_seeded; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_is_seeded ON leaderboards USING btree (is_seeded);


--
-- TOC entry 1944 (class 1259 OID 16442)
-- Name: idx_leaderboards_is_speedrun; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_is_speedrun ON leaderboards USING btree (is_speedrun);


--
-- TOC entry 1945 (class 1259 OID 16447)
-- Name: idx_leaderboards_is_story_mode; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_leaderboards_is_story_mode ON leaderboards USING btree (is_story_mode);


--
-- TOC entry 1970 (class 1259 OID 16718)
-- Name: idx_power_ranking_entries_power_ranking_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_power_ranking_entries_power_ranking_id ON power_ranking_entries USING btree (power_ranking_id);


--
-- TOC entry 1971 (class 1259 OID 16721)
-- Name: idx_power_ranking_entries_rank_asc; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_power_ranking_entries_rank_asc ON power_ranking_entries USING btree (rank);


--
-- TOC entry 1972 (class 1259 OID 16722)
-- Name: idx_power_ranking_entries_rank_desc; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_power_ranking_entries_rank_desc ON power_ranking_entries USING btree (rank DESC);


--
-- TOC entry 1973 (class 1259 OID 16723)
-- Name: idx_power_ranking_entries_steam_user_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_power_ranking_entries_steam_user_id ON power_ranking_entries USING btree (steam_user_id);


--
-- TOC entry 1966 (class 1259 OID 16527)
-- Name: idx_power_ranking_leaderboard_snapshots_leaderboard_snapshot_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_power_ranking_leaderboard_snapshots_leaderboard_snapshot_id ON power_ranking_leaderboard_snapshots USING btree (leaderboard_snapshot_id);


--
-- TOC entry 1967 (class 1259 OID 16526)
-- Name: idx_power_ranking_leaderboard_snapshots_power_ranking_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_power_ranking_leaderboard_snapshots_power_ranking_id ON power_ranking_leaderboard_snapshots USING btree (power_ranking_id);


--
-- TOC entry 1928 (class 1259 OID 16401)
-- Name: idx_steam_users_personaname; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_steam_users_personaname ON steam_users USING btree (personaname);


--
-- TOC entry 1929 (class 1259 OID 16400)
-- Name: idx_steam_users_steamid; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_steam_users_steamid ON steam_users USING btree (steamid);


--
-- TOC entry 1930 (class 1259 OID 16402)
-- Name: idx_steam_users_updated; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX idx_steam_users_updated ON steam_users USING btree (updated);


--
-- TOC entry 2020 (class 2606 OID 16673)
-- Name: daily_ranking_leaderboard_snapshots_leaderboard_snapshot_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY daily_ranking_leaderboard_snapshots
    ADD CONSTRAINT fk_daily_ranking_leaderboard_snapshots_leaderboard_snapshot_id FOREIGN KEY (leaderboard_snapshot_id) REFERENCES leaderboard_snapshots(leaderboard_snapshot_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2021 (class 2606 OID 16702)
-- Name: fk_daily_ranking_entries_daily_ranking_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY daily_ranking_entries
    ADD CONSTRAINT fk_daily_ranking_entries_daily_ranking_id FOREIGN KEY (daily_ranking_id) REFERENCES daily_rankings(daily_ranking_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2022 (class 2606 OID 16707)
-- Name: fk_daily_ranking_entries_steam_user_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY daily_ranking_entries
    ADD CONSTRAINT fk_daily_ranking_entries_steam_user_id FOREIGN KEY (steam_user_id) REFERENCES steam_users(steam_user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2019 (class 2606 OID 16668)
-- Name: fk_daily_ranking_leaderboard_snapshots_daily_ranking_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY daily_ranking_leaderboard_snapshots
    ADD CONSTRAINT fk_daily_ranking_leaderboard_snapshots_daily_ranking_id FOREIGN KEY (daily_ranking_id) REFERENCES daily_rankings(daily_ranking_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1992 (class 2606 OID 16475)
-- Name: fk_leaderboard_entries_leaderboard_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY leaderboard_entries
    ADD CONSTRAINT fk_leaderboard_entries_leaderboard_id FOREIGN KEY (leaderboard_id) REFERENCES leaderboards(leaderboard_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1993 (class 2606 OID 16480)
-- Name: fk_leaderboard_entries_leaderboard_snapshot_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY leaderboard_entries
    ADD CONSTRAINT fk_leaderboard_entries_leaderboard_snapshot_id FOREIGN KEY (leaderboard_snapshot_id) REFERENCES leaderboard_snapshots(leaderboard_snapshot_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1994 (class 2606 OID 16485)
-- Name: fk_leaderboard_entries_steam_user_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY leaderboard_entries
    ADD CONSTRAINT fk_leaderboard_entries_steam_user_id FOREIGN KEY (steam_user_id) REFERENCES steam_users(steam_user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1990 (class 2606 OID 16712)
-- Name: fk_leaderboards_last_snapshot_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY leaderboards
    ADD CONSTRAINT fk_leaderboards_last_snapshot_id FOREIGN KEY (last_snapshot_id) REFERENCES leaderboard_snapshots(leaderboard_snapshot_id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 1991 (class 2606 OID 16456)
-- Name: fk_leaderboards_leaderboard_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY leaderboard_snapshots
    ADD CONSTRAINT fk_leaderboards_leaderboard_id FOREIGN KEY (leaderboard_id) REFERENCES leaderboards(leaderboard_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2012 (class 2606 OID 16611)
-- Name: fk_power_ranking_entries_aria_score_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_aria_score_leaderboard_entry_id FOREIGN KEY (aria_score_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2002 (class 2606 OID 16561)
-- Name: fk_power_ranking_entries_aria_speed_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_aria_speed_leaderboard_entry_id FOREIGN KEY (aria_speed_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2010 (class 2606 OID 16601)
-- Name: fk_power_ranking_entries_bard_score_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_bard_score_leaderboard_entry_id FOREIGN KEY (bard_score_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2000 (class 2606 OID 16551)
-- Name: fk_power_ranking_entries_bard_speed_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_bard_speed_leaderboard_entry_id FOREIGN KEY (bard_speed_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2013 (class 2606 OID 16616)
-- Name: fk_power_ranking_entries_bolt_score_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_bolt_score_leaderboard_entry_id FOREIGN KEY (bolt_score_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2003 (class 2606 OID 16566)
-- Name: fk_power_ranking_entries_bolt_speed_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_bolt_speed_leaderboard_entry_id FOREIGN KEY (bolt_speed_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2009 (class 2606 OID 16596)
-- Name: fk_power_ranking_entries_cadence_score_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_cadence_score_leaderboard_entry_id FOREIGN KEY (cadence_score_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1999 (class 2606 OID 16546)
-- Name: fk_power_ranking_entries_cadence_speed_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_cadence_speed_leaderboard_entry_id FOREIGN KEY (cadence_speed_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2018 (class 2606 OID 16641)
-- Name: fk_power_ranking_entries_coda_score_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_coda_score_leaderboard_entry_id FOREIGN KEY (coda_score_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2008 (class 2606 OID 16591)
-- Name: fk_power_ranking_entries_coda_speed_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_coda_speed_leaderboard_entry_id FOREIGN KEY (coda_speed_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2017 (class 2606 OID 16636)
-- Name: fk_power_ranking_entries_dorian_score_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_dorian_score_leaderboard_entry_id FOREIGN KEY (dorian_score_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2007 (class 2606 OID 16586)
-- Name: fk_power_ranking_entries_dorian_speed_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_dorian_speed_leaderboard_entry_id FOREIGN KEY (dorian_speed_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2014 (class 2606 OID 16621)
-- Name: fk_power_ranking_entries_dove_score_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_dove_score_leaderboard_entry_id FOREIGN KEY (dove_score_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2004 (class 2606 OID 16571)
-- Name: fk_power_ranking_entries_dove_speed_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_dove_speed_leaderboard_entry_id FOREIGN KEY (dove_speed_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2015 (class 2606 OID 16626)
-- Name: fk_power_ranking_entries_eli_score_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_eli_score_leaderboard_entry_id FOREIGN KEY (eli_score_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2005 (class 2606 OID 16576)
-- Name: fk_power_ranking_entries_eli_speed_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_eli_speed_leaderboard_entry_id FOREIGN KEY (eli_speed_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2016 (class 2606 OID 16631)
-- Name: fk_power_ranking_entries_melody_score_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_melody_score_leaderboard_entry_id FOREIGN KEY (melody_score_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2006 (class 2606 OID 16581)
-- Name: fk_power_ranking_entries_melody_speed_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_melody_speed_leaderboard_entry_id FOREIGN KEY (melody_speed_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2011 (class 2606 OID 16606)
-- Name: fk_power_ranking_entries_monk_score_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_monk_score_leaderboard_entry_id FOREIGN KEY (monk_score_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2001 (class 2606 OID 16556)
-- Name: fk_power_ranking_entries_monk_speed_leaderboard_entry_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_monk_speed_leaderboard_entry_id FOREIGN KEY (monk_speed_leaderboard_entry_id) REFERENCES leaderboard_entries(leaderboard_entry_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1997 (class 2606 OID 16536)
-- Name: fk_power_ranking_entries_power_ranking_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_power_ranking_id FOREIGN KEY (power_ranking_id) REFERENCES power_rankings(power_ranking_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1998 (class 2606 OID 16541)
-- Name: fk_power_ranking_entries_steam_user_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_entries
    ADD CONSTRAINT fk_power_ranking_entries_steam_user_id FOREIGN KEY (steam_user_id) REFERENCES steam_users(steam_user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1996 (class 2606 OID 16521)
-- Name: fk_power_ranking_leaderboard_snapshots_leaderboard_snapshot_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_leaderboard_snapshots
    ADD CONSTRAINT fk_power_ranking_leaderboard_snapshots_leaderboard_snapshot_id FOREIGN KEY (leaderboard_snapshot_id) REFERENCES leaderboard_snapshots(leaderboard_snapshot_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1995 (class 2606 OID 16516)
-- Name: fk_power_ranking_leaderboard_snapshots_power_ranking_id; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY power_ranking_leaderboard_snapshots
    ADD CONSTRAINT fk_power_ranking_leaderboard_snapshots_power_ranking_id FOREIGN KEY (power_ranking_id) REFERENCES power_rankings(power_ranking_id) ON UPDATE CASCADE ON DELETE CASCADE;
    
-- Add data to characters table

INSERT INTO characters VALUES (1, 'cadence', 'Cadence', 1, 1);
INSERT INTO characters VALUES (2, 'bard', 'Bard', 1, 1);
INSERT INTO characters VALUES (3, 'aria', 'Aria', 1, 0);
INSERT INTO characters VALUES (4, 'bolt', 'Bolt', 1, 0);
INSERT INTO characters VALUES (5, 'monk', 'Monk', 1, 0);
INSERT INTO characters VALUES (6, 'dove', 'Dove', 1, 0);
INSERT INTO characters VALUES (7, 'eli', 'Eli', 1, 0);
INSERT INTO characters VALUES (8, 'melody', 'Melody', 1, 0);
INSERT INTO characters VALUES (9, 'dorian', 'Dorian', 1, 0);
INSERT INTO characters VALUES (10, 'coda', 'Coda', 0, 0);
INSERT INTO characters VALUES (11, 'ghost', 'Ghost', 0, 0);
INSERT INTO characters VALUES (12, 'pacifist', 'Pacifist', 0, 0);
INSERT INTO characters VALUES (13, 'thief', 'Thief', 0, 0);


REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;