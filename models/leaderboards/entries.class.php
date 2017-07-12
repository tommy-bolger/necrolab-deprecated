<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \DateTime;
use \Modules\Necrolab\Models\Necrolab;
use \Framework\Modules\Module;

class Entries
extends Necrolab {
    protected $lbid;
    
    protected $entries = array();
    
    public static function saveTempXml($lbid, DateTime $date, $page, $xml) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/temp/{$date->format('Y-m-d')}/{$lbid}";
        
        if(!is_dir($snapshot_path)) {
            mkdir($snapshot_path);
        }
    
        file_put_contents("{$snapshot_path}/page_{$page}.xml", $xml);
    }
    
    public static function getCacheQueueName() {
        return 'leaderboard_entries_cache';
    }
    
    public static function addToCacheQueue(DateTime $date) {        
        static::addDateToQueue(static::getCacheQueueName(), $date);
    }
    
    public static function getDailyCacheQueueName() {
        return 'daily_leaderboard_entries_cache';
    }
    
    public static function addToDailyCacheQueue(DateTime $date) {        
        static::addDateToQueue(static::getDailyCacheQueueName(), $date);
    }
}