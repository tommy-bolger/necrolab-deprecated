<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Dailies\Rankings\Rankings as BaseRankings;

class Rankings
extends BaseRankings {
    protected static function load($release_id, $daily_ranking_day_type_id, DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        
        if(empty(static::$rankings[$release_id][$date_formatted][$daily_ranking_day_type_id])) {
            $ranking = db()->getRow("
                SELECT *
                FROM daily_rankings
                WHERE date = :date
                    AND daily_ranking_day_type_id = :daily_ranking_day_type_id
            ", array(
                ':date' => $date_formatted,
                ':daily_ranking_day_type_id' => $daily_ranking_day_type_id
            ));
            
            if(!empty($ranking)) {
                static::$rankings[$release_id][$date_formatted][$daily_ranking_day_type_id] = $ranking;
            }
        }
    }

    public static function save($release_id, $daily_ranking_day_type_id, DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        $daily_ranking = static::get($release_id, $daily_ranking_day_type_id, $date);
        
        $current_time = date('Y-m-d H:i:s');
        
        $daily_ranking_id = NULL;
    
        if(empty($daily_ranking)) {
            $record = array(
                'daily_ranking_day_type_id' => $daily_ranking_day_type_id,
                'release_id' => $release_id,
                'created' => $current_time,
                'date' => $date_formatted
            );
        
            $daily_ranking_id = db()->insert('daily_rankings', $record);
            
            $record['daily_ranking_id'] = $daily_ranking_id;
            
            static::$rankings[$release_id][$date_formatted][$daily_ranking_day_type_id] = $record;
        }
        else {      
            $daily_ranking_id = $daily_ranking['daily_ranking_id'];
        
            db()->update('daily_rankings', array(
                'updated' => $current_time
            ), array(
                'daily_ranking_id' => $daily_ranking_id
            ));
            
            static::$rankings[$release_id][$date_formatted][$daily_ranking_day_type_id]['updated'] = $current_time;
        }
        
        return $daily_ranking_id;
    }
    
    public static function getBaseResultset($number_of_days = NULL) {
        if(empty($number_of_days)) {
            $number_of_days = 0;
        }
    
        $resultset = new SQL('daily_rankings');
    
        $resultset->setBaseQuery("
            SELECT *
            FROM daily_rankings dr
            JOIN daily_ranking_day_types drdt ON drdt.daily_ranking_day_type_id = dr.daily_ranking_day_type_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('drdt.number_of_days = :number_of_days', array(
            ':number_of_days' => $number_of_days
        ));
        
        $resultset->setSortCriteria('date', 'ASC'); 
        
        return $resultset;
    }
}