<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class ReplayVersions
extends Necrolab {
    protected static $replay_versions = array();
    
    protected static $replay_versions_by_id = array();
    
    public static function loadAll() {}
    
    public static function get($replay_version) {
        static::loadAll();
        
        $replay_version_id = NULL;
        
        if(isset(static::$replay_versions[$replay_version])) {
            $replay_version_id = static::$replay_versions[$replay_version];
        }
        
        return $replay_version_id;
    }
    
    public static function getById($steam_replay_version_id) {
        static::loadAll();
        
        $replay_version = NULL;
        
        if(isset(static::$replay_versions_by_id[$steam_replay_version_id])) {
            $replay_version = static::$replay_versions_by_id[$steam_replay_version_id];
        }
        
        return $replay_version;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return $data_row['replay_version'];
    }
}