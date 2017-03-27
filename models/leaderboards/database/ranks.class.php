<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Leaderboards\Ranks as BaseRanks;

class Ranks
extends BaseRanks {    
    public static function loadAll() {
        if(empty(static::$ranks_records)) {            
            static::$ranks_records = db()->getMappedColumn("
                SELECT 
                    rank,
                    points
                FROM leaderboard_ranks
            ");
        }
    }
    
    public static function getEntriesResultset() {    
        $resultset = new SQL("ranks");
        
        $resultset->setBaseQuery("
            SELECT *
            FROM leaderboard_ranks
            {{WHERE_CRITERIA}}
        ");
        
        return $resultset;
    }
    
    public static function save($rank) {
        $points = static::getPoints($rank);
        
        if(empty($points)) {
            db()->insert('leaderboard_ranks', array(
                'rank' => $rank,
                'points' => static::generateRankPoints($rank)
            ), 'ranks_insert', false);
            
            static::$ranks_records[$rank] = $points;
        }
    
        return $points;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE leaderboard_ranks;");
    }
    
    public static function populateTable($max_rank) {
        db()->beginTransaction();
    
        for($rank = 1; $rank <= $max_rank; $rank++) {
            static::save($rank);
        }
        
        db()->commit();
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'lr.rank',
                'alias' => 'rank'
            ),
            array(
                'field' => 'lr.points',
                'alias' => 'rank_points'
            )
        ));
    }
}