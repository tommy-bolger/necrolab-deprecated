<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Framework\Modules\Module;
use \Modules\Necrolab\Models\Leaderboards\Snapshots as BaseSnapshots;
use \Modules\Necrolab\Models\Releases\Database\Leaderboards;
use \Modules\Necrolab\Models\Releases\Database\Releases;
use \Modules\Necrolab\Models\leaderboards\Database\RecordModels\Leaderboard as DatabaseLeaderboard;

class Snapshots
extends BaseSnapshots {
    protected static function loadDate(DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
    
        if(empty(static::$snapshots[$date_formatted])) {
            static::$snapshots[$date_formatted] = array();
            
            $snapshots = db()->getGroupedRows("
                SELECT 
                    l.lbid, 
                    ls.*
                FROM leaderboard_snapshots ls
                JOIN leaderboards l ON l.leaderboard_id = ls.leaderboard_id
                WHERE date = :date
            ", array(
                ':date' => $date_formatted
            ));
            
            if(!empty($snapshots)) {
                static::$snapshots[$date_formatted] = $snapshots;
            }
        }
    }

    public static function save(DatabaseLeaderboard $leaderboard_record, DateTime $date) {
        $lbid = $leaderboard_record->lbid;
        $date_formatted = $date->format('Y-m-d');
        $leaderboard_snapshot = static::get($lbid, $date);
        
        $current_time = date('Y-m-d H:i:s');
        
        $leaderboard_snapshot_id = NULL;
    
        if(empty($leaderboard_snapshot)) {
            $record = array(
                'leaderboard_id' => $leaderboard_record->leaderboard_id,
                'created' => $current_time,
                'date' => $date_formatted
            );
        
            $leaderboard_snapshot_id = db()->insert('leaderboard_snapshots', $record, 'leaderboard_snapshot_insert');
            
            $record['leaderboard_snapshot_id'] = $leaderboard_snapshot_id;
            
            static::$snapshots[$date_formatted][$lbid] = $record;
        }
        else {      
            $leaderboard_snapshot_id = $leaderboard_snapshot['leaderboard_snapshot_id'];
        
            db()->update('leaderboard_snapshots', array(
                'updated' => $current_time
            ), array(
                'leaderboard_snapshot_id' => $leaderboard_snapshot_id
            ), array(), 'leaderboard_snapshot_update');
            
            static::$snapshots[$date_formatted][$lbid]['updated'] = $current_time;
        }
        
        return $leaderboard_snapshot_id;
    }
    
    public static function getAllBaseResultset($lbid) {
        $resultset = new SQL('leaderboard_snapshots');
        
        $resultset->setBaseQuery("
            SELECT ls.*
            FROM leaderboards l
            JOIN leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('l.lbid = :lbid', array(
            ':lbid' => $lbid
        ));
        
        $resultset->addSortCriteria('ls.date', 'ASC');
        
        return $resultset;
    }
    
    public static function getSteamUserBaseResultset($steamid, $lbid) {
        $resultset = new SQL('leaderboard_snapshots');
        
        $resultset->setBaseQuery("
            SELECT ls.*
            FROM leaderboards l
            JOIN leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            JOIN {{PARTITION_TABLE}} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            JOIN steam_users su ON su.steam_user_id = le.steam_user_id
            {{WHERE_CRITERIA}}
            GROUP BY ls.leaderboard_snapshot_id
        ");
        
        $parition_table_names = static::getPartitionTableNames('leaderboard_entries', new DateTime('2015-04-01'), new DateTime());
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->addFilterCriteria('l.lbid = ?', array(
            $lbid
        ));
        
        $resultset->addSortCriteria('date', 'ASC');
        
        return $resultset;
    }
}