<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Rankings\Rankings as BaseRankings;
use \Modules\Necrolab\Models\Releases\Database\Releases;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers;

class Rankings
extends BaseRankings {
    protected static function load($release_id, DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        
        if(empty(static::$rankings[$release_id][$date_formatted])) {
            $ranking = db()->getRow("
                SELECT *
                FROM power_rankings
                WHERE release_id = :release_id
                    AND date = :date
            ", array(
                ':release_id' => $release_id,
                ':date' => $date_formatted
            ));
            
            if(!empty($ranking)) {
                static::$rankings[$release_id][$date_formatted] = $ranking;
            }
        }
    }

    public static function save($release_id, DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        $power_ranking = static::get($release_id, $date);
        
        $current_time = date('Y-m-d H:i:s');
        
        $power_ranking_id = NULL;
    
        if(empty($power_ranking)) {
            $record = array(
                'release_id' => $release_id,
                'created' => $current_time,
                'date' => $date_formatted
            );
        
            $power_ranking_id = db()->insert('power_rankings', $record);
            
            $record['power_ranking_id'] = $power_ranking_id;
            
            static::$rankings[$release_id][$date_formatted] = $record;
        }
        else {      
            $power_ranking_id = $power_ranking['power_ranking_id'];
        
            db()->update('power_rankings', array(
                'updated' => $current_time
            ), array(
                'power_ranking_id' => $power_ranking_id
            ));
            
            static::$rankings[$release_id][$date_formatted]['updated'] = $current_time;
        }
        
        return $power_ranking_id;
    }
    
    public static function getLastRefreshed() {
        return NULL;
    }
    
    public static function getAllBaseResultset($release_name) {
        $resultset = new SQL('power_rankings');
    
        $resultset->setBaseQuery("
            SELECT pr.*
            FROM power_rankings pr
            JOIN releases r ON r.release_id = pr.release_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
        ));
        
        $resultset->setSortCriteria('pr.date', 'ASC'); 
        
        return $resultset;
    }
    
    public static function getSteamUserBaseResultset($release_name, $steamid) {
        $resultset = new SQL('steam_user_power_ranking_entries');
    
        $resultset->setBaseQuery("
            SELECT 
                pr.*
            FROM power_rankings pr
            JOIN releases r ON r.release_id = pr.release_id
            JOIN {{PARTITION_TABLE}} pre ON pre.power_ranking_id = pr.power_ranking_id
            JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
            {{WHERE_CRITERIA}}
        ");
        
        $release = Releases::getByName($release_name);
        
        $parition_table_names = static::getPartitionTableNames('power_ranking_entries', new DateTime($release['start_date']), new DateTime($release['end_date']));
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->addFilterCriteria('r.name = ?', array(
            $release_name
        ));
        
        $resultset->setSortCriteria('date', 'ASC');
        
        return $resultset;
    }
}