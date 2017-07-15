<?php
namespace Modules\Necrolab\Models;

use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\CacheNames;

class Characters
extends Necrolab {
    protected static $characters = array();
    
    protected static $characters_by_id = array();
    
    protected static $active_characters = array();
    
    protected static $characters_by_sort_order = array();
    
    protected static function loadAll() {        
        if(empty(static::$characters)) {
            $characters = array();
            
            $cache_key = CacheNames::getCharactersName();
            
            $local_cache = cache('local');
        
            $characters = $local_cache->get($cache_key);
        
            if(empty($characters)) {
                $characters = db()->getAll("
                    SELECT *
                    FROM characters
                    ORDER BY sort_order ASC
                ");
                
                if(!empty($characters)) {
                    $local_cache->set($cache_key, $characters, NULL, 86400);
                }
            }
            
            if(!empty($characters)) {
                foreach($characters as $character) {                
                    static::$characters[$character['name']] = $character;
                }
            }
        }
    }
    
    protected static function loadActive() {
        if(empty(static::$active_characters)) {
            static::loadAll();
        
            if(!empty(static::$characters)) {
                foreach(static::$characters as $character) {
                    if(!empty($character['is_active'])) {
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
        
        $character = array();
        
        if(isset(static::$characters[$character_name])) {
            $character = static::$characters[$character_name];
        }
        
        return $character;
    }
    
    public static function getById($character_id) {
        static::loadAllById();
        
        $character = array();
        
        if(isset(static::$characters_by_id[$character_id])) {
            $character = static::$characters_by_id[$character_id];
        }
        
        return $character;
    }
    
    public static function getActiveByName($character_name) {
        static::loadActive();

        $character_record = array();
        
        if(isset(static::$active_characters[$character_name])) {
            $character_record = static::$active_characters[$character_name];
        }
        
        return $character_record;
    }
    
    public static function isCoOpCharacter($character_name) {
        $co_op_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            'nocturna',
            'diamond',
            'mary',
            'tempo'
        );
        
        return in_array($character_name, $co_op_characters, true);
    }
    
    public static function isSeededCharacter($character_name) {
        $seeded_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            'nocturna',
            'diamond',
            'mary',
            'tempo'
        );
        
        return in_array($character_name, $seeded_characters, true);
    }
    
    public static function isDeathlessCharacter($character_name) {
        $deathless_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            'nocturna',
            'diamond',
            'mary',
            'tempo'
        );
        
        return in_array($character_name, $deathless_characters, true);
    }
    
    public static function isModeCharacter($character_name) {
        $mode_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            'nocturna',
            'diamond',
            'mary',
            'tempo'
        );
        
        return in_array($character_name, $mode_characters, true);
    }
    
    public static function isOriginalCharacter($character_name) {
        $original_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            "story",
            "all"
        );
        
        return in_array($character_name, $original_characters, true);
    }
    
    public static function isAmplifiedDlcCharacter($character_name) {
        $dlc_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            "story",
            "all",
            'nocturna',
            'diamond',
            'mary',
            'tempo',
            'all_dlc'
        );
        
        return in_array($character_name, $dlc_characters, true);
    }
    
    public static function getFormattedApiRecord($data_row) {
        return array(
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name']
        );
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE characters;");
    }
    
    public static function getAllBaseResultset() {    
        $resultset = new SQL("characters");
        
        $resultset->setBaseQuery("
            SELECT *
            FROM characters
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addSortCriteria('sort_order', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllActiveBaseResultset() {    
        $resultset = static::getAllBaseResultset();
        
        $resultset->addFilterCriteria('is_active = 1');
        
        return $resultset;
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'c.character_id',
                'alias' => 'character_id'
            ),
            array(
                'field' => 'c.name',
                'alias' => 'character_name',
            ),
            array(
                'field' => 'c.sort_order',
                'alias' => 'character_number',
            )
        ));
    }
}