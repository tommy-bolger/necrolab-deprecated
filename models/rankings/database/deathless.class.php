<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;

class Deathless
extends Rankings {
    protected static function getEntriesResultset(DateTime $date) {    
        $resultset = new SQL('deathless_rankings');
    
        $resultset->setBaseQuery("
            SELECT
                pre.deathless_rank,
                pre.deathless_rank_points_total AS total_points,
                pre.cadence_deathless_rank,
                pre.bard_deathless_rank,
                pre.monk_deathless_rank,
                pre.aria_deathless_rank,
                pre.bolt_deathless_rank,
                pre.dove_deathless_rank,
                pre.eli_deathless_rank,
                pre.melody_deathless_rank,
                pre.dorian_deathless_rank,
                pre.coda_deathless_rank,
                pre.cadence_deathless_rank_points,
                pre.bard_deathless_rank_points,
                pre.monk_deathless_rank_points,
                pre.aria_deathless_rank_points,
                pre.bolt_deathless_rank_points,
                pre.dove_deathless_rank_points,
                pre.eli_deathless_rank_points,
                pre.melody_deathless_rank_points,
                pre.dorian_deathless_rank_points,
                pre.coda_deathless_rank_points,
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
                pre.deathless_rank AS rank,
                pre.deathless_rank_points_total AS total_points,
                pre.cadence_deathless_rank AS cadence_rank,
                pre.bard_deathless_rank AS bard_rank,
                pre.monk_deathless_rank AS monk_rank,
                pre.aria_deathless_rank AS aria_rank,
                pre.bolt_deathless_rank AS bolt_rank,
                pre.dove_deathless_rank AS dove_rank,
                pre.eli_deathless_rank AS eli_rank,
                pre.melody_deathless_rank AS melody_rank,
                pre.dorian_deathless_rank AS dorian_rank,
                pre.coda_deathless_rank AS coda_rank,
                pre.cadence_deathless_rank_points AS cadence_rank_points,
                pre.bard_deathless_rank_points AS bard_rank_points,
                pre.monk_deathless_rank_points AS monk_rank_points,
                pre.aria_deathless_rank_points AS aria_rank_points,
                pre.bolt_deathless_rank_points AS bolt_rank_points,
                pre.dove_deathless_rank_points AS dove_rank_points,
                pre.eli_deathless_rank_points AS eli_rank_points,
                pre.melody_deathless_rank_points AS melody_rank_points,
                pre.dorian_deathless_rank_points AS dorian_rank_points,
                pre.coda_deathless_rank_points AS coda_rank_points,
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
        
        $resultset->setSortCriteria('pre.deathless_rank', 'ASC'); 
        
        $resultset->setRowsPerPage(100);
        
        return $resultset;
    }
}