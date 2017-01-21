<?php
namespace Modules\Necrolab\Models\Rankings\Cache;

use \Modules\Necrolab\Models\Rankings\Rankings as BaseRankings;
use \Modules\Necrolab\Models\SteamUsers\Cache\SteamUsers;

class Rankings 
extends BaseRankings {
    public static function getSteamUsersFromResultData(array $result_data) {
        $steamids = array();
        
        if(!empty($result_data)) {
            foreach($result_data as $result_row) {
                $steamids[] = $result_row['steamid'];
            }
        }
        
        return SteamUsers::getSocialMediaData($steamids);
    }
}