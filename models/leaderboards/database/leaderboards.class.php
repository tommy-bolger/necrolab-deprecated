<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Leaderboards\Leaderboards as BaseLeaderboards;
use \Modules\Necrolab\Models\Releases\Database\Releases;
use \Modules\Necrolab\Models\Characters\Database\Characters as DatabaseCharacters;
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
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'l.lbid',
                'alias' => 'lbid',
            ),
            array(
                'field' => 'l.name',
                'alias' => 'leaderboard_name',
            ),
            array(
                'field' => 'l.display_name',
                'alias' => 'leaderboard_display_name',
            ),
            array(
                'field' => 'l.url',
                'alias' => 'url',
            ),
            array(
                'field' => 'l.is_speedrun',
                'alias' => 'is_speedrun',
            ),
            array(
                'field' => 'l.is_custom',
                'alias' => 'is_custom',
            ),
            array(
                'field' => 'l.is_co_op',
                'alias' => 'is_co_op',
            ),
            array(
                'field' => 'l.is_seeded',
                'alias' => 'is_seeded',
            ),
            array(
                'field' => 'l.is_daily',
                'alias' => 'is_daily',
            ),
            array(
                'field' => 'l.daily_date',
                'alias' => 'daily_date',
            ),
            array(
                'field' => 'l.is_score_run',
                'alias' => 'is_score_run',
            ),
            array(
                'field' => 'l.is_all_character',
                'alias' => 'is_all_character',
            ),
            array(
                'field' => 'l.is_deathless',
                'alias' => 'is_deathless',
            ),
            array(
                'field' => 'l.is_story_mode',
                'alias' => 'is_story_mode',
            ),
            array(
                'field' => 'l.is_power_ranking',
                'alias' => 'is_power_ranking',
            ),
            array(
                'field' => 'l.is_daily_ranking',
                'alias' => 'is_daily_ranking',
            ),
            array(
                'field' => 'l.release_id',
                'alias' => 'release_id',
            )
        ));
    }
    
    public static function getBaseResultset() {
        $resultset = new SQL('leaderboards');
        
        static::setSelectFields($resultset);
        DatabaseCharacters::setSelectFields($resultset);
        
        $resultset->setFromTable('leaderboards l');
        
        $resultset->addJoinCriteria('characters c ON c.character_id = l.character_id');
        
        return $resultset;
    }
    
    public static function getOneResultset($lbid) {
        $resultset = static::getBaseResultset();
        
        $resultset->addFilterCriteria('l.lbid = :lbid', array(
            ':lbid' => $lbid
        ));
        
        return $resultset;
    }
    
    public static function getAllResultset($release_name) {
        $resultset = static::getBaseResultset();
        
        $resultset->addJoinCriteria('releases r ON r.release_id = l.release_id');
        $resultset->addLeftJoinCriteria('leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id');
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
        ));
        
        $resultset->addFilterCriteria('lb.leaderboards_blacklist_id IS NULL');
        
        return $resultset;
    }
    
    public static function getAllDailyResultset($release_name) {                       
        $resultset = static::getAllResultset($release_name);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 1");
        $resultset->addFilterCriteria("l.is_co_op = 0");
        $resultset->addFilterCriteria("l.is_daily_ranking = 1");
    
        return $resultset;
    }
    
    public static function getAllScoreResultset($release_name) {                       
        $resultset = static::getAllResultset($release_name);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 0");
        $resultset->addFilterCriteria("l.is_deathless = 0");
    
        return $resultset;
    }
    
    public static function getAllSpeedResultset($release_name) {                       
        $resultset = static::getAllResultset($release_name);
        
        $resultset->addFilterCriteria("l.is_speedrun = 1");
    
        return $resultset;
    }
    
    public static function getAllDeathlessResultset($release_name) {                       
        $resultset = static::getAllResultset($release_name);
        
        $resultset->addFilterCriteria("l.is_deathless = 1");
    
        return $resultset;
    }
    
    public static function getSteamUserResultset($steamid, $release_name) {
        $resultset = static::getBaseResultset();        
        
        $release = Releases::getByName($release_name);

        $parition_table_names = static::getPartitionTableNames('leaderboard_entries', new DateTime($release['start_date']), new DateTime($release['end_date']));
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = le.steam_user_id');
        $resultset->addJoinCriteria('releases r ON r.release_id = l.release_id');
        $resultset->addLeftJoinCriteria('leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id');       
        
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
        $resultset = static::getSteamUserResultset($steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 1");
        $resultset->addFilterCriteria("l.is_co_op = 0");
        $resultset->addFilterCriteria("l.is_daily_ranking = 1");
    
        return $resultset;
    }
    
    public static function getSteamUserScoreResultset($steamid, $release_name) {                       
        $resultset = static::getSteamUserResultset($steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 0");
    
        return $resultset;
    }
    
    public static function getSteamUserSpeedResultset($steamid, $release_name) {                       
        $resultset = static::getSteamUserResultset($steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_speedrun = 1");
    
        return $resultset;
    }
    
    public static function getSteamUserDeathlessResultset($steamid, $release_name) {                       
        $resultset = static::getSteamUserResultset($steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_deathless = 1");
    
        return $resultset;
    }
}