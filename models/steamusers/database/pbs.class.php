<?php
namespace Modules\Necrolab\Models\SteamUsers\Database;

use \DateTime;
use \Framework\Data\Database\InsertQueue;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis\Hybrid as HybridResultset;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUserPb as DatabaseSteamUserPb;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards as DatabaseLeaderboards;
use \Modules\Necrolab\Models\SteamUsers\CacheNames;
use \Modules\Necrolab\Models\SteamUsers\Pbs as BasePbs;

class Pbs
extends BasePbs {
    public static function load($steam_user_pb_id) {
        if(empty(static::$users[$steam_user_pb_id])) {
            static::$users[$steam_user_pb_id] = db()->getRow("
                SELECT *
                FROM steam_user_pbs
                WHERE steam_user_pb_id = :steam_user_pb_id
            ", array(
                ':steam_user_pb_id' => $steam_user_pb_id
            ));
        }
    }
    
    public static function loadIds() {
        if(empty(static::$pb_ids)) {
            $pb_ids = db()->getAll("
                SELECT
                    steam_user_pb_id,
                    leaderboard_id,
                    steam_user_id,
                    score
                FROM steam_user_pbs
            ");
            
            if(!empty($pb_ids)) {
                foreach($pb_ids as $pb_id) {
                    static::addId($pb_id['leaderboard_id'], $pb_id['steam_user_id'], $pb_id['score'], $pb_id['steam_user_pb_id']);
                }
            }
        }
    }
    
    public static function dropTableConstraints() {    
        db()->exec("
            ALTER TABLE steam_user_pbs
            DROP CONSTRAINT fk_steam_user_pbs_first_leaderboard_snapshot_id,
            DROP CONSTRAINT fk_steam_user_pbs_leaderboard_entry_details_id,
            DROP CONSTRAINT fk_steam_user_pbs_leaderboard_id,
            DROP CONSTRAINT fk_steam_user_pbs_steam_replay_id,
            DROP CONSTRAINT fk_steam_user_pbs_steam_user_id;
        ");
    }
    
    public static function createTableConstraints() {        
        db()->exec("
            ALTER TABLE steam_user_pbs
            ADD CONSTRAINT fk_steam_user_pbs_first_leaderboard_snapshot_id FOREIGN KEY (first_leaderboard_snapshot_id)
                REFERENCES leaderboard_snapshots (leaderboard_snapshot_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE,
            ADD CONSTRAINT fk_steam_user_pbs_leaderboard_entry_details_id FOREIGN KEY (leaderboard_entry_details_id)
                REFERENCES leaderboard_entry_details (leaderboard_entry_details_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE,
            ADD CONSTRAINT fk_steam_user_pbs_leaderboard_id FOREIGN KEY (leaderboard_id)
                REFERENCES leaderboards (leaderboard_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE,
            ADD CONSTRAINT fk_steam_user_pbs_steam_replay_id FOREIGN KEY (steam_replay_id)
                REFERENCES steam_replays (steam_replay_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE,
            ADD CONSTRAINT fk_steam_user_pbs_steam_user_id FOREIGN KEY (steam_user_id)
                REFERENCES steam_users (steam_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE;
        ");
    }

    public static function dropTableIndexes() {
        db()->exec("
            DROP INDEX IF EXISTS idx_steam_user_pbs_first_leaderboard_id;
            DROP INDEX IF EXISTS idx_steam_user_pbs_first_leaderboard_snapshot_id;
            DROP INDEX IF EXISTS idx_steam_user_pbs_leaderboard_entry_details_id;
            DROP INDEX IF EXISTS idx_steam_user_pbs_steam_replay_id;
            DROP INDEX IF EXISTS idx_steam_user_pbs_steam_user_id;
        ");
    }
    
    public static function createTableIndexes() {
        db()->exec("
            CREATE INDEX idx_steam_user_pbs_first_leaderboard_id
            ON steam_user_pbs
            USING btree (leaderboard_id);

            CREATE INDEX idx_steam_user_pbs_first_leaderboard_snapshot_id
            ON steam_user_pbs
            USING btree (first_leaderboard_snapshot_id);

            CREATE INDEX idx_steam_user_pbs_leaderboard_entry_details_id
            ON steam_user_pbs
            USING btree (leaderboard_entry_details_id);

            CREATE INDEX idx_steam_user_pbs_steam_replay_id
            ON steam_user_pbs
            USING btree (steam_replay_id);

            CREATE INDEX idx_steam_user_pbs_steam_user_id
            ON steam_user_pbs
            USING btree (steam_user_id);
        ");
    }
    
    public static function getNewRecordId() {
        return db()->getOne("SELECT nextval('steam_user_pbs_seq'::regclass)");
    }
    
    public static function save(DatabaseSteamUserPb $steam_user_pb, InsertQueue $insert_queue) {
        $steam_user_pb_id = static::getNewRecordId();
        
        $pb_record = $steam_user_pb->toArray();
        
        $pb_record['steam_user_pb_id'] = $steam_user_pb_id;
    
        $insert_queue->addRecord($pb_record);
        
        static::addId($steam_user_pb->leaderboard_id, $steam_user_pb->steam_user_id, $steam_user_pb->score, $steam_user_pb_id);
        
        return $steam_user_pb_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE steam_user_pbs;");
    }
    
    public static function getTempInsertQueue() {
        return new InsertQueue("steam_user_pbs_temp", db(), 5000);
    }
    
    public static function createTemporaryTable() {
        db()->exec("
            CREATE TEMPORARY TABLE steam_user_pbs_temp (
                steam_user_pb_id integer NOT NULL,
                leaderboard_id smallint NOT NULL,
                steam_user_id integer NOT NULL,
                score integer,
                first_leaderboard_snapshot_id integer NOT NULL,
                first_rank integer NOT NULL,
                \"time\" double precision,
                win_count smallint,
                zone smallint,
                level smallint,
                is_win smallint,
                leaderboard_entry_details_id smallint NOT NULL,
                steam_replay_id integer
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp() {
        db()->query("
            INSERT INTO steam_user_pbs
            SELECT *
            FROM steam_user_pbs_temp
        ");
    }
    
    public static function getRecordModel(array $properties) {
        $record_model = new DatabaseSteamUserPb();
        
        $record_model->setPropertiesFromArray($properties);
        
        return $record_model;
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'sup.steam_user_pb_id',
                'alias' => 'steam_user_pb_id',
            ),
            array(
                'field' => 'sup.steam_user_id',
                'alias' => 'steam_user_id',
            ),
            array(
                'field' => 'sup.leaderboard_id',
                'alias' => 'leaderboard_id',
            ),
            array(
                'field' => 'sup.first_leaderboard_snapshot_id',
                'alias' => 'first_leaderboard_snapshot_id',
            ),
            array(
                'field' => 'sup.score',
                'alias' => 'score',
            ),
            array(
                'field' => 'sup.time',
                'alias' => 'time',
            ),
            array(
                'field' => 'sup.first_rank',
                'alias' => 'first_rank',
            ),
            array(
                'field' => 'sup.is_win',
                'alias' => 'is_win',
            ),
            array(
                'field' => 'sup.zone',
                'alias' => 'zone',
            ),
            array(
                'field' => 'sup.level',
                'alias' => 'level',
            ),
            array(
                'field' => 'sup.win_count',
                'alias' => 'win_count',
            ),
            array(
                'field' => 'sup.leaderboard_entry_details_id',
                'alias' => 'leaderboard_entry_details_id',
            ),
            array(
                'field' => 'sup.steam_replay_id',
                'alias' => 'steam_replay_id',
            )
        ));
    }
    
    public static function getApiSqlResultset() {
        $resultset = new SQL('api_steam_user_pbs');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            ),
            array(
                'field' => 'l.lbid',
                'alias' => 'lbid'
            ),
            array(
                'field' => 'l.is_speedrun',
                'alias' => 'is_speedrun'
            ),
            array(
                'field' => 'l.is_deathless',
                'alias' => 'is_deathless'
            ),
            array(
                'field' => 'ls.date',
                'alias' => 'snapshot_date',
            ),
            array(
                'field' => 'sup.first_rank',
                'alias' => 'first_rank',
            ),
            array(
                'field' => 'led.details',
                'alias' => 'details'
            ),
            array(
                'field' => 'sup.zone',
                'alias' => 'zone'
            ),
            array(
                'field' => 'sup.level',
                'alias' => 'level'
            ),
            array(
                'field' => 'sup.is_win',
                'alias' => 'is_win'
            ),
            array(
                'field' => 'sup.score',
                'alias' => 'score'
            ),
            array(
                'field' => 'sup.time',
                'alias' => 'time'
            ),
            array(
                'field' => 'sup.win_count',
                'alias' => 'win_count'
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
        
        $resultset->setFromTable('steam_user_pbs sup');
    
        $resultset->addJoinCriteria('leaderboards l ON l.leaderboard_id = sup.leaderboard_id');
        $resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = sup.leaderboard_entry_details_id');
        $resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_snapshot_id = sup.first_leaderboard_snapshot_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sup.steam_user_id');
        
        $resultset->addLeftJoinCriteria("
            steam_replays sr ON sr.steam_replay_id = sup.steam_replay_id
                AND downloaded = 1
                AND invalid = 0
            LEFT JOIN run_results rr ON rr.run_result_id = sr.run_result_id
            LEFT JOIN steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id
        ");
        
        $resultset->setSortCriteria('sup.steam_user_pb_id', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllApiResultset($release_id, $mode_id) {
        $resultset = new HybridResultset("api_pbs", cache('database'), cache('local'));
        
        $resultset->setSqlResultset(static::getApiSqlResultset(), 'sup.steam_user_pb_id');
        
        $resultset->setIndexName(CacheNames::getAllPbsName());
        
        $resultset->setPartitionName(CacheNames::getPbsIndexName(array(
            $release_id,
            $mode_id
        )));
        
        return $resultset;
    }
    
    public static function getAllApiScoreResultset($release_id, $mode_id) {
        $resultset = static::getAllApiResultset($release_id, $mode_id);
        
        $resultset->setPartitionName(CacheNames::getPbsIndexName(array(
            $release_id,
            $mode_id,
            CacheNames::SCORE
        )));
        
        return $resultset;
    }
    
    public static function getAllApiSpeedResultset($release_id, $mode_id) {
        $resultset = static::getAllApiResultset($release_id, $mode_id);
        
        $resultset->setPartitionName(CacheNames::getPbsIndexName(array(
            $release_id,
            $mode_id,
            CacheNames::SPEED
        )));
        
        return $resultset;
    }
    
    public static function getAllApiDeathlessResultset($release_id, $mode_id) {
        $resultset = static::getAllApiResultset($release_id, $mode_id);
        
        $resultset->setPartitionName(CacheNames::getPbsIndexName(array(
            $release_id,
            $mode_id,
            CacheNames::DEATHLESS
        )));
        
        return $resultset;
    }
    
    public static function getApiSteamUserResultset($release_id, $mode_id, $character_id, $seeded, $co_op, $custom, $steamid) {        
        $resultset = static::getApiSqlResultset();
        
        $resultset->setName("steam_user_api_pbs");
        
        $resultset->addFilterCriteria('su.steamid = :steamid', array(
            ':steamid' => $steamid
        ));
        
        $resultset->addFilterCriteria('l.release_id = :release_id', array(
            ':release_id' => $release_id
        ));
        
        $resultset->addFilterCriteria('l.mode_id = :mode_id', array(
            ':mode_id' => $mode_id
        ));
        
        $resultset->addFilterCriteria('l.character_id = :character_id', array(
            ':character_id' => $character_id
        ));
        
        $resultset->addFilterCriteria('l.is_seeded = :seeded', array(
            ':seeded' => $seeded
        ));
        
        $resultset->addFilterCriteria('l.is_co_op = :co_op', array(
            ':co_op' => $co_op
        ));
        
        $resultset->addFilterCriteria('l.is_custom = :custom', array(
            ':custom' => $custom
        ));
        
        $resultset->setSortCriteria('ls.date', 'DESC');
        $resultset->addSortCriteria('sup.steam_user_pb_id', 'ASC');
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);
        
        return $resultset;
    }
    
    public static function getApiSteamUserScoreResultset($release_id, $mode_id, $character_id, $seeded, $co_op, $custom, $steamid) {
        $resultset = static::getApiSteamUserResultset($release_id, $mode_id, $character_id, $seeded, $co_op, $custom, $steamid);
        
        $resultset->setName("api_score_steam_user_pbs");
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_deathless = 0");
        $resultset->addFilterCriteria("l.is_daily = 0");
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);
    
        return $resultset;
    }
    
    public static function getApiSteamUserSpeedResultset($release_id, $mode_id, $character_id, $seeded, $co_op, $custom, $steamid) {                       
        $resultset = static::getApiSteamUserResultset($release_id, $mode_id, $character_id, $seeded, $co_op, $custom, $steamid);
        
        $resultset->setName("api_speed_steam_user_pbs");
        
        $resultset->addFilterCriteria("l.is_speedrun = 1");
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);
    
        return $resultset;
    }
    
    public static function getApiSteamUserDeathlessResultset($release_id, $mode_id, $character_id, $seeded, $co_op, $custom, $steamid) {                       
        $resultset = static::getApiSteamUserResultset($release_id, $mode_id, $character_id, $seeded, $co_op, $custom, $steamid);
        
        $resultset->setName("api_deathless_steam_user_pbs");
        
        $resultset->addFilterCriteria("l.is_deathless = 1");
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);
    
        return $resultset;
    }
    
    public static function loadIntoCache() {
        $resultset = new SQL('pbs_cache');

        $resultset->addSelectFields(array(
            array(
                'field' => 'sup.steam_user_pb_id',
                'alias' => 'steam_user_pb_id',
            ),
            array(
                'field' => 'l.release_id',
                'alias' => 'release_id',
            ),
            array(
                'field' => 'l.mode_id',
                'alias' => 'mode_id',
            ),
            array(
                'field' => 'l.is_deathless',
                'alias' => 'is_deathless',
            ),
            array(
                'field' => 'l.is_speedrun',
                'alias' => 'is_speedrun',
            )
        ));
        
        $resultset->setFromTable('steam_user_pbs sup');
        
        $resultset->addJoinCriteria('leaderboards l ON l.leaderboard_id = sup.leaderboard_id');
        $resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_snapshot_id = sup.first_leaderboard_snapshot_id');
        
        $resultset->addFilterCriteria('l.is_daily = 0');
        
        $resultset->addSortCriteria("sup.leaderboard_id", "ASC");
        $resultset->addSortCriteria("ls.date", "ASC");
        $resultset->addSortCriteria("sup.steam_user_id", "ASC");
        
        $resultset->setAsCursor(100000);
        
        db()->beginTransaction();
        
        $resultset->prepareExecuteQuery();
        
        $transaction = cache('database')->transaction();
        
        $steam_user_pbs = array();
        $indexes = array();
        
        do {
            $steam_user_pbs = $resultset->getNextCursorChunk();
        
            if(!empty($steam_user_pbs)) {
                foreach($steam_user_pbs as $steam_user_pb) {   
                    $steam_user_pb_id = (int)$steam_user_pb['steam_user_pb_id'];
                    $release_id = (int)$steam_user_pb['release_id'];
                    $mode_id = (int)$steam_user_pb['mode_id'];
                    
                    $indexes[CacheNames::getPbsIndexName(array(
                        $release_id,
                        $mode_id
                    ))][] = $steam_user_pb_id;
                    
                    if(!empty($steam_user_pb['is_deathless'])) {                        
                        $indexes[CacheNames::getPbsIndexName(array(
                            $release_id,
                            $mode_id,
                            CacheNames::DEATHLESS
                        ))][] = $steam_user_pb_id;
                    }
                    else {
                        if(!empty($steam_user_pb['is_speedrun'])) {
                            $indexes[CacheNames::getPbsIndexName(array(
                                $release_id,
                                $mode_id,
                                CacheNames::SPEED
                            ))][] = $steam_user_pb_id;
                        }
                        else {
                            $indexes[CacheNames::getPbsIndexName(array(
                                $release_id,
                                $mode_id,
                                CacheNames::SCORE
                            ))][] = $steam_user_pb_id;
                        }
                    }
                }
            }
        }
        while(!empty($steam_user_pbs));
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {
                $transaction->set($key, static::encodeRecord($index_data), CacheNames::getAllPbsName());
            }
        }
        
        unset($indexes);
        
        $transaction->commit();
        
        db()->commit();
    }
}