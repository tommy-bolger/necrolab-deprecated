<?php
namespace Modules\Necrolab\Models\Characters;

use \Exception;

class Characters {
    protected static $characters = array();
    
    protected static $characters_by_id = array();
    
    protected static $active_characters = array();
    
    protected static $characters_by_sort_order = array();
    
    protected static function loadAll() {}
    
    protected static function loadActive() {
        if(empty(static::$active_characters)) {
            static::loadAll();
        
            if(!empty(static::$characters)) {
                foreach(static::$characters as $character) {
                    if($character['is_active'] == 1) {
                        static::$active_characters[$character['name']] = $character;
                    }
                }
            }
        }
    }
    
    protected static function loadAllBySortOrder() {
        if(empty(static::$characters_by_sort_order)) {
            static::loadAll();
        
            if(!empty(static::$characters)) {  
                foreach(static::$characters as $character) {
                    static::$characters_by_sort_order[$character['sort_order']] = $character;
                }
            }
        }
    }
    
    protected static function loadAllById() {
        if(empty(static::$characters_by_id)) {
            static::loadAll();
        
            if(!empty(static::$characters)) {  
                foreach(static::$characters as $character) {
                    static::$characters_by_id[$character['character_id']] = $character;
                }
            }
        }
    }
    
    public static function getAll() {
        static::loadAll();
        
        return static::$characters;
    }
    
    public static function getActive() {
        static::loadActive();
        
        return static::$active_characters;
    }

    public static function getAllBySortOrder() {
        static::loadAllBySortOrder();
        
        return static::$characters_by_sort_order;
    }
    
    public function __get($character_name) {
        static::loadAll();
        
        if(!isset(static::$characters[$character_name])) {
            throw new Exception("Character '{$character_name}' does not exist.");
        }
        
        return static::$characters[$character_name];
    }
    
    public static function getById($character_id) {
        static::loadAllById();
        
        if(!isset(static::$characters_by_id[$character_id])) {
            throw new Exception("Character id '{$character_id}' does not exist.");
        }
        
        return static::$characters_by_id[$character_id];
    }
    
    public static function getActiveByName($character_name) {
        static::loadActive();

        $character_record = array();
        
        if(isset(static::$active_characters[$character_name])) {
            $character_record = static::$active_characters[$character_name];
        }
        
        return $character_record;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return array(
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name']
        );
    }
}