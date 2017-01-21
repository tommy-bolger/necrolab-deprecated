<?php
namespace Modules\Necrolab\Models\Rankings\Database\Deathless;

use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Rankings\Database\Rankings as BaseRankings;

class Database
extends BaseRankings {
    protected static function getBaseRankingsResultset() {
        $resultset = new SQL('deathless_score_rankings');
    
        $resultset->setBaseQuery("
            SELECT
                pre.deathless_score_rank,
                su.personaname,
                pre.cadence_deathless_score_rank,
                pre.bard_deathless_score_rank,
                pre.monk_deathless_score_rank,
                pre.aria_deathless_score_rank,
                pre.bolt_deathless_score_rank,
                pre.dove_deathless_score_rank,
                pre.eli_deathless_score_rank,
                pre.melody_deathless_score_rank,
                pre.dorian_deathless_score_rank,
                pre.coda_deathless_score_rank,                    
                pre.deathless_score_rank_points_total,
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
                AND pre.deathless_score_rank IS NOT NULL
        ");
        
        $resultset->setSortCriteria('pre.deathless_score_rank', 'ASC'); 
        
        return $resultset;
    }
    
    protected static function getRankingsResultset($power_ranking_id) {
        $resultset = static::getBaseRankingsResultset();
        
        $resultset->addFilterCriteria('pr.power_ranking_id = :power_ranking_id', array(
            ':power_ranking_id' => $power_ranking_id
        ));
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processCategoryResultset('deathless_score', __NAMESPACE__ . '\RecordModels\DeathlessEntry', $result_data);
        });
        
        return $resultset;
    }
    
    protected static function getLatestRankingsResultset() {
        $resultset = static::getBaseRankingsResultset();
        
        $resultset->addFilterCriteria('pr.latest = 1');
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processCategoryResultset('deathless_score', __NAMESPACE__ . '\RecordModels\DeathlessEntry', $result_data);
        });
        
        return $resultset;
    }
}