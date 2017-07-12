<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\Database\InsertQueue;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis\Hybrid as HybridResultset;
use \Modules\Necrolab\Models\Leaderboards\Replays as BaseReplays;
use \Modules\Necrolab\Models\Leaderboards\Database\RunResults as DatabaseRunResults;
use \Modules\Necrolab\Models\Leaderboards\Database\ReplayVersions as DatabaseReplayVersions;
use \Modules\Necrolab\Models\Characters;
use \Modules\Necrolab\Models\Modes;
use \Modules\Necrolab\Models\SteamUsers\Database\Pbs as DatabaseSteamUserPbs;
use \Modules\Necrolab\Models\Leaderboards\CacheNames;

class Replays
extends BaseReplays {    
    public static function loadAll() {
        if(empty(static::$replays)) {            
            static::$replays = db()->getMappedColumn("
                SELECT 
                    ugcid,
                    steam_replay_id
                FROM steam_replays
            ");
        }
    }
    
    public static function dropTableConstraints() {    
        db()->exec("
            ALTER TABLE steam_replays
            DROP CONSTRAINT fk_sr_run_result_id,
            DROP CONSTRAINT fk_sr_steam_replay_version_id;
        ");
    }
    
    public static function createTableConstraints() {        
        db()->exec("
            ALTER TABLE steam_replays
            ADD CONSTRAINT fk_sr_run_result_id FOREIGN KEY (run_result_id)
                REFERENCES run_results (run_result_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE,
            ADD CONSTRAINT fk_sr_steam_replay_version_id FOREIGN KEY (steam_replay_version_id)
                REFERENCES steam_replay_versions (steam_replay_version_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE;
        ");
    }

    public static function dropTableIndexes() {
        db()->exec("
            DROP INDEX IF EXISTS idx_sr_downloaded;
            DROP INDEX IF EXISTS idx_sr_downloaded_invalid;
            DROP INDEX IF EXISTS idx_sr_invalid;
            DROP INDEX IF EXISTS idx_sr_run_result_id;
            DROP INDEX IF EXISTS idx_sr_steam_replay_version_id;
        ");
    }
    
    public static function createTableIndexes() {
        db()->exec("
            CREATE INDEX idx_sr_downloaded
            ON steam_replays
            USING btree (downloaded);           
            
            CREATE INDEX idx_sr_downloaded_invalid
            ON steam_replays
            USING btree (downloaded, invalid);
            
            CREATE INDEX idx_sr_invalid
            ON steam_replays
            USING btree (invalid);
            
            CREATE INDEX idx_sr_run_result_id
            ON steam_replays
            USING btree (run_result_id);
            
            CREATE INDEX idx_sr_steam_replay_version_id
            ON public.steam_replays
            USING btree (steam_replay_version_id);
        ");
    }
    
    public static function getNewRecordId() {
        return db()->getOne("SELECT nextval('steam_replays_seq'::regclass)");
    }
    
    public static function save($ugcid, $steam_user_id, InsertQueue $insert_queue) {
        $steam_replay_id = static::get($ugcid);
        
        if(empty($steam_replay_id)) {
            $steam_replay_id = static::getNewRecordId();
        
            $insert_queue->addRecord(array(
                'steam_replay_id' => $steam_replay_id,
                'ugcid' => $ugcid,
                'steam_user_id' => $steam_user_id,
                'downloaded' => 0,
                'invalid' => 0,
                'uploaded_to_s3' => 0
            ));
            
            static::$replays[$ugcid] = $steam_replay_id;
        }
    
        return $steam_replay_id;
    }
    
    public static function getInsertQueue() {
        return new InsertQueue("steam_replays", db(), 8000);
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE steam_replays;");
    }
    
    public static function createTemporaryTable() {
        db()->exec("
            CREATE TEMPORARY TABLE steam_replays_temp (
                steam_replay_id integer NOT NULL,
                steam_user_id integer,
                ugcid numeric,
                downloaded smallint,
                invalid smallint,
                seed bigint,
                run_result_id smallint,
                steam_replay_version_id smallint,
                uploaded_to_s3 smallint
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function getTempInsertQueue() {
        return new InsertQueue("steam_replays_temp", db(), 10000);
    }
    
    public static function saveNewTemp() {
        db()->query("
            INSERT INTO steam_replays
            SELECT *
            FROM steam_replays_temp
        ");
    }

    public static function saveDownloadedTemp() {
        db()->query("
            UPDATE steam_replays sr
            SET 
                seed = srt.seed,
                run_result_id = srt.run_result_id,
                steam_replay_version_id = srt.steam_replay_version_id,
                downloaded = srt.downloaded,
                invalid = srt.invalid,
                uploaded_to_s3 = srt.uploaded_to_s3
            FROM steam_replays_temp srt
            WHERE sr.steam_replay_id = srt.steam_replay_id
        ");
    }
    
    public static function saveInvalidTemp() {
        db()->query("
            UPDATE steam_replays sr
            SET 
                downloaded = srt.downloaded,
                invalid = srt.invalid,
                uploaded_to_s3 = srt.uploaded_to_s3
            FROM steam_replays_temp srt
            WHERE sr.steam_replay_id = srt.steam_replay_id
        ");
    }
    
    public static function saveUpdatedFromFilesTemp() {
        db()->query("
            UPDATE steam_replays sr
            SET 
                seed = srt.seed,
                run_result_id = srt.run_result_id,
                steam_replay_version_id = srt.steam_replay_version_id
            FROM steam_replays_temp srt
            WHERE sr.steam_replay_id = srt.steam_replay_id
        ");
    }
    
    public static function saveS3UploadedTemp() {
        db()->query("
            UPDATE steam_replays sr
            SET 
                uploaded_to_s3 = srt.uploaded_to_s3
            FROM steam_replays_temp srt
            WHERE sr.steam_replay_id = srt.steam_replay_id
        ");
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'sr.ugcid',
                'alias' => 'ugcid'
            ),
            array(
                'field' => 'sr.seed',
                'alias' => 'seed'
            ),
            array(
                'field' => 'sr.uploaded_to_s3',
                'alias' => 'uploaded_to_s3'
            )
        ));
    }
    
    public static function getEntriesResultset() {    
        $resultset = new SQL("steam_replays");
        
        $resultset->setBaseQuery("
            SELECT *
            FROM steam_replays
            {{WHERE_CRITERIA}}
        ");
        
        return $resultset;
    }
    
    public static function getUnsavedReplaysResultset() {
        $resultset = static::getEntriesResultset();
        
        $resultset->addFilterCriteria('downloaded = 0');
        $resultset->addFilterCriteria('invalid = 0');
        
        $resultset->addSortCriteria('ugcid', 'ASC');
        
        return $resultset;
    }
    
    public static function getSavedReplaysResultset() {
        $resultset = static::getEntriesResultset();
        
        $resultset->setName('saved_steam_replays');
        
        $resultset->addFilterCriteria('downloaded = 1');
        $resultset->addFilterCriteria('invalid = 0');
        
        return $resultset;
    }
    
    public static function getUnuploadedReplaysResultset() {
        $resultset = static::getEntriesResultset();
        
        $resultset->addFilterCriteria('downloaded = 1');
        $resultset->addFilterCriteria('invalid = 0');
        $resultset->addFilterCriteria('uploaded_to_s3 = 0');
        
        $resultset->addSortCriteria('ugcid', 'ASC');
        
        return $resultset;
    }
    
    public static function getCacheResultset() {
        $resultset = new SQL('replays:entries:cache');
        
        static::setSelectFields($resultset);
        $resultset->addSelectField('sr.steam_user_id', 'steam_user_id');
        
        DatabaseRunResults::setSelectFields($resultset);
        DatabaseReplayVersions::setSelectFields($resultset);
        
        $resultset->setFromTable('steam_replays sr');
        
        $resultset->addLeftJoinCriteria('run_results rr ON rr.run_result_id = sr.run_result_id');
        $resultset->addLeftJoinCriteria('steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id');
        
        return $resultset;
    }
    
    public static function getAllResultset() {
        $resultset = new SQL('replays:entries');
        
        static::setSelectFields($resultset);
        DatabaseRunResults::setSelectFields($resultset);
        DatabaseReplayVersions::setSelectFields($resultset);
        DatabaseSteamUserPbs::setSelectFields($resultset);
        Leaderboards::setSelectFields($resultset);
        Characters::setSelectFields($resultset);
        Modes::setSelectFields($resultset);
        Snapshots::setSelectFields($resultset);
        Details::setSelectFields($resultset);
        
        $resultset->addSelectField('su.steamid', 'steamid');
        
        $resultset->setFromTable('steam_replays sr');
        
        $resultset->addJoinCriteria('steam_user_pbs sup ON sup.steam_replay_id = sr.steam_replay_id');
        $resultset->addJoinCriteria('leaderboards l ON l.leaderboard_id = sup.leaderboard_id');
        $resultset->addJoinCriteria('characters c ON c.character_id = l.character_id');
        $resultset->addJoinCriteria('modes mo ON mo.mode_id = l.mode_id');
        $resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_snapshot_id = sup.first_leaderboard_snapshot_id');
        $resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = sup.leaderboard_entry_details_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sr.steam_user_id');
        
        $resultset->addLeftJoinCriteria('run_results rr ON rr.run_result_id = sr.run_result_id');
        $resultset->addLeftJoinCriteria('steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id');
        
        return $resultset;
    }
    
    public static function getApiAllSqlResultset() {
        $resultset = new SQL('replays');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'l.lbid',
                'alias' => 'lbid',
            ),
            array(
                'field' => 'ls.date',
                'alias' => 'date',
            ),
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid',
            ),
            array(
                'field' => 'sr.ugcid',
                'alias' => 'ugcid'
            ),
            array(
                'field' => 'sr.seed',
                'alias' => 'seed'
            ),
            array(
                'field' => 'sr.downloaded',
                'alias' => 'downloaded'
            ),
            array(
                'field' => 'srv.name',
                'alias' => 'version'
            ),
            array(
                'field' => 'rr.name',
                'alias' => 'run_result'
            )
        ));
        
        $resultset->setFromTable('steam_replays sr');
        
        $resultset->addJoinCriteria('steam_user_pbs sup ON sup.steam_replay_id = sr.steam_replay_id');
        $resultset->addJoinCriteria('leaderboards l ON l.leaderboard_id = sup.leaderboard_id');
        $resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_snapshot_id = sup.first_leaderboard_snapshot_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sr.steam_user_id');
        
        $resultset->addLeftJoinCriteria('run_results rr ON rr.run_result_id = sr.run_result_id');
        $resultset->addLeftJoinCriteria('steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id');
        
        return $resultset;
    }
    
    public static function getApiAllResultset($release_id, $mode_id) {
        $resultset = new HybridResultset("replays", cache('database'), cache('local'));
        
        $sql_resultset = static::getApiAllSqlResultset();
        
        $sql_resultset->setSortCriteria('sr.steam_replay_id', 'ASC');
        
        $resultset->setSqlResultset($sql_resultset, 'sr.steam_replay_id');
        
        $resultset->setIndexName(CacheNames::getReplaysName());
        
        $resultset->setPartitionName(CacheNames::getReplaysIndexName(array(
            $release_id,
            $mode_id
        )));
        
        return $resultset;
    }
    
    public static function getOneResultset($ugcid) {        
        $resultset = static::getApiAllSqlResultset();
        
        $resultset->addFilterCriteria("sr.ugcid = :ugcid", array(
            ':ugcid' => $ugcid
        ));
        
        return $resultset;
    }
    
    public static function getSteamUserResultset($release_id, $mode_id, $steamid) {
        $resultset = static::getApiAllSqlResultset();
        
        $resultset->setName("steam_users_replay_entries");
        
        $resultset->addFilterCriteria("su.steamid = :steamid", array(
            ':steamid' => $steamid
        ));
        
        $resultset->addFilterCriteria("l.release_id = :release_id", array(
            ':release_id' => $release_id
        ));
        
        $resultset->addFilterCriteria("l.mode_id = :mode_id", array(
            ':mode_id' => $mode_id
        ));
        
        $resultset->setSortCriteria('sr.steam_replay_id', 'DESC');
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);
        
        return $resultset;
    }
    
    public static function loadIntoCache() {
        $resultset = new SQL('replay_cache_query');
        
        $resultset->setFromTable('steam_replays sr');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'sr.steam_replay_id',
                'alias' => 'steam_replay_id',
            ),
            array(
                'field' => 'l.release_id',
                'alias' => 'release_id',
            ),
            array(
                'field' => 'l.mode_id',
                'alias' => 'mode_id',
            )
        ));
        
        $resultset->addJoinCriteria('steam_user_pbs sup ON sup.steam_replay_id = sr.steam_replay_id');
        $resultset->addJoinCriteria('leaderboards l ON l.leaderboard_id = sup.leaderboard_id');
        
        $resultset->addFilterCriteria('downloaded = 1');
        $resultset->addFilterCriteria('invalid = 0');
        
        $resultset->addSortCriteria("sr.steam_replay_id", "ASC");
        
        $resultset->setAsCursor(100000);
        
        db()->beginTransaction();
        
        $resultset->prepareExecuteQuery();
        
        $transaction = cache('database')->transaction();
        
        $steam_replays = array();
        $indexes = array();
        
        do {
            $steam_replays = $resultset->getNextCursorChunk();
        
            if(!empty($steam_replays)) {
                foreach($steam_replays as $steam_replay) {   
                    $steam_replay_id = (int)$steam_replay['steam_replay_id'];
                    $release_id = (int)$steam_replay['release_id'];
                    $mode_id = (int)$steam_replay['mode_id'];
                    
                    $indexes[CacheNames::getReplaysIndexName(array(
                        $release_id,
                        $mode_id
                    ))][] = $steam_replay_id;
                }
            }
        }
        while(!empty($steam_replays));
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {
                $transaction->set($key, static::encodeRecord($index_data), CacheNames::getReplaysName());
            }
        }
        
        unset($indexes);
        
        $transaction->commit();
        
        db()->commit();
    }
}