<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \Exception;
use \Modules\Necrolab\Models\Leaderboards\ReplayVersions as BaseReplayVersions;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\ReplayVersion;

class ReplayVersions
extends BaseReplayVersions {    
    public static function loadAll() {
        if(empty(static::$replay_versions)) {            
            static::$replay_versions = db()->getMappedColumn("
                SELECT 
                    name,
                    steam_replay_version_id
                FROM steam_replay_versions
            ");
            
            if(!empty(static::$replay_versions)) {
                static::$replay_versions_by_id = array_flip(static::$replay_versions);
            }
        }
    }
    
    public static function save(ReplayVersion $replay_version) {
        $replay_version_id = static::get($replay_version->name);
        
        if(empty($replay_version_id)) {        
            $replay_version_id = db()->insert('steam_replay_versions', $replay_version->toArray(), 'replay_version_insert');
            
            static::$replay_versions[$replay_version->name] = $replay_version_id;
        }
    
        return $replay_version_id;
    }
    
    public static function updateBatch($replay_version_id, ReplayVersion $replay_version) {         
        db()->update('steam_replay_versions', $replay_version->toArray(), array(
            'replay_version_id' => $replay_version_id
        ), '', 'replay_version_update');
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE steam_replay_versions;");
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'srv.name',
                'alias' => 'replay_version'
            )
        ));
    }
}