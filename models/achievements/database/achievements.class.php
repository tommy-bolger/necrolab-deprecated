<?php
namespace Modules\Necrolab\Models\Achievements\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Achievements\Achievements as BaseAchievements;
use \Modules\Necrolab\Models\Achievements\Database\RecordModels\Achievement as AchievementRecord;

class Achievements
extends BaseAchievements {
    protected static function loadAll() {        
        if(empty(static::$achievements)) {        
            $achievements = db()->getAll("
                SELECT *
                FROM achievements
                ORDER BY achievement_id
            ");

            if(!empty($achievements)) {
                foreach($achievements as $achievement) {
                    $achievement_id = $achievement['achievement_id'];
                
                    static::$achievements[$achievement_id] = $achievement;
                    
                    static::$ids_by_name[$achievement['name']] = $achievement_id;
                }
            }
        }
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
                'alias' => 'achievement_name',
            ),
            array(
                'field' => 'a.display_name',
                'alias' => 'achievement_display_name',
            ),
            array(
                'field' => 'a.description',
                'alias' => 'a.achievement_description',
            ),
            array(
                'field' => 'a.icon_url',
                'alias' => 'achieved_icon_url',
            ),
            array(
                'field' => 'a.icon_gray_url',
                'alias' => 'not_achieved_icon_url',
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