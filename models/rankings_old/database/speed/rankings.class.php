<?php
namespace Modules\Necrolab\Models\Rankings\Database\Speed;

use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Rankings\Database\Rankings as BaseRankings;

class Rankings
extends BaseRankings {
    protected static function getBaseRankingsResultset() {
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
                pre.coda_speed_rank,
                pre.all_speed_rank,
                pre.story_speed_rank,
                pre.speed_rank_points_total,
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
            WHERE 1 = 1
                {{AND_CRITERIA}}
                AND pre.speed_rank IS NOT NULL
        ");
        
        $resultset->setSortCriteria('pre.speed_rank', 'ASC'); 
        
        return $resultset;
    }
    
    protected static function getRankingsResultset($power_ranking_id) {
        $resultset = static::getBaseRankingsResultset();
        
        $resultset->addFilterCriteria('pr.power_ranking_id = :power_ranking_id', array(
            ':power_ranking_id' => $power_ranking_id
        ));
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processCategoryResultset('speed', __NAMESPACE__ . '\RecordModels\SpeedEntry', $result_data);
        });
        
        return $resultset;
    }
    
    protected static function getLatestRankingsResultset() {
        $resultset = static::getBaseRankingsResultset();
        
        $resultset->addFilterCriteria('pr.latest = 1');
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processCategoryResultset('speed', __NAMESPACE__ . '\RecordModels\SpeedEntry',, $result_data);
        });
        
        return $resultset;
    }
}