<?php
namespace Modules\Necrolab\Models;

use \Framework\Core\Framework;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;

class SpeedRankings {
    public static function getLatestRankings($page_number = 1, $rows_per_page = 100) {
        $resultset = NULL;

        if(!Framework::getInstance()->enable_cache) {
            $resultset = new SQL('speed_rankings');
        
            $resultset->setBaseQuery("
                SELECT
                    pre.speed_rank,
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
                    pre.all_speed_rank,
                    pre.story_speed_rank,
                    pre.speed_rank_points_total,
                    pre.steam_user_id,
                    pre.power_ranking_entry_id,
                    su.twitch_username,
                    su.twitter_username,
                    su.website
                FROM power_rankings pr
                JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
                JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
                WHERE pr.latest = 1   
                    AND pre.speed_rank IS NOT NULL
                {{WHERE_CRITERIA}}
            ");
            
            //Set default sort criteria
            $resultset->setSortCriteria('pre.speed_rank', 'ASC');  
        }
        else {
            $resultset = new Redis('latest_speed_rankings');       
        }  
        
        $resultset->enableTotalRecordCount();
        
        //Set default rows per page
        $resultset->setRowsPerPage($rows_per_page);    
        
        //Set the default page number
        $resultset->setPageNumber($page_number);  
        
        return $resultset;
    }
}