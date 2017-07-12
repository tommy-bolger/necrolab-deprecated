<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis\Hybrid as HybridResultset;
use \Modules\Necrolab\Models\Leaderboards\Leaderboards as BaseLeaderboards;
use \Modules\Necrolab\Models\Modes;
use \Modules\Necrolab\Models\Characters;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\Leaderboard as DatabaseLeaderboard;
use \Modules\Necrolab\Models\Leaderboards\CacheNames;

class Leaderboards
extends BaseLeaderboards {    
    public static function load($leaderboard_id) {       
        if(empty(static::$leaderboards[$leaderboard_id])) {            
            $cache_key = CacheNames::getRecordsName();
            
            $local_cache = cache('local');
            
            $local_key = "{$cache_key}:{$leaderboard_id}";
        
            $leaderboard = $local_cache->get($local_key);
        
            if(empty($leaderboard)) {
                $leaderboard = db()->getRow("
                    SELECT *
                    FROM leaderboards
                    WHERE leaderboard_id = :leaderboard_id
                ", array(
                    ':leaderboard_id' => $leaderboard_id
                ));
                
                if(!empty($leaderboard)) {
                    $local_cache->set($local_key, $leaderboard, NULL, 86400);
                }
            }
            
            if(!empty($leaderboard)) {
                static::$leaderboards[$leaderboard_id] = $leaderboard;
            }
        }
    }
    
    public static function loadIds() {        
        if(empty(static::$ids)) {
            $ids = array();
            
            $cache_key = CacheNames::getIdsName();
            
            $local_cache = cache('local');
        
            $ids = $local_cache->get($cache_key);
        
            if(empty($ids)) {
                $ids = db()->getMappedColumn("
                    SELECT 
                        lbid,
                        leaderboard_id
                    FROM leaderboards
                ");
                
                if(!empty($ids)) {
                    $local_cache->set($cache_key, $ids, NULL, 60);
                }
            }
            
            if(!empty($ids)) {
                static::$ids = $ids;
            }
        }
    }
    
    public static function save(DatabaseLeaderboard $leaderboard_record) {
        $lbid = $leaderboard_record->lbid;
        
        $leaderboard_fields = $leaderboard_record->toArray(false);
        
        $leaderboard_id = static::getId($lbid);
        
        if(empty($leaderboard_id)) {
            $leaderboard_id = db()->insert('leaderboards', $leaderboard_fields, 'leaderboard_insert');
            
            $leaderboard_fields['leaderboard_id'] = $leaderboard_id;
            
            static::$ids[$lbid] = $leaderboard_id;
        }
        else {        
            db()->update('leaderboards', $leaderboard_fields, array(
                'leaderboard_id' => $leaderboard_id
            ), array(), 'leaderboard_update');
        }
        
        return $leaderboard_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE leaderboards;");
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'l.leaderboard_id',
                'alias' => 'leaderboard_id',
            ),
            array(
                'field' => 'l.lbid',
                'alias' => 'lbid',
            ),
            array(
                'field' => 'l.name',
                'alias' => 'name',
            ),
            array(
                'field' => 'l.display_name',
                'alias' => 'display_name',
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
                'field' => 'l.is_deathless',
                'alias' => 'is_deathless',
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
            ),
            array(
                'field' => 'l.mode_id',
                'alias' => 'mode_id',
            ),
            array(
                'field' => 'l.character_id',
                'alias' => 'character_id',
            )
        ));
    }
    
    public static function getBaseResultset() {
        $resultset = new SQL('leaderboards');
        
        static::setSelectFields($resultset);
        
        $resultset->setFromTable('leaderboards l');
        
        return $resultset;
    }
    
    public static function getOneResultset($lbid) {
        $resultset = static::getBaseResultset();
        
        $resultset->addJoinCriteria('modes mo ON mo.mode_id = l.mode_id');
        
        $resultset->addFilterCriteria('l.lbid = :lbid', array(
            ':lbid' => $lbid
        ));
        
        return $resultset;
    }
    
    public static function getAllResultset($release_id, $mode_id) {
        $resultset = static::getBaseResultset();
        
        $resultset->addFilterCriteria('l.release_id = :release_id', array(
            ':release_id' => $release_id
        ));
        
        $resultset->addFilterCriteria('l.mode_id = :mode_id', array(
            ':mode_id' => $mode_id
        ));
        
        $resultset->addFilterCriteria('l.is_daily = 0');
        
        return $resultset;
    
        /*$resultset = new HybridResultset(CacheNames::getRecordsName(), cache(), cache('local'));
        
        $resultset->setCacheResultsetName(CacheNames::getRecordsName());
        
        $resultset->setIndexName(CacheNames::getRecordsIndexName(array(
            $release_id,
            $mode_id
        )));
        
        return $resultset;*/
    }
    
    public static function getAllDailyResultset($release_id, $mode_id) { 
        $resultset = static::getBaseResultset();

        $resultset->addFilterCriteria('l.release_id = :release_id', array(
            ':release_id' => $release_id
        ));
        
        $resultset->addFilterCriteria('l.mode_id = :mode_id', array(
            ':mode_id' => $mode_id
        ));
        
        $resultset->addFilterCriteria("l.is_daily_ranking = 1");
        $resultset->addFilterCriteria("l.is_daily = 1");
        $resultset->addFilterCriteria("l.is_co_op = 0");
        $resultset->addFilterCriteria("l.is_score_run = 1");
        
        $resultset->addFilterCriteria("l.daily_date <= :daily_date", array(
            ':daily_date' => date('Y-m-d')
        ));
        
        $resultset->setSortCriteria('l.daily_date', 'DESC');
        
        return $resultset;
    
        /*$resultset = new HybridResultset('daily_leaderboards', cache(), cache('local'));
        
        $resultset->setCacheResultsetName(CacheNames::getRecordsName());
        
        $resultset->setIndexName(CacheNames::getRecordsIndexName(array(
            $release_id,
            $mode_id,
            'daily'
        )));
        
        return $resultset;*/
    }
    
    public static function getAllDailyDatesResultset($release_id, $mode_id) { 
        $resultset = static::getAllDailyResultset($release_id, $mode_id);
        
        $resultset->setSelectField('l.daily_date', 'daily_date');  
        
        $resultset->setSortCriteria('l.daily_date', 'DESC');
        
        return $resultset;
    }
    
    public static function getAllScoreResultset($release_id, $mode_id) { 
        $resultset = static::getAllResultset($release_id, $mode_id);
    
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 0");
        $resultset->addFilterCriteria("l.is_deathless = 0");
        
        return $resultset;
    
        /*$resultset = new HybridResultset(CacheNames::getRecordsName(), cache(), cache('local'));
        
        $resultset->setCacheResultsetName(CacheNames::getRecordsName());
        
        $resultset->setIndexName(CacheNames::getRecordsIndexName(array(
            $release_id,
            $mode_id,
            'score'
        )));
        
        return $resultset;*/
    }
    
    public static function getAllSpeedResultset($release_id, $mode_id) {     
        $resultset = static::getAllResultset($release_id, $mode_id);
    
        $resultset->addFilterCriteria("l.is_speedrun = 1");
        
        return $resultset;
    
        /*$resultset = new HybridResultset(CacheNames::getRecordsName(), cache(), cache('local'));
        
        $resultset->setCacheResultsetName(CacheNames::getRecordsName());
        
        $resultset->setIndexName(CacheNames::getRecordsIndexName(array(
            $release_id,
            $mode_id,
            'speed'
        )));
        
        return $resultset;*/
    }
    
    public static function getAllDeathlessResultset($release_id, $mode_id) { 
        $resultset = static::getAllResultset($release_id, $mode_id);
    
        $resultset->addFilterCriteria("l.is_deathless = 1");
        
        return $resultset;
    
        /*$resultset = new HybridResultset(CacheNames::getRecordsName(), cache(), cache('local'));
        
        $resultset->setCacheResultsetName(CacheNames::getRecordsName());
        
        $resultset->setIndexName(CacheNames::getRecordsIndexName(array(
            $release_id,
            $mode_id,
            'deathless'
        )));
        
        return $resultset;*/
    }
    
    protected static function getSteamUserBaseResulset($steamid, $release_id, $mode_id) {
        $resultset = new SQL("leaderboards_users_{$steamid}");   
        
        static::setSelectFields($resultset);
        
        $resultset->setFromTable('steam_users su');
        $resultset->addJoinCriteria('steam_user_pbs sup ON sup.steam_user_id = su.steam_user_id');
        $resultset->addJoinCriteria('leaderboards l ON l.leaderboard_id = sup.leaderboard_id');
        $resultset->addLeftJoinCriteria('leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id');       
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->addFilterCriteria('l.release_id = ?', array(
            $release_id
        ));
        
        $resultset->addFilterCriteria('l.mode_id = ?', array(
            $mode_id
        ));
        
        $resultset->addFilterCriteria('lb.leaderboards_blacklist_id IS NULL');
        
        $resultset->addSortCriteria('l.character_id');
        $resultset->addSortCriteria('l.leaderboard_id');
        
        return $resultset;
    }
    
    public static function getSteamUserResultset($steamid, $release_id, $mode_id) {        
        $resultset = static::getSteamUserBaseResulset($steamid, $release_id, $mode_id);
        
        $resultset->addFilterCriteria('l.is_daily = 0');
        
        return $resultset;
    }
    
    public static function getSteamUserDailyResultset($steamid, $release_id, $mode_id) {                       
        $resultset = static::getSteamUserBaseResulset($steamid, $release_id, $mode_id);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 1");
        $resultset->addFilterCriteria("l.is_co_op = 0");
        $resultset->addFilterCriteria("l.is_daily_ranking = 1");
        
        $resultset->addFilterCriteria("l.daily_date <= ?", array(
            date('Y-m-d')
        ));
        
        $resultset->setSortCriteria('l.daily_date', 'DESC');
    
        return $resultset;
    }
    
    public static function getSteamUserDailyDatesResultset($steamid, $release_id, $mode_id) {
        $resultset = static::getSteamUserDailyResultset($steamid, $release_id, $mode_id);
        
        $resultset->setSelectField('l.daily_date', 'daily_date');
    
        return $resultset;
    }
    
    public static function getSteamUserScoreResultset($steamid, $release_id, $mode_id) {                       
        $resultset = static::getSteamUserResultset($steamid, $release_id, $mode_id);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 0");
        $resultset->addFilterCriteria("l.is_deathless = 0");
    
        return $resultset;
    }
    
    public static function getSteamUserSpeedResultset($steamid, $release_id, $mode_id) {                       
        $resultset = static::getSteamUserResultset($steamid, $release_id, $mode_id);
        
        $resultset->addFilterCriteria("l.is_speedrun = 1");
    
        return $resultset;
    }
    
    public static function getSteamUserDeathlessResultset($steamid, $release_id, $mode_id) {                       
        $resultset = static::getSteamUserResultset($steamid, $release_id, $mode_id);
        
        $resultset->addFilterCriteria("l.is_deathless = 1");
    
        return $resultset;
    }
    
    /*public static function loadIntoCache() {     
        $resultset = new SQL("leaderboards");
            
        static::setSelectFields($resultset);
        
        $resultset->setFromTable('leaderboards l');
        
        $resultset->addLeftJoinCriteria("leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id");
        
        $resultset->addFilterCriteria("lb.leaderboards_blacklist_id IS NULL");

        $resultset->setAsCursor(1000);
        
        db()->beginTransaction();
        
        $transaction = cache()->transaction();
        
        $resultset->prepareExecuteQuery();
        
        $current_leaderboard_snapshot_id = NULL;
        $current_leaderboard_id = NULL;
        
        $records_cache_name = CacheNames::getRecordsName();
        
        $entries = array();
        $indexes = array();
        $ids = array();
        
        do {
            $entries = $resultset->getNextCursorChunk();
        
            if(!empty($entries)) {
                foreach($entries as $entry) {
                    $leaderboard_id = (int)$entry['leaderboard_id'];
                    $lbid = (int)$entry['lbid'];
                    $release_id = (int)$entry['release_id'];
                    $mode_id = (int)$entry['mode_id'];
                    
                    $is_daily = $entry['is_daily'];
                    $is_daily_ranking = $entry['is_daily_ranking'];
                    $is_score_run = $entry['is_score_run'];
                    $is_speedrun = $entry['is_speedrun'];
                    $is_deathless = $entry['is_deathless'];
                    
                    $type = 'score';
                    
                    if(empty($is_daily)) {
                        if(!empty($is_deathless)) {
                            $type = 'deathless';
                        }
                        else {
                            if(!empty($is_speedrun)) {
                                $type = 'speed';
                            }
                        }
                    }
                    else {
                        if(!empty($is_daily_ranking)) {
                            $type = 'daily';
                        }
                    }
                    
                    $transaction->hSet($records_cache_name, $leaderboard_id, static::encodeRecord($entry));
                    
                    $indexes[CacheNames::getRecordsIndexName(array(
                        $release_id,
                        $mode_id,
                        $type
                    ))][] = $leaderboard_id;
                    
                    $indexes[CacheNames::getRecordsIndexName(array(
                        $release_id,
                        $mode_id
                    ))][] = $leaderboard_id;
                    
                    $ids[$lbid] = $leaderboard_id;
                }
            }
        }
        while(!empty($entries));
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {
                $transaction->set($key, static::encodeRecord($index_data));
            }
        }
        
        if(!empty($ids)) {
            $transaction->set(CacheNames::getIdsName(), static::encodeRecord($ids));
        }
        
        $transaction->commit();
        
        db()->commit();
    }*/
}