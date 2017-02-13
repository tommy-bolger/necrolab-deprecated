<?php
namespace Modules\Necrolab\Models\ExternalSites;

use \DateTime;
use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class ExternalSites
extends Necrolab {
    protected static $sites = array();
    
    protected static $active_sites = array();

    protected static function loadAll() {}
    
    protected static function loadActive() {
        static::loadAll();
        
        if(!empty(static::$sites)) {
            foreach(static::$sites as $site) {
                if(!empty($site['active'])) {
                    static::$active_sites[] = $site;
                }
            }
        }
    }
    
    public static function getAll() {
        static::loadAll();
        
        return static::$sites;
    }
    
    public static function getActive() {
        static::loadActive();
        
        return static::$sites;
    }
    
    public static function getById($external_site_id) {
        static::loadAll();
        
        $external_site_record = array();
        
        if(!empty(static::$sites)) {
            foreach(static::$sites as $site) {
                if($site['external_site_id'] == $external_site_id) {
                    $external_site_record = $site;
                    
                    break;
                }
            }
        }
        
        return $external_site_record;
    }
    
    public static function getByName($name) {
        static::loadAll();
        
        $external_site_record = array();
        
        if(!empty(static::$sites)) {
            foreach(static::$sites as $site) {
                if($site['name'] == $name) {
                    $external_site_record = $site;
                    
                    break;
                }
            }
        }
        
        return $external_site_record;
    }
    
    public static function getActiveByName($name) {
        static::loadActive();
        
        $external_site_record = array();
        
        if(!empty(static::$active_sites)) {
            foreach(static::$active_sites as $active_site) {
                if($active_site['name'] == $name) {
                    $external_site_record = $active_site;
                    
                    break;
                }
            }
        }
        
        return $external_site_record;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return array(
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name']
        );
    }
}