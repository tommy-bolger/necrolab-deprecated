<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class ReplayVersions
extends Necrolab {
    protected static $replay_versions = array();
    
    public static function loadAll() {}
    
    public static function get($replay_version) {
        static::loadAll();
        
        $replay_version_id = NULL;
        
        if(isset(static::$replay_versions[$replay_version])) {
            $replay_version_id = static::$replay_versions[$replay_version];
        }
        
        return $replay_version_id;
    }
}