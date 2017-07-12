<?php
namespace Modules\Necrolab\Models;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;

class Releases
extends Necrolab {
    protected static $releases = array();
    
    protected static function loadAll() {        
        if(empty(static::$releases)) {
            $releases = array();
            
            $cache_key = 'releases';
            
            $local_cache = cache('local');
        
            $releases = $local_cache->get($cache_key);
        
            if(empty($releases)) {
                $releases = db()->getAll("
                    SELECT *
                    FROM releases
                    ORDER BY start_date
                ");
                
                if(!empty($releases)) {
                    
                    $local_cache->set($cache_key, $releases, NULL, 86400);
                }
            }
            
            if(!empty($releases)) {
                static::$releases = $releases;
            }
        }
    }
    
    public static function getAll() {
        static::loadAll();
        
        return static::$releases;
    }
    
    public static function getById($release_id) {
        static::loadAll();
        
        $release_record = array();
        
        if(!empty(static::$releases)) {
            foreach(static::$releases as $release) {
                if($release['release_id'] == $release_id) {
                    $release_record = $release;
                    
                    break;
                }
            }
        }
        
        return $release_record;
    }
    
    public static function getByName($name) {
        static::loadAll();
        
        $release_record = array();
        
        if(!empty(static::$releases)) {
            foreach(static::$releases as $release) {
                if($release['name'] == $name) {
                    $release_record = $release;
                    
                    break;
                }
            }
        }
        
        return $release_record;
    }
    
    public static function getByDate(DateTime $date) {
        static::loadAll();
        
        $release_records = array();
        
        if(!empty(static::$releases)) {        
            foreach(static::$releases as $release) {
                $start_date = new DateTime($release['start_date']);
                $end_date = new DateTime($release['end_date']);
            
                if($date >= $start_date && $date <= $end_date) {
                    $release_records[] = $release;
                }
            }
        }
        
        return $release_records;
    }
    
    public static function getByDateAndId(DateTime $date, $release_id) {
        static::loadAll();
        
        $release_record = array();
        
        if(!empty(static::$releases)) {        
            foreach(static::$releases as $release) {
                $start_date = new DateTime($release['start_date']);
                $end_date = new DateTime($release['end_date']);
            
                if($release_id == $release['release_id'] && $date >= $start_date && $date <= $end_date) {
                    $release_record = $release;
                }
            }
        }
        
        return $release_record;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return array(
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name'],
            'start_date' => $data_row['start_date'],
            'end_date' => $data_row['end_date']
        );
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE releases;");
    }
    
    public static function getAllBaseResultset() {    
        $resultset = new SQL("releases");
        
        $resultset->setBaseQuery("
            SELECT *
            FROM releases
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addSortCriteria('start_date', 'DESC');
        
        return $resultset;
    }
    
    public static function getAllActiveBaseResultset() {    
        $resultset = static::getAllBaseResultset();
        
        $resultset->addFilterCriteria('start_date <= :start_date', array(
            ':start_date' => date('Y-m-d')
        ));
        
        return $resultset;
    }
}