<?php
namespace Modules\Necrolab\Models\SteamUsers\Database;

use \DateTime;
use \Framework\Data\Database\InsertQueue;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis\Hybrid as HybridResultset;
use \Modules\Necrolab\Models\SteamUsers\SteamUsers as BaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUser as DatabaseSteamUser;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards;
use \Modules\Necrolab\Models\ExternalSites as ExternalSites;
use \Modules\Necrolab\Models\SteamUsers\CacheNames;

class SteamUsers
extends BaseSteamUsers {
    public static function load($steamid) {
        if(empty(static::$users[$steamid])) {
            static::$users[$steamid] = db()->getRow("
                SELECT *
                FROM steam_users
                WHERE steamid = :steamid
            ", array(
                ':steamid' => $steamid
            ));
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            $user_ids = db()->getMappedColumn("
                SELECT
                    steamid,
                    steam_user_id
                FROM steam_users
            ");
            
            if(!empty($user_ids)) {
                static::$user_ids = $user_ids;
            }
        }
    }
    
    public static function getAllResultset() {
        $resultset = new SQL('steam_users');
        
        $resultset->setFromTable('steam_users su');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            ),
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            ),
            array(
                'field' => 'su.profileurl',
                'alias' => 'profileurl'
            )
        ));
        
        ExternalSites::addSiteUserLeftJoins($resultset);
        
        $resultset->setSortCriteria('su.personaname', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllDisplayResultset($external_site_id) {
        $resultset = new HybridResultset('all_steam_users', cache('database'), cache('local'));
        
        $resultset->setSqlResultset(static::getAllResultset(), 'su.steam_user_id');
        
        $resultset->setIndexName(CacheNames::getBaseName());
        
        $resultset->setPartitionName(CacheNames::getUsersIndexName(array(
            $external_site_id
        )));
        
        return $resultset;
    }
    
    public static function getOneApiResultset($steamid) {
        $resultset = static::getAllResultset();
        
        $resultset->addFilterCriteria("steamid = :steamid", array(
            ':steamid' => $steamid
        ));
        
        return $resultset;
    }
    
    public static function getOutdatedIds() {
        $thirty_days_ago = new DateTime('-1 day');
    
        return db()->getMappedColumn("
            SELECT
                steam_user_id,
                steamid
            FROM steam_users
            WHERE updated < :updated
        ", array(
            ':updated' => $thirty_days_ago->format('Y-m-d H:i:s')
        ));
    }
    
    public static function getRecordModel(array $properties) {
        $record_model = new DatabaseSteamUser();
        
        $record_model->setPropertiesFromArray($properties);
        
        return $record_model;
    }
    
    public static function save(DatabaseSteamUser $steam_user, $cache_query_name = NULL) {
        $steam_user_id = static::getId($steam_user->steamid);
        
        if(empty($steam_user_id)) {
            $updated = new DateTime('-31 day');
        
            $steam_user->updated = $updated->format('Y-m-d H:i:s');
            
            $user_record = array();
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
                
                $user_record = $steam_user->toArray();
            }
            else {
                $user_record = $steam_user->toArray(false);
            }
            
            $steam_user_id = static::getNewRecordId();
            
            $user_record['steam_user_id'] = $steam_user_id;
        
            db()->insert('steam_users', $user_record, $cache_query_name, false);
            
            static::addId($steam_user->steamid, $steam_user_id);
        }
        else {            
            if(isset($user_record['steamid'])) {
                unset($user_record['steamid']);
            }
            
            $user_record = array();
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
                
                $user_record = $steam_user->toArray();
            }
            else {
                $user_record = $steam_user->toArray(false);
            }
        
            db()->update('steam_users', $user_record, array(
                'steam_user_id' => $steam_user_id
            ), array(), $cache_query_name);
        }
        
        return $steam_user_id;
    }
    
    public static function dropTableConstraints() {    
        db()->exec("
            ALTER TABLE steam_users
            DROP CONSTRAINT fk_steam_users_beampro_user_id,
            DROP CONSTRAINT fk_steam_users_discord_user_id,
            DROP CONSTRAINT fk_steam_users_reddit_user_id,
            DROP CONSTRAINT fk_steam_users_twitch_user_id,
            DROP CONSTRAINT fk_steam_users_twitter_user_id,
            DROP CONSTRAINT fk_steam_users_youtube_user_id;
        ");
    }
    
    public static function createTableConstraints() {        
        db()->exec("
            ALTER TABLE steam_users
            ADD CONSTRAINT fk_steam_users_beampro_user_id FOREIGN KEY (beampro_user_id)
                REFERENCES beampro_users (beampro_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE SET NULL,
            ADD CONSTRAINT fk_steam_users_discord_user_id FOREIGN KEY (discord_user_id)
                REFERENCES discord_users (discord_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE SET NULL,
            ADD CONSTRAINT fk_steam_users_reddit_user_id FOREIGN KEY (reddit_user_id)
                REFERENCES reddit_users (reddit_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE SET NULL,
            ADD CONSTRAINT fk_steam_users_twitch_user_id FOREIGN KEY (twitch_user_id)
                REFERENCES twitch_users (twitch_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE SET NULL,
            ADD CONSTRAINT fk_steam_users_twitter_user_id FOREIGN KEY (twitter_user_id)
                REFERENCES twitter_users (twitter_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE SET NULL,
            ADD CONSTRAINT fk_steam_users_youtube_user_id FOREIGN KEY (youtube_user_id)
                REFERENCES youtube_users (youtube_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE SET NULL;
        ");
    }

    public static function dropTableIndexes() {
        db()->exec("
            DROP INDEX IF EXISTS idx_steam_users_personaname;
            DROP INDEX IF EXISTS idx_su_personaname_search;
            DROP INDEX IF EXISTS idx_steam_users_updated;
            DROP INDEX IF EXISTS idx_su_beampro_user_id;
            DROP INDEX IF EXISTS idx_su_discord_user_id;
            DROP INDEX IF EXISTS idx_su_reddit_user_id;
            DROP INDEX IF EXISTS idx_su_twitch_user_id;
            DROP INDEX IF EXISTS idx_su_twitter_user_id;
            DROP INDEX IF EXISTS idx_su_youtube_user_id;
        ");
    }
    
    public static function createTableIndexes() {
        db()->exec("
            CREATE INDEX idx_steam_users_personaname
            ON steam_users
            USING btree (personaname COLLATE pg_catalog.\"default\");
            
            CREATE INDEX IF NOT EXISTS idx_su_personaname_search
            ON steam_users 
            USING GIN (personaname gin_trgm_ops);

            CREATE INDEX idx_steam_users_updated
            ON steam_users
            USING btree (updated);

            CREATE INDEX idx_su_beampro_user_id
            ON steam_users
            USING btree (beampro_user_id);

            CREATE INDEX idx_su_discord_user_id
            ON steam_users
            USING btree (discord_user_id);

            CREATE INDEX idx_su_reddit_user_id
            ON steam_users
            USING btree (reddit_user_id);

            CREATE INDEX idx_su_twitch_user_id
            ON steam_users
            USING btree (twitch_user_id);

            CREATE INDEX idx_su_twitter_user_id
            ON steam_users
            USING btree (twitter_user_id);
            
            CREATE INDEX idx_su_youtube_user_id
            ON steam_users
            USING btree (youtube_user_id)
        ");
    }
    
    public static function getNewRecordId() {
        return db()->getOne("SELECT nextval('steam_users_seq'::regclass)");
    }
    
    public static function saveToQueue($steamid, InsertQueue $insert_queue) {
        $steam_user_id = static::getNewRecordId();
    
        $updated = new DateTime('-31 day');
        
        $insert_queue->addRecord(array(
            'steam_user_id' => $steam_user_id,
            'steamid' => $steamid,
            'updated' => $updated->format('Y-m-d H:i:s')
        ));
        
        static::addId($steamid, $steam_user_id);
        
        return $steam_user_id;
    }
    
    public static function getInsertQueue() {
        return new InsertQueue("steam_users", db(), 5000);
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE steam_users;");
    }
    
    public static function getTempInsertQueue() {
        return new InsertQueue("steam_users_temp", db(), 1000);
    }
    
    public static function createTemporaryTable() {
        db()->exec("
            CREATE TEMPORARY TABLE steam_users_temp
            (
                steam_user_id integer,
                steamid bigint,
                communityvisibilitystate smallint,
                profilestate smallint,
                personaname character varying(255),
                lastlogoff integer,
                profileurl text,
                avatar text,
                avatarmedium text,
                avatarfull text,
                personastate smallint,
                realname character varying(255),
                primaryclanid bigint,
                timecreated integer,
                personastateflags smallint,
                loccountrycode character varying(3),
                locstatecode character varying(3),
                loccityid integer,
                updated timestamp without time zone
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp() {
        db()->query("
            INSERT INTO steam_users (steam_user_id, steamid, updated)
            SELECT 
                steam_user_id,
                steamid,
                updated
            FROM steam_users_temp
        ");
    }
    
    public static function saveTemp() {
        db()->query("
            UPDATE steam_users su
            SET 
                communityvisibilitystate = sut.communityvisibilitystate,
                profilestate = sut.profilestate,
                personaname = sut.personaname,
                lastlogoff = sut.lastlogoff,
                profileurl = sut.profileurl,
                avatar = sut.avatar,
                avatarmedium = sut.avatarmedium,
                avatarfull = sut.avatarfull,
                personastate = sut.personastate,
                realname = sut.realname,
                primaryclanid = sut.primaryclanid,
                timecreated = sut.timecreated,
                personastateflags = sut.personastateflags,
                loccountrycode = sut.loccountrycode,
                locstatecode = sut.locstatecode,
                loccityid = sut.loccityid,
                updated = sut.updated
            FROM steam_users_temp sut
            WHERE su.steam_user_id = sut.steam_user_id
        ");
    }
    
    public static function loadIntoCache() {                
        $resultset = new SQL('steam_users_cache');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            )
        ));
        
        ExternalSites::addSiteIdSelectFields($resultset);
        
        $resultset->setFromTable('steam_users su');
        
        $resultset->setSortCriteria('su.personaname', 'ASC');
        
        $resultset->setAsCursor(50000);
        
        db()->beginTransaction();
        
        $resultset->prepareExecuteQuery();
        
        $steam_users_names_cache_name = CacheNames::getUsersByName();
        
        $transaction = cache('database')->transaction();
        
        $steam_users = array();
        $steam_user_names = array();
        $indexes = array();
        $users_index_base_name = CacheNames::getUsersIndexName();
        
        do {
            $steam_users = $resultset->getNextCursorChunk();
        
            if(!empty($steam_users)) {
                foreach($steam_users as $steam_user) {  
                    $steam_user_id = (int)$steam_user['steam_user_id'];
                    $personaname = $steam_user['personaname'];
                    
                    $steam_user_names[$steam_user_id] = $personaname;
                    
                    ExternalSites::addToSiteIdIndexes($indexes, $steam_user, $users_index_base_name, $steam_user_id);
                }
            }
        }
        while(!empty($steam_users));
        
        $transaction->set($steam_users_names_cache_name, static::encodeRecord($steam_user_names));
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {
                $transaction->set($key, static::encodeRecord($index_data), CacheNames::getBaseName());
            }
        }
        
        $transaction->commit();
        
        db()->commit();
    }
    
    /*public static function loadIntoCache() {
        ExternalSites::loadAll();
    
        $resultset = static::getAllResultset();
        
        $resultset->setAsCursor(100000);
        
        db()->beginTransaction();
        
        $resultset->prepareExecuteQuery();
        
        $steam_users_cache_name = CacheNames::getAllRecordsName();
        $steam_users_names_cache_name = CacheNames::getUsersByName();
        
        $transaction = cache()->transaction();
        
        $steam_users = array();
        $steam_user_names = array();
        $indexes = array();
        $users_index_base_name = CacheNames::getUsersIndexName();
        
        do {
            $steam_users = $resultset->getNextCursorChunk();
        
            if(!empty($steam_users)) {
                foreach($steam_users as $steam_user) {  
                    $steam_user_id = (int)$steam_user['steam_user_id'];
                    $personaname = $steam_user['personaname'];
                
                    $transaction->hSet($steam_users_cache_name, $steam_user_id, static::encodeRecord(array(
                        'steamid' => $steam_user['steamid'],
                        'personaname' => $steam_user['personaname'],
                        'steam_profile_url' => $steam_user['steam_profile_url'],
                        'beampro_user_id' => $steam_user['beampro_user_id'],
                        'beampro_id' => $steam_user['beampro_id'],
                        'beampro_username' => $steam_user['beampro_username'],
                        'discord_user_id' => $steam_user['discord_user_id'],
                        'discord_id' => $steam_user['discord_id'],
                        'discord_username' => $steam_user['discord_username'],
                        'discord_discriminator' => $steam_user['discord_discriminator'],
                        'reddit_user_id' => $steam_user['reddit_user_id'],
                        'reddit_id' => $steam_user['reddit_id'],
                        'reddit_username' => $steam_user['reddit_username'],
                        'twitch_user_id' => $steam_user['twitch_user_id'],
                        'twitch_id' => $steam_user['twitch_id'],
                        'twitch_username' => $steam_user['twitch_username'],
                        'twitter_user_id' => $steam_user['twitter_user_id'],
                        'twitter_id' => $steam_user['twitter_id'],
                        'twitter_nickname' => $steam_user['twitter_nickname'],
                        'twitter_name' => $steam_user['twitter_name'],
                        'youtube_user_id' => $steam_user['youtube_id'],
                        'youtube_username' => $steam_user['youtube_username']
                    )));
                    
                    $steam_user_names[$steam_user_id] = $personaname;
                    
                    ExternalSites::addToSiteIdIndexes($indexes, $steam_user, $users_index_base_name, $steam_user_id);
                }
            }
        }
        while(!empty($steam_users));
        
        $transaction->set($steam_users_names_cache_name, static::encodeRecord($steam_user_names));
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {
                $transaction->set($key, static::encodeRecord($index_data));
            }
        }
        
        $transaction->commit();
        
        db()->commit();
    }*/
}
