<?php
namespace Modules\Necrolab\Models;

use \Framework\Core\Framework;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;

class DailyRankings {
    public static function getLatestRankings($page_number = 1, $rows_per_page = 100) {
        $resultset = NULL;
    
        if(!Framework::getInstance()->enable_cache) {
            $resultset = new SQL('daily_rankings');
            
            $resultset->setBaseQuery("
                SELECT
                    dre.rank,
                    su.personaname,
                    dre.first_place_ranks,
                    dre.top_5_ranks,
                    dre.top_10_ranks,
                    dre.top_20_ranks,
                    dre.top_50_ranks,
                    dre.top_100_ranks,
                    dre.total_points,
                    dre.points_per_day,
                    dre.total_dailies,
                    dre.total_wins,
                    dre.average_place                
                FROM daily_rankings dr
                JOIN daily_ranking_entries dre ON dre.daily_ranking_id = dr.daily_ranking_id
                JOIN steam_users su ON su.steam_user_id = dre.steam_user_id
                WHERE dr.latest = 1
                {{WHERE_CRITERIA}}
            ");
            
            //Set default sort criteria
            $resultset->setSortCriteria('dre.rank', 'ASC');
        }
        else {
            $resultset = new Redis('latest_daily_rankings');
        }
        
        $resultset->enableTotalRecordCount();
        
        //Set default rows per page
        $resultset->setRowsPerPage($rows_per_page);    
        
        //Set the default page number
        $resultset->setPageNumber($page_number);  
        
        return $resultset;
    }
}