<?php
namespace Modules\Necrolab\Models\Releases\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Releases\Releases as BaseReleases;

class Releases
extends BaseReleases {
    protected static function loadAll() {        
        if(empty(static::$releases)) {        
            $releases = db()->getAll("
                SELECT *
                FROM releases
            ");
            
            if(!empty($releases)) {
                static::$releases = $releases;
            }
        }
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