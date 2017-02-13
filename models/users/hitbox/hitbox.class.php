<?php
namespace Modules\Necrolab\Models\Users\Hitbox;

use \Modules\Necrolab\Models\Necrolab;

class Hitbox
extends Necrolab {
    public static function getProfileUrl($hitbox_username) {
        return "http://hitbox.tv/{$hitbox_username}";        
    }
}
