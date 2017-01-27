<?php
namespace Modules\Necrolab\Models\Releases\Database;

use \DateTime;
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
}