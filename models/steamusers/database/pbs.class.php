<?php
namespace Modules\Necrolab\Models\SteamUsers\Database;

use \DateTime;
use \Framework\Data\Database\InsertQueue;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUserPb as DatabaseSteamUserPb;
use \Modules\Necrolab\Models\SteamUsers\SteamUsers as DatabaseSteamUsers;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards as DatabaseLeaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\Snapshots as DatabaseSnapshots;
use \Modules\Necrolab\Models\ExternalSites\Database\ExternalSites as DatabaseExternalSites;
use \Modules\Necrolab\Models\Characters\Database\Characters as DatabaseCharacters;
use \Modules\Necrolab\Models\Modes\Database\Modes as DatabaseModes;
use \Modules\Necrolab\Models\Leaderboards\Database\Replays as DatabaseReplays;
use \Modules\Necrolab\Models\Leaderboards\Database\Details as DatabaseDetails;
use \Modules\Necrolab\Models\Leaderboards\Database\RunResults as DatabaseRunResults;
use \Modules\Necrolab\Models\Leaderboards\Database\ReplayVersions as DatabaseReplayVersions;
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
            )
        ));
    }
    
    protected static function getBaseResultset() {
        $resultset = new SQL('steam_user_pbs');
        
        static::setSelectFields($resultset);
        
        $resultset->setFromTable('steam_user_pbs sup');
        
        return $resultset;
    }
    
    public static function getAllResultset() {
        $resultset = static::getBaseResultset();
        
        $resultset->setName('pbs');
        
        $resultset->addJoinCriteria('leaderboards l ON l.leaderboard_id = sup.leaderboard_id');
        $resultset->addJoinCriteria('releases r ON r.release_id = l.release_id');
        $resultset->addJoinCriteria('modes mo ON mo.mode_id = l.mode_id');
        $resultset->addJoinCriteria('characters c ON c.character_id = l.character_id');
        $resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = sup.leaderboard_entry_details_id');
        $resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_snapshot_id = sup.first_leaderboard_snapshot_id');
        
        $resultset->addJoinCriteria("
            steam_replays sr ON sr.steam_replay_id = sup.steam_replay_id
            LEFT JOIN run_results rr ON rr.run_result_id = sr.run_result_id
            LEFT JOIN steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id
        ");
        
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sup.steam_user_id');
        
        return $resultset;
    }
    
    public static function getAllApiResultset($release_name, $mode_name, $character_name) {
        $resultset = static::getAllResultset();
        
        $resultset->setName('api:pbs');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            ),
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            )
        ));
        
        DatabaseLeaderboards::setSelectFields($resultset);
        DatabaseCharacters::setSelectFields($resultset);
        DatabaseModes::setSelectFields($resultset);
        DatabaseSnapshots::setSelectFields($resultset);
        DatabaseReplays::setSelectFields($resultset);
        DatabaseDetails::setSelectFields($resultset);
        DatabaseRunResults::setSelectFields($resultset);
        DatabaseReplayVersions::setSelectFields($resultset);
        
        DatabaseExternalSites::addSiteUserLeftJoins($resultset);
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
        ));
        
        $resultset->addFilterCriteria('mo.name = :mode_name', array(
            ':mode_name' => $mode_name
        ));
        
        $resultset->addFilterCriteria('c.name = :character_name', array(
            ':character_name' => $character_name
        ));
        
        $resultset->addSortCriteria('ls.date', 'DESC');
        
        return $resultset;
    }
    
    public static function getAllApiScoreResultset($release_name, $mode_name, $character_name) {
        $resultset = static::getAllApiResultset($release_name, $mode_name, $character_name);
        
        $resultset->setName("api:pbs:score");
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 0");
        $resultset->addFilterCriteria("l.is_deathless = 0");
        
        return $resultset;
    }
    
    public static function getAllApiSpeedResultset($release_name, $mode_name, $character_name) {
        $resultset = static::getAllApiResultset($release_name, $mode_name, $character_name);
        
        $resultset->setName("api:pbs:speed");
        
        $resultset->addFilterCriteria("l.is_speedrun = 1");
        
        return $resultset;
    }
    
    public static function getAllApiDeathlessResultset($release_name, $mode_name, $character_name) {
        $resultset = static::getAllApiResultset($release_name, $mode_name, $character_name);
        
        $resultset->setName("api:pbs:deathless");
        
        $resultset->addFilterCriteria("l.is_deathless = 1");
        
        return $resultset;
    }
    
    public static function getApiSteamUserResultset($release_name, $mode_name, $character_name, $steamid) {
        $resultset = static::getAllApiResultset($release_name, $mode_name, $character_name);
        
        $resultset->setName("api:{$steamid}:pbs");
        
        $resultset->addFilterCriteria('su.steamid = :steamid', array(
            ':steamid' => $steamid
        ));
        
        return $resultset;
    }
    
    public static function getApiSteamUserScoreResultset($release_name, $mode_name, $character_name, $steamid) {                       
        $resultset = static::getApiSteamUserResultset($release_name, $mode_name, $character_name, $steamid);
        
        $resultset->setName("api:{$steamid}:pbs:score");
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 0");
    
        return $resultset;
    }
    
    public static function getApiSteamUserSpeedResultset($release_name, $mode_name, $character_name, $steamid) {                       
        $resultset = static::getApiSteamUserResultset($release_name, $mode_name, $character_name, $steamid);
        
        $resultset->setName("api:{$steamid}:pbs:speed");
        
        $resultset->addFilterCriteria("l.is_speedrun = 1");
    
        return $resultset;
    }
    
    public static function getApiSteamUserDeathlessResultset($release_name, $mode_name, $character_name, $steamid) {                       
        $resultset = static::getApiSteamUserResultset($release_name, $mode_name, $character_name, $steamid);
        
        $resultset->setName("api:{$steamid}:pbs:deathless");
        
        $resultset->addFilterCriteria("l.is_deathless = 1");
    
        return $resultset;
    }
}
