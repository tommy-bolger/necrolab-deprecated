<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \Modules\Necrolab\Models\Rankings\Rankings as BaseRankings;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers;

class Rankings
extends BaseRankings {
    public static function getSteamUsersFromResultData(array $result_data) {
        $steam_user_ids = array();
        
        if(!empty($result_data)) {
            foreach($result_data as $result_row) {
                $steam_user_ids[] = $result_row['steam_user_id'];
            }
        }
        
        return SteamUsers::getSocialMediaData($steam_user_ids);
    }
}