<?php
namespace Modules\Necrolab\Models\SteamUsers\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUserPb as DatabaseSteamUserPb;
use \Modules\Necrolab\Models\SteamUsers\SteamUsers as DatabaseSteamUsers;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards as DatabaseLeaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\Snapshots as DatabaseSnapshots;
use \Modules\Necrolab\Models\ExternalSites\Database\ExternalSites as DatabaseExternalSites;
use \Modules\Necrolab\Models\Characters\Database\Characters as DatabaseCharacters;
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
            $database = db();
        
            $pb_ids = $database->prepareExecuteQuery("
                SELECT
                    steam_user_pb_id,
                    leaderboard_id,
                    steam_user_id,
                    score
                FROM steam_user_pbs
            ");
            
            while($pb_id = $database->getStatementRow($pb_ids)) {
                static::addId($pb_id['leaderboard_id'], $pb_id['steam_user_id'], $pb_id['score'], $pb_id['steam_user_pb_id']);
            }
        }
    }
    
    public static function save(DatabaseSteamUserPb $steam_user_pb, $cache_query_name = NULL) {
        $steam_user_pb_id = static::getId($steam_user_pb->leaderboard_id, $steam_user_pb->steam_user_id, $steam_user_pb->score);
        
        $pb_record = array();
            
        if(!empty($cache_query_name)) {
            $cache_query_name .= '_insert';
            
            $pb_record = $steam_user_pb->toArray();
        }
        else {
            $pb_record = $steam_user_pb->toArray(false);
        }
    
        $steam_user_pb_id = db()->insert('steam_user_pbs', $pb_record, $cache_query_name);
        
        static::addId($steam_user_pb->leaderboard_id, $steam_user_pb->steam_user_id, $steam_user_pb->score, $steam_user_pb_id);
        
        return $steam_user_pb_id;
    }
    
    public static function update($steam_user_pb_id, DatabaseSteamUserPb $steam_user_pb, $cache_query_name = NULL) {                        
        $pb_record = array();
        
        if(!empty($cache_query_name)) {            
            $pb_record = $steam_user_pb->toArray();
        }
        else {
            $pb_record = $steam_user_pb->toArray(false);
        }
        
        if(array_key_exists('leaderboard_id', $pb_record)) {
            unset($pb_record['leaderboard_id']);
        }
        
        if(array_key_exists('steam_user_id', $pb_record)) {
            unset($pb_record['steam_user_id']);
        }
        
        if(array_key_exists('score', $pb_record)) {
            unset($pb_record['score']);
        }
    
        db()->update('steam_user_pbs', $pb_record, array(
            'steam_user_pb_id' => $steam_user_pb_id
        ), array(), $cache_query_name);
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
        $resultset->addJoinCriteria('characters c ON c.character_id = l.character_id');
        $resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_snapshot_id = sup.first_leaderboard_snapshot_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sup.steam_user_id');
        $resultset->addJoinCriteria('steam_replays sr ON sr.steam_replay_id = sup.steam_replay_id');
        $resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = sup.leaderboard_entry_details_id');
        
        $resultset->addLeftJoinCriteria('run_results rr ON rr.run_result_id = sr.run_result_id');
        $resultset->addLeftJoinCriteria('steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id');
        
        return $resultset;
    }
    
    public static function getAllApiResultset($release_name) {
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
        DatabaseSnapshots::setSelectFields($resultset);
        DatabaseReplays::setSelectFields($resultset);
        DatabaseDetails::setSelectFields($resultset);
        DatabaseRunResults::setSelectFields($resultset);
        DatabaseReplayVersions::setSelectFields($resultset);
        
        DatabaseExternalSites::addSiteUserLeftJoins($resultset);
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
        ));
        
        $resultset->addSortCriteria('ls.date', 'ASC');
        
        return $resultset;
    }
    
    public static function getApiSteamUserResultset($release_name, $steamid) {
        $resultset = static::getAllApiResultset($release_name);
        
        $resultset->setName("api:{$steamid}:pbs");
        
        $resultset->addFilterCriteria('su.steamid = :steamid', array(
            ':steamid' => $steamid
        ));
        
        return $resultset;
    }
}
