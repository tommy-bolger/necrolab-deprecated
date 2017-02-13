<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Leaderboards\Leaderboards as BaseLeaderboards;
use \Modules\Necrolab\Models\Releases\Database\Releases;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\Leaderboard as DatabaseLeaderboard;

class Leaderboards
extends BaseLeaderboards {
    public static function loadAll() {
        if(empty(static::$leaderboards)) {        
            $leaderboards = db()->getGroupedRows("
                SELECT 
                    lbid,
                    c.name as character_name,
                    l.*
                FROM leaderboards l
                JOIN characters c ON c.character_id = l.character_id
            ");
            
            if(!empty($leaderboards)) {
                static::$leaderboards = $leaderboards;
            }
        }
    }
    
    public static function save(DatabaseLeaderboard $leaderboard_record) {
        $lbid = $leaderboard_record->lbid;
    
        $leaderboard = static::get($lbid);
        
        $leaderboard_fields = $leaderboard_record->toArray(false);
        
        $leaderboard_id = NULL;
        
        if(empty($leaderboard)) {
            $leaderboard_id = db()->insert('leaderboards', $leaderboard_fields, 'leaderboard_insert');
            
            $leaderboard_fields['leaderboard_id'] = $leaderboard_id;
            
            static::$leaderboards[$lbid] = $leaderboard_fields;
        }
        else {
            $leaderboard_id = $leaderboard['leaderboard_id'];
        
            db()->update('leaderboards', $leaderboard_fields, array(
                'leaderboard_id' => $leaderboard_id
            ), array(), 'leaderboard_update');
            
            static::$leaderboards[$lbid] = array_merge(static::$leaderboards[$lbid], $leaderboard_fields);
        }
        
        return $leaderboard_id;
    }
    
    public static function getAllBaseResultset($release_name) {
        $resultset = new SQL('leaderboards');
        
        $resultset->setBaseQuery("
            SELECT
                l.lbid,
                l.name,
                l.display_name,
                c.name AS character_name,
                l.url,
                l.is_speedrun,
                l.is_custom,
                l.is_co_op,
                l.is_seeded,
                l.is_daily,
                l.daily_date,
                l.is_score_run,
                l.is_all_character,
                l.is_deathless,
                l.is_story_mode,
                l.is_power_ranking,
                l.is_daily_ranking
            FROM leaderboards l
            JOIN characters c ON c.character_id = l.character_id
            JOIN releases r ON r.release_id = l.release_id
            LEFT JOIN leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
        ));
        
        $resultset->addFilterCriteria('lb.leaderboards_blacklist_id IS NULL');
        
        return $resultset;
    }
    
    public static function getAllDailyResultset($release_name) {                       
        $resultset = static::getAllBaseResultset($release_name);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 1");
        $resultset->addFilterCriteria("l.is_co_op = 0");
        $resultset->addFilterCriteria("l.is_daily_ranking = 1");
    
        return $resultset;
    }
    
    public static function getAllScoreResultset($release_name) {                       
        $resultset = static::getAllBaseResultset($release_name);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 0");
    
        return $resultset;
    }
    
    public static function getAllSpeedResultset($release_name) {                       
        $resultset = static::getAllBaseResultset($release_name);
        
        $resultset->addFilterCriteria("l.is_speedrun = 1");
    
        return $resultset;
    }
    
    public static function getAllDeathlessResultset($release_name) {                       
        $resultset = static::getAllBaseResultset($release_name);
        
        $resultset->addFilterCriteria("l.is_deathless = 1");
    
        return $resultset;
    }
    
    public static function getSteamUserBaseResultset($steamid, $release_name) {
        $resultset = new SQL('leaderboards');
        
        $resultset->setBaseQuery("
            SELECT DISTINCT 
                l.leaderboard_id,
                l.lbid,
                l.name,
                l.display_name,
                c.name AS character_name,
                l.url,
                l.is_speedrun,
                l.is_custom,
                l.is_co_op,
                l.is_seeded,
                l.is_daily,
                l.daily_date,
                l.is_score_run,
                l.is_all_character,
                l.is_deathless,
                l.is_story_mode,
                l.is_power_ranking,
                l.is_daily_ranking
            FROM leaderboards l
            JOIN leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            JOIN {{PARTITION_TABLE}} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            JOIN characters c ON c.character_id = l.character_id
            JOIN releases r ON r.release_id = l.release_id
            JOIN steam_users su ON su.steam_user_id = le.steam_user_id
            LEFT JOIN leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id
            {{WHERE_CRITERIA}}
        ");
        
        $release = Releases::getByName($release_name);

        $parition_table_names = static::getPartitionTableNames('leaderboard_entries', new DateTime($release['start_date']), new DateTime($release['end_date']));
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->addFilterCriteria('r.name = ?', array(
            $release_name
        ));
        
        $resultset->addFilterCriteria('lb.leaderboards_blacklist_id IS NULL');
        
        return $resultset;
    }
    
    public static function getSteamUserDailyResultset($steamid, $release_name) {                       
        $resultset = static::getSteamUserBaseResultset($steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 1");
        $resultset->addFilterCriteria("l.is_co_op = 0");
        $resultset->addFilterCriteria("l.is_daily_ranking = 1");
    
        return $resultset;
    }
    
    public static function getSteamUserScoreResultset($steamid, $release_name) {                       
        $resultset = static::getSteamUserBaseResultset($steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 0");
    
        return $resultset;
    }
    
    public static function getSteamUserSpeedResultset($steamid, $release_name) {                       
        $resultset = static::getSteamUserBaseResultset($steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_speedrun = 1");
    
        return $resultset;
    }
    
    public static function getSteamUserDeathlessResultset($steamid, $release_name) {                       
        $resultset = static::getSteamUserBaseResultset($steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_deathless = 1");
    
        return $resultset;
    }
}