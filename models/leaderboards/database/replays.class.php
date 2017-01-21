<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Leaderboards\Replays as BaseReplays;
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
        
            $steam_replay_id = db()->insert('steam_replays', $steam_replay->toArray(), 'replay_insert');
            
            static::$replays[$ugcid] = $steam_replay_id;
        }
    
        return $steam_replay_id;
    }
    
    public static function updateBatch($steam_replay_id, SteamReplay $steam_replay) { 
        $array_record = $steam_replay->toArray();
        
        unset($array_record['ugcid']);
        unset($array_record['steam_user_id']);
    
        db()->update('steam_replays', $array_record, array(
            'steam_replay_id' => $steam_replay_id
        ), '', 'steam_replay_update');
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
        
        return $resultset;
    }
}