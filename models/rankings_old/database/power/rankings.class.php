<?php
namespace Modules\Necrolab\Models\Rankings\Database\Power;

use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Rankings\Database\Rankings as BaseRankings;

class Rankings
extends BaseRankings {
    protected static function getBaseRankingsResultset() {
        $resultset = new SQL('deathless_score_rankings');
    
        $resultset->setBaseQuery("
            SELECT
                pre.*,
                su.steamid,
                su.personaname,
                su.steamid,
                su.twitch_username,
                su.nico_nico_url,
                su.hitbox_username,
                su.twitter_username,
                su.website
            FROM power_rankings pr
            JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
            JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
            {{AND_CRITERIA}}
        ");
        
        $resultset->setSortCriteria('pre.rank', 'ASC'); 
        
        return $resultset;
    }
    
    protected static function getRankingsResultset($power_ranking_id) {
        $resultset = static::getBaseRankingsResultset();
        
        $resultset->addFilterCriteria('pr.power_ranking_id = :power_ranking_id', array(
            ':power_ranking_id' => $power_ranking_id
        ));
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processResultset('power', $result_data);
        });
        
        return $resultset;
    }
    
    protected static function getLatestRankingsResultset($post_process = true) {
        $resultset = static::getBaseRankingsResultset();
        
        $resultset->addFilterCriteria('pr.latest = 1');
        
        if(!empty($post_process)) {
            $resultset->addProcessorFunction(function($result_data) {
                return static::processResultset(__NAMESPACE__ . '\RecordModels\PowerRankingEntry', $result_data);
            });
        }
        
        return $resultset;
    }
}