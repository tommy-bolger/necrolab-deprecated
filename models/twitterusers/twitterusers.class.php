<?php
namespace Modules\Necrolab\Models\TwitterUsers;

use \Modules\Necrolab\Models\Necrolab;

class TwitterUsers
extends Necrolab {
    public static function getProfileUrl($twitter_username) {
        return "https://twitter.com/{$twitter_username}";        
    }
}
