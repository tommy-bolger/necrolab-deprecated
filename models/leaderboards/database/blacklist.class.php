<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Leaderboards\Blacklist as BaseBlacklist;

class Blacklist
extends BaseBlacklist {    
    public static function load() {
        if(empty(static::$blacklist)) {            
            static::$blacklist = db()->getMappedColumn("
                SELECT 
                    l.lbid,
                    l.leaderboard_id
                FROM leaderboards_blacklist lb
                JOIN leaderboards l ON lb.leaderboard_id = l.leaderboard_id
            ");
        }
    }
    
    public static function getAll() {
        return db()->getColumn("
            SELECT
                l.lbid
            FROM leaderboards_blacklist lb
            JOIN leaderboards l ON l.leaderboard_id = lb.leaderboard_id
        ");
    }
}