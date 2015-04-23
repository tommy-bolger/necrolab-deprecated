<?php
namespace Modules\Necrolab\Models;

use \Framework\Core\Framework;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;

class ScoreRankings {
    public static function getLatestRankings($page_number = 1, $rows_per_page = 100) {
        $resultset = NULL;

        if(!Framework::getInstance()->enable_cache) {
            $resultset = new SQL('score_rankings');
        
            $resultset->setBaseQuery("
                SELECT
                    pre.score_rank,
                    su.personaname,
                    pre.cadence_score_rank,
                    pre.bard_score_rank,
                    pre.monk_score_rank,
                    pre.aria_score_rank,
                    pre.bolt_score_rank,
                    pre.dove_score_rank,
                    pre.eli_score_rank,
                    pre.melody_score_rank,
                    pre.dorian_score_rank,
                    pre.coda_score_rank,
                    pre.all_score_rank,
                    pre.story_score_rank,
                    pre.score_rank_points_total,
                    pre.steam_user_id,
                    pre.power_ranking_entry_id,
                    su.twitch_username,
                    su.nico_nico_url,
                    su.hitbox_username,
                    su.twitter_username,
                    su.website
                FROM power_rankings pr
                JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
                JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
                WHERE pr.latest = 1   
                    AND pre.score_rank IS NOT NULL
                {{WHERE_CRITERIA}}
            ");
            
            //Set default sort criteria
            $resultset->setSortCriteria('pre.score_rank', 'ASC');  
        }
        else {
            $resultset = new Redis('latest_score_rankings');       
        }  
        
        $resultset->enableTotalRecordCount();
        
        //Set default rows per page
        $resultset->setRowsPerPage($rows_per_page);    
        
        //Set the default page number
        $resultset->setPageNumber($page_number);  
        
        return $resultset;
    }
}