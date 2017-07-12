<?php
namespace Modules\Necrolab\Models\Achievements;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\Achievements\Achievement as AchievementRecord;

class Achievements
extends Necrolab {
    protected static $achievements = array();
    
    protected static $ids_by_name = array();
    
    protected static function loadAll() {        
        if(empty(static::$achievements)) {
            $achievements = array();
            
            $cache_key = 'achievements';
            
            $local_cache = cache('local');
        
            $achievements = $local_cache->get($cache_key);
        
            if(empty($achievements)) {
                $achievements = db()->getAll("
                    SELECT *
                    FROM achievements
                    ORDER BY achievement_id
                ");
                
                if(!empty($achievements)) {
                    $local_cache->set($cache_key, $achievements, NULL, 86400);
                }
            }
            
            if(!empty($achievements)) {
                foreach($achievements as $achievement) {
                    $achievement_id = $achievement['achievement_id'];
                
                    static::$achievements[$achievement_id] = $achievement;
                    
                    static::$ids_by_name[$achievement['name']] = $achievement_id;
                }
            }
        }
    }
    
    public static function getAll() {
        static::loadAll();
        
        return static::$achievements;
    }
    
    public static function getById($achievement_id) {
        static::loadAll();
        
        $achievement_record = array();
        
        if(!isset(static::$achievements[$achievement_id])) {
            $achievement_record = static::$achievements[$achievement_id];
        }
        
        return $achievement_record;
    }
    
    public static function getIdByName($achievement_name) {
        static::loadAll();
    
        $achievement_id = NULL;

        if(isset(static::$ids_by_name[$achievement_name])) {
            $achievement_id = static::$ids_by_name[$achievement_name];
        }
        
        return $achievement_id;
    }
    
    public static function getByName($name) {
        static::loadAll();
        
        $achievement_record = array();
        
        if(!empty(static::$achievements)) {
            foreach(static::$achievements as $achievement) {
                if($achievement['name'] == $name) {
                    $achievement_record = $achievement;
                    
                    break;
                }
            }
        }
        
        return $achievement_record;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return array(
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name'],
            'description' => $data_row['description'],
            'icon_url' => $data_row['icon_url'],
            'icon_gray_url' => $data_row['icon_gray_url']
        );
    }
    
    public static function save(AchievementRecord $achievement, $cache_query_name = NULL) {
        $achievement_record = $achievement->toArray();
        
        $achievement_id = db()->insert('achievements', $achievement_record, $cache_query_name);
        
        static::$achievements[] = $achievement_record;
        
        return $achievement_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE achievements;");
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'a.name',
                'alias' => 'name',
            ),
            array(
                'field' => 'a.display_name',
                'alias' => 'display_name',
            ),
            array(
                'field' => 'a.description',
                'alias' => 'description',
            ),
            array(
                'field' => 'a.icon_url',
                'alias' => 'icon_url',
            ),
            array(
                'field' => 'a.icon_gray_url',
                'alias' => 'icon_gray_url',
            )
        ));
    }
    
    public static function getAllBaseResultset() {    
        $resultset = new SQL("achievements");
        
        $resultset->setBaseQuery("
            SELECT *
            FROM achievements
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addSortCriteria('start_date', 'DESC');
        
        return $resultset;
    }
}