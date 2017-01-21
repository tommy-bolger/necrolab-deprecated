<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;

class Score
extends Rankings {    
    protected static function getEntriesResultset(DateTime $date) {    
        $resultset = new SQL('score_rankings');
    
        $resultset->setBaseQuery("
            SELECT
                pre.score_rank,
                pre.score_rank_points_total AS total_points,
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
                pre.cadence_score_rank_points,
                pre.bard_score_rank_points,
                pre.monk_score_rank_points,
                pre.aria_score_rank_points,
                pre.bolt_score_rank_points,
                pre.dove_score_rank_points,
                pre.eli_score_rank_points,
                pre.melody_score_rank_points,
                pre.dorian_score_rank_points,
                pre.coda_score_rank_points,
                pre.all_score_rank_points,
                pre.story_score_rank_points,
                su.steamid,
                su.personaname,
                su.steamid,
                su.twitch_username,
                su.nico_nico_url,
                su.hitbox_username,
                su.twitter_username,
                su.website
            FROM power_rankings pr
            JOIN power_ranking_entries_{$date->format('Y_m')} pre ON pre.power_ranking_id = pr.power_ranking_id
            JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('pr.date = ?', array(
            $date->format('Y-m-d')
        ));
        
        return $resultset;
    }
    
    public static function getEntriesDisplayResultset(DateTime $date) {    
        $resultset = new SQL('power_rankings');
    
        $resultset->setBaseQuery("
            SELECT
                pre.score_rank AS rank,
                pre.score_rank_points_total AS total_points,
                pre.cadence_score_rank AS cadence_rank,
                pre.bard_score_rank AS bard_rank,
                pre.monk_score_rank AS monk_rank,
                pre.aria_score_rank AS aria_rank,
                pre.bolt_score_rank AS bolt_rank,
                pre.dove_score_rank AS dove_rank,
                pre.eli_score_rank AS eli_rank,
                pre.melody_score_rank AS melody_rank,
                pre.dorian_score_rank AS dorian_rank,
                pre.coda_score_rank AS coda_rank,
                pre.all_score_rank AS all_rank,
                pre.story_score_rank AS story_rank,
                pre.cadence_score_rank_points AS cadence_rank_points,
                pre.bard_score_rank_points AS bard_rank_points,
                pre.monk_score_rank_points AS monk_rank_points,
                pre.aria_score_rank_points AS aria_rank_points,
                pre.bolt_score_rank_points AS bolt_rank_points,
                pre.dove_score_rank_points AS dove_rank_points,
                pre.eli_score_rank_points AS eli_rank_points,
                pre.melody_score_rank_points AS melody_rank_points,
                pre.dorian_score_rank_points AS dorian_rank_points,
                pre.coda_score_rank_points AS coda_rank_points,
                pre.all_score_rank_points AS all_rank_points,
                pre.story_score_rank_points AS story_rank_points,
                su.steamid,
                su.personaname,
                su.steamid,
                su.twitch_username,
                su.nico_nico_url,
                su.hitbox_username,
                su.twitter_username,
                su.website
            FROM power_rankings pr
            JOIN power_ranking_entries_{$date->format('Y_m')} pre ON pre.power_ranking_id = pr.power_ranking_id
            JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('pr.date = ?', array(
            $date->format('Y-m-d')
        ));
        
        $resultset->setSortCriteria('pre.score_rank', 'ASC'); 
        
        $resultset->setRowsPerPage(100);
        
        return $resultset;
    }
}
