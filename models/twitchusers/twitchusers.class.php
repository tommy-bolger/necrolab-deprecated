<?php
namespace Modules\Necrolab\Models\TwitchUsers;

use \Modules\Necrolab\Models\Necrolab;

class TwitchUsers
extends Necrolab {
    public static function getProfileUrl($twitch_username) {
        return "https://www.twitch.tv/{$twitch_username}";        
    }
}
