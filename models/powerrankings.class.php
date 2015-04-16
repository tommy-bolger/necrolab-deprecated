<?php
namespace Modules\Necrolab\Models;

use \Framework\Core\Framework;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;

class PowerRankings {
    public static function getLatestRankings($page_number = 1, $rows_per_page = 100) {
        $resultset = NULL;

        if(!Framework::getInstance()->enable_cache) {
            $resultset = new SQL('power_rankings');
        
            $resultset->setBaseQuery("
                SELECT
                    pre.rank,
                    su.personaname,
                    pre.cadence_speed_rank,
                    pre.bard_speed_rank,
                    pre.monk_speed_rank,
                    pre.aria_speed_rank,
                    pre.bolt_speed_rank,
                    pre.dove_speed_rank,
                    pre.eli_speed_rank,
                    pre.melody_speed_rank,
                    pre.dorian_speed_rank,
                    pre.cadence_score_rank,
                    pre.bard_score_rank,
                    pre.monk_score_rank,
                    pre.aria_score_rank,
                    pre.bolt_score_rank,
                    pre.dove_score_rank,
                    pre.eli_score_rank,
                    pre.melody_score_rank,
                    pre.dorian_score_rank,
                    pre.speed_total,
                    pre.score_total,
                    pre.base,
                    pre.weighted,
                    pre.top_10_bonus
                FROM power_rankings pr
                JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
                JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
                WHERE pr.latest = 1
                {{WHERE_CRITERIA}}
            ");
            
            //Set default sort criteria
            $resultset->setSortCriteria('pre.rank', 'ASC');  
        }
        else {
            $resultset = new Redis('latest_power_rankings');       
        }  
        
        $resultset->enableTotalRecordCount();
        
        //Set default rows per page
        $resultset->setRowsPerPage($rows_per_page);    
        
        //Set the default page number
        $resultset->setPageNumber($page_number);  
        
        return $resultset;
    }
}