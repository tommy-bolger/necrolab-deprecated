<?php
namespace Modules\Necrolab\Models\HitboxUsers;

use \Modules\Necrolab\Models\Necrolab;

class HitboxUsers
extends Necrolab {
    public static function getProfileUrl($hitbox_username) {
        return "http://hitbox.tv/{$hitbox_username}";        
    }
}
