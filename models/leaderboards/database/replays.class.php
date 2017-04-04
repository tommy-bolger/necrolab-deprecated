<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Leaderboards\Replays as BaseReplays;
use \Modules\Necrolab\Models\Leaderboards\Database\RunResults as DatabaseRunResults;
use \Modules\Necrolab\Models\Leaderboards\Database\ReplayVersions as DatabaseReplayVersions;
use \Modules\Necrolab\Models\Characters\Database\Characters as DatabaseCharacters;
use \Modules\Necrolab\Models\Modes\Database\Modes as DatabaseModes;
use \Modules\Necrolab\Models\SteamUsers\Database\Pbs as DatabaseSteamUserPbs;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\SteamReplay;

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
    
    public static function save($ugcid, $steam_user_id) {
        $steam_replay_id = static::get($ugcid);
        
        if(empty($steam_replay_id)) {
            $steam_replay = new SteamReplay();
        
            $steam_replay->ugcid = $ugcid;
            $steam_replay->steam_user_id = $steam_user_id;
            $steam_replay->downloaded = 0;
            $steam_replay->invalid = 0;
            $steam_replay->uploaded_to_s3 = 0;
        
            $steam_replay_id = db()->insert('steam_replays', $steam_replay->toArray(), 'replay_insert');
            
            static::$replays[$ugcid] = $steam_replay_id;
        }
    
        return $steam_replay_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE steam_replays;");
    }
    
    public static function updateBatch($steam_replay_id, SteamReplay $steam_replay) { 
        $array_record = $steam_replay->toArray();
        
        unset($array_record['ugcid']);
        unset($array_record['steam_user_id']);
    
        db()->update('steam_replays', $array_record, array(
            'steam_replay_id' => $steam_replay_id
        ), '', 'steam_replay_update');
    }
    
    public static function update($steam_replay_id, SteamReplay $steam_replay) { 
        $array_record = $steam_replay->toArray(false);
        
        if(array_key_exists('ugcid', $array_record)) {
            unset($array_record['ugcid']);
        }
        
        if(array_key_exists('steam_user_id', $array_record)) {
            unset($array_record['steam_user_id']);
        }
    
        db()->update('steam_replays', $array_record, array(
            'steam_replay_id' => $steam_replay_id
        ));
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
        
        $resultset->setBaseQuery("
            DECLARE saved_replays_data CURSOR FOR
            {$resultset->getBaseQuery()}
        ");
        
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
    
    public static function getAllResultset() {
        $resultset = new SQL('replays:entries');
        
        static::setSelectFields($resultset);
        DatabaseRunResults::setSelectFields($resultset);
        DatabaseReplayVersions::setSelectFields($resultset);
        DatabaseSteamUserPbs::setSelectFields($resultset);
        Leaderboards::setSelectFields($resultset);
        DatabaseCharacters::setSelectFields($resultset);
        DatabaseModes::setSelectFields($resultset);
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
    
    public static function getApiAllResultset($release_name) {
        $resultset = static::getAllResultset();
        
        $resultset->addJoinCriteria('releases r ON r.release_id = l.release_id');
        
        $resultset->addFilterCriteria("r.name = :release_name", array(
            ':release_name' => $release_name
        ));
        
        return $resultset;
    }
    
    public static function getOneResultset($ugcid) {
        $resultset = static::getAllResultset();
        
        $resultset->setName("replays:entries:{$ugcid}");
        
        $resultset->addFilterCriteria("sr.ugcid = :ugcid", array(
            ':ugcid' => $ugcid
        ));
        
        return $resultset;
    }
    
    public static function getSteamUserResultset($release_name, $steamid) {
        $resultset = static::getApiAllResultset($release_name);
        
        $resultset->setName("steam_users:{$steamid}:replays:entries");
        
        $resultset->addFilterCriteria("su.steamid = :steamid", array(
            ':steamid' => $steamid
        ));
        
        return $resultset;
    }
}