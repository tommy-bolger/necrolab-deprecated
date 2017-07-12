<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Releases;
use \Modules\Necrolab\Models\Dailies\Rankings\Rankings as BaseRankings;

class Rankings
extends BaseRankings {
    protected static function load($release_id, $mode_id, $daily_ranking_day_type_id, DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        
        if(empty(static::$rankings[$release_id][$mode_id][$date_formatted][$daily_ranking_day_type_id])) {
            $ranking = db()->getRow("
                SELECT *
                FROM daily_rankings
                WHERE date = :date
                    AND release_id = :release_id
                    AND mode_id = :mode_id
                    AND daily_ranking_day_type_id = :daily_ranking_day_type_id
            ", array(
                ':date' => $date_formatted,
                ':release_id' => $release_id,
                ':mode_id' => $mode_id,
                ':daily_ranking_day_type_id' => $daily_ranking_day_type_id
            ));
            
            if(!empty($ranking)) {
                static::$rankings[$release_id][$mode_id][$date_formatted][$daily_ranking_day_type_id] = $ranking;
            }
        }
    }

    public static function save($release_id, $mode_id, $daily_ranking_day_type_id, DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        $daily_ranking = static::get($release_id, $mode_id, $daily_ranking_day_type_id, $date);
        
        $current_time = date('Y-m-d H:i:s');
        
        $daily_ranking_id = NULL;
    
        if(empty($daily_ranking)) {
            $record = array(
                'daily_ranking_day_type_id' => $daily_ranking_day_type_id,
                'release_id' => $release_id,
                'mode_id' => $mode_id,
                'created' => $current_time,
                'date' => $date_formatted
            );
        
            $daily_ranking_id = db()->insert('daily_rankings', $record);
            
            $record['daily_ranking_id'] = $daily_ranking_id;
            
            static::$rankings[$release_id][$mode_id][$date_formatted][$daily_ranking_day_type_id] = $record;
        }
        else {      
            $daily_ranking_id = $daily_ranking['daily_ranking_id'];
        
            db()->update('daily_rankings', array(
                'updated' => $current_time
            ), array(
                'daily_ranking_id' => $daily_ranking_id
            ));
            
            static::$rankings[$release_id][$mode_id][$date_formatted][$daily_ranking_day_type_id]['updated'] = $current_time;
        }
        
        return $daily_ranking_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE daily_rankings;");
    }
    
    public static function getAllBaseResultset($release_id, $mode_id, $daily_ranking_day_type_id) {    
        $resultset = new SQL('daily_rankings');
    
        $resultset->setBaseQuery("
            SELECT dr.*
            FROM daily_rankings dr
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('dr.release_id = :release_id', array(
            ':release_id' => $release_id
        ));
        
        $resultset->addFilterCriteria('dr.mode_id = :mode_id', array(
            ':mode_id' => $mode_id
        ));
        
        $resultset->addFilterCriteria('dr.daily_ranking_day_type_id = :daily_ranking_day_type_id', array(
            ':daily_ranking_day_type_id' => $daily_ranking_day_type_id
        ));
        
        $resultset->setSortCriteria('date', 'DESC'); 
        
        return $resultset;
    }
    
    public static function getDatesResultset($release_id, $mode_id, $daily_ranking_day_type_id) {
        $resultset = static::getAllBaseResultset($release_id, $mode_id, $daily_ranking_day_type_id);
        
        $resultset->setSelectField('pr.date', 'date');
        
        return $resultset;
    }
    
    public static function getSteamUserBaseResultset($steamid, $release_id, $mode_id, $daily_ranking_day_type_id) {    
        $resultset = new SQL('steam_user_daily_ranking_entries');
    
        $resultset->setBaseQuery("
            SELECT dr.date
            FROM daily_rankings dr
            JOIN {{PARTITION_TABLE}} dre ON dre.daily_ranking_id = dr.daily_ranking_id
            JOIN steam_users su ON su.steam_user_id = dre.steam_user_id
            {{WHERE_CRITERIA}}
        ");
        
        $release = Releases::getById($release_id);
        
        $parition_table_names = static::getPartitionTableNames('daily_ranking_entries', new DateTime($release['start_date']), new DateTime($release['end_date']));
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->addFilterCriteria('dr.release_id = ?', array(
            $release_id
        ));
        
        $resultset->addFilterCriteria('dr.mode_id = ?', array(
            $mode_id
        ));
        
        $resultset->addFilterCriteria('dr.daily_ranking_day_type_id = ?', array(
            $daily_ranking_day_type_id
        ));
        
        $resultset->setSortCriteria('date', 'DESC');
        
        return $resultset;
    }
    
    public static function getSteamUserDatesResultset($steamid, $release_id, $mode_id, $daily_ranking_day_type_id) {
        $resultset = static::getSteamUserBaseResultset($steamid, $release_id, $mode_id, $daily_ranking_day_type_id);
        
        $resultset->setSelectField('pr.date', 'date');
        
        return $resultset;
    }
}