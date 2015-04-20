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
                    pre.score_rank,
                    pre.score_rank_points_total,
                    pre.deathless_score_rank,
                    pre.deathless_score_rank_points_total,
                    pre.speed_rank,
                    pre.speed_rank_points_total,
                    pre.total_points,
                    pre.steam_user_id,
                    pre.power_ranking_entry_id,
                    su.twitch_username,
                    su.twitter_username,
                    su.website
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