<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;
use \Modules\Necrolab\Models\Rankings\Rankings as BaseRankings;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers;

class Rankings
extends BaseRankings {
    protected static function load(DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        
        if(empty(static::$rankings[$date_formatted])) {
            $ranking = db()->getRow("
                SELECT *
                FROM power_rankings
                WHERE date = :date
            ", array(
                ':date' => $date_formatted
            ));
            
            if(!empty($ranking)) {
                static::$rankings[$date_formatted] = $ranking;
            }
        }
    }

    public static function save(DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        $power_ranking = static::get($date);
        
        $current_time = date('Y-m-d H:i:s');
        
        $power_ranking_id = NULL;
    
        if(empty($power_ranking)) {
            $record = array(
                'created' => $current_time,
                'date' => $date_formatted
            );
        
            $power_ranking_id = db()->insert('power_rankings', $record);
            
            $record['power_ranking_id'] = $power_ranking_id;
            
            static::$rankings[$date_formatted] = $record;
        }
        else {      
            $power_ranking_id = $power_ranking['power_ranking_id'];
        
            db()->update('power_rankings', array(
                'updated' => $current_time
            ), array(
                'power_ranking_id' => $power_ranking_id
            ));
            
            static::$rankings[$date_formatted]['updated'] = $current_time;
        }
        
        return $power_ranking_id;
    }
    
    public static function getLastRefreshed() {
        return NULL;
    }
}