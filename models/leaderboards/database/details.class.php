<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Leaderboards\Details as BaseDetails;

class Details
extends BaseDetails {    
    public static function loadAll() {
        if(empty(static::$details_records)) {            
            static::$details_records = db()->getMappedColumn("
                SELECT 
                    details,
                    leaderboard_entry_details_id
                FROM leaderboard_entry_details
            ");
            
            if(!empty(static::$details_records)) {
                static::$details_records_by_id = array_flip(static::$details_records);
            }
        }
    }
    
    public static function getEntriesResultset() {    
        $resultset = new SQL("details");
        
        $resultset->setBaseQuery("
            SELECT *
            FROM leaderboard_entry_details
            {{WHERE_CRITERIA}}
        ");
        
        return $resultset;
    }
    
    public static function save($details) {
        $leaderboard_entry_details_id = static::get($details);
        
        if(empty($leaderboard_entry_details_id)) {
            $leaderboard_entry_details_id = db()->insert('leaderboard_entry_details', array(
                'details' => $details
            ), 'details_insert');
            
            static::$details_records[$details] = $leaderboard_entry_details_id;
        }
    
        return $leaderboard_entry_details_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE leaderboard_entry_details;");
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'led.details',
                'alias' => 'details'
            )
        ));
    }
}