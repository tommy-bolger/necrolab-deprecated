<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Dailies\Rankings\Rankings as BaseRankings;

class Rankings
extends BaseRankings {
    protected static function load($daily_ranking_day_type_id, DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        
        if(empty(static::$rankings[$date_formatted][$daily_ranking_day_type_id])) {
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
                static::$rankings[$date_formatted][$daily_ranking_day_type_id] = $ranking;
            }
        }
    }

    public static function save($daily_ranking_day_type_id, DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        $daily_ranking = static::get($daily_ranking_day_type_id, $date);
        
        $current_time = date('Y-m-d H:i:s');
        
        $daily_ranking_id = NULL;
    
        if(empty($daily_ranking)) {
            $record = array(
                'daily_ranking_day_type_id' => $daily_ranking_day_type_id,
                'created' => $current_time,
                'date' => $date_formatted
            );
        
            $daily_ranking_id = db()->insert('daily_rankings', $record);
            
            $record['daily_ranking_id'] = $daily_ranking_id;
            
            static::$rankings[$date_formatted][$daily_ranking_day_type_id] = $record;
        }
        else {      
            $daily_ranking_id = $daily_ranking['daily_ranking_id'];
        
            db()->update('daily_rankings', array(
                'updated' => $current_time
            ), array(
                'daily_ranking_id' => $daily_ranking_id
            ));
            
            static::$rankings[$date_formatted][$daily_ranking_day_type_id]['updated'] = $current_time;
        }
        
        return $daily_ranking_id;
    }

    public static function getEntriesBaseResultset() {        
        $resultset = new SQL('daily_ranking_entries');
        
        $resultset->setBaseQuery("
            SELECT
                dre.rank,
                dre.first_place_ranks,
                dre.top_5_ranks,
                dre.top_10_ranks,
                dre.top_20_ranks,
                dre.top_50_ranks,
                dre.top_100_ranks,
                dre.total_points,
                dre.points_per_day,
                dre.total_dailies,
                dre.total_wins,
                dre.average_place,
                dr.daily_ranking_id,
                dre.daily_ranking_entry_id,
                dre.steam_user_id,
                drdt.daily_ranking_day_type_id,
                drdt.number_of_days,
                su.steamid
            FROM daily_rankings dr
            JOIN daily_ranking_entries dre ON dre.daily_ranking_id = dr.daily_ranking_id
            JOIN steam_users su ON su.steam_user_id = dre.steam_user_id
            LEFT JOIN daily_ranking_day_types drdt ON drdt.daily_ranking_day_type_id = dr.daily_ranking_day_type_id
            {{WHERE_CRITERIA}}
        ");
    
        return $resultset;
    }

    public static function getLatestEntriesResultset($number_of_days = NULL) {
        $resultset = static::getEntriesBaseResultset();
        
        $resultset->addFilterCriteria("dr.latest = 1");
        
        if(empty($number_of_days)) {
            $resultset->addFilterCriteria('dr.daily_ranking_day_type_id IS NULL');
        }
        else {
            $resultset->addFilterCriteria('', array(
                ':number_of_days' => $number_of_days
            ));
        }
    
        return $resultset;
    }
    
    public static function getEntriesResultSet($daily_ranking_id) {
        $resultset = static::getEntriesBaseResultset();
        
        $resultset->addFilterCriteria("dr.daily_ranking_id = :daily_ranking_id", array(
            ':daily_ranking_id' => $daily_ranking_id
        ));
    
        return $resultset;
    }
    
    public static function getRankingsResultset($number_of_days = NULL) {            
        $resultset = static::getLatestEntriesResultset();
        
        //Set default sort criteria
        $resultset->setSortCriteria('dre.rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getLatestEntries() {
        $resultset = static::getRankingsResultset();
        
        $resultset->addSortCriteria('dr.daily_ranking_day_type_id', 'ASC');
        $resultset->addSortCriteria('dre.rank', 'ASC');
        
        return $resultset;
    }
}