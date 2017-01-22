<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;

class Power
extends Rankings {
    public static function getEntriesResultset(DateTime $date) {    
        $resultset = new SQL('power_rankings');
    
        $resultset->setBaseQuery("
            SELECT
                pre.*,
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
            {{AND_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('pr.date = ?', array(
            $date->format('Y-m-d')
        ));
        
        $resultset->setSortCriteria('pre.rank', 'ASC'); 
        
        return $resultset;
    }
}