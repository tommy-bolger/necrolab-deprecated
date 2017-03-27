<?php
namespace Modules\Necrolab\Models\Users\Beampro\Database;

use \DateTime;
use \Modules\Necrolab\Models\Users\Beampro\Beampro as BaseBeampro;
use \Modules\Necrolab\Models\Users\Beampro\Database\RecordModels\BeamproUser as DatabaseBeamproUser;

class Beampro
extends BaseBeampro {
    public static function load($beampro_id) {
        if(empty(static::$users[$beampro_id])) {
            static::$users[$beampro_id] = db()->getRow("
                SELECT *
                FROM beampro_users
                WHERE beampro_id = :beampro_id
            ", array(
                ':beampro_id' => $beampro_id
            ));
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            $user_ids = db()->getMappedColumn("
                SELECT
                    beampro_id,
                    beampro_user_id
                FROM beampro_users
            ");
            
            if(!empty($user_ids)) {
                static::$user_ids = $user_ids;
            }
        }
    }
    
    public static function save(DatabasebeamproUser $beampro_user, $cache_query_name = NULL) {
        $beampro_user_id = static::getId($beampro_user->beampro_id);
        
        if(empty($beampro_user_id)) {       
            $beampro_user->updated = date('Y-m-d H:i:s');
            
            $user_record = array();
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
                
                $user_record = $beampro_user->toArray();
            }
            else {
                $user_record = $beampro_user->toArray(false);
            }
        
            $beampro_user_id = db()->insert('beampro_users', $user_record, $cache_query_name);
            
            static::addId($beampro_user->beampro_id, $beampro_user_id);
        }
        else {
            $beampro_user->updated = date('Y-m-d H:i:s');       
            $user_record = $beampro_user->toArray();
            
            if(isset($user_record['beampro_id'])) {
                unset($user_record['beampro_id']);
            }
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
                
                $user_record = $beampro_user->toArray();
            }
            else {
                $user_record = $beampro_user->toArray(false);
            }
        
            db()->update('beampro_users', $user_record, array(
                'beampro_user_id' => $beampro_user_id
            ), array(), $cache_query_name);
        }
        
        return $beampro_user_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE beampro_users;");
    }
    
    public static function addSiteUserJoin($resultset) {    
        $resultset->addJoinCriteria("beampro_users site_user ON site_user.beampro_user_id = su.beampro_user_id");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'site_user.username',
                'alias' => 'personaname'
            ),
            array(
                'field' => 'site_user.beampro_id',
                'alias' => 'beampro_id'
            )
        ));
        
        return $resultset;
    }
}
