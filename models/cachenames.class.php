<?php

namespace Modules\Necrolab\Models;

class CacheNames {    
    const ENTRIES = 'e';
    
    const INDEX = 'idx';
    
    const IDS = 'ids';
    
    const NO_ID = '0';
    
    const SCORE = 'sc';
    
    const SPEED = 'sp';
    
    const DEATHLESS = 'de';
    
    public static function getIndexName($base_name, array $index_segments) {
        $index_name = implode(':', $index_segments);
        
        $cache_name = $base_name;
        
        if(!empty($index_segments)) {
            $cache_name .= ":{$index_name}";
        }
    
        return $cache_name;
    }
    
    public static function getCharactersName() {
        return 'characters';
    }
    
    public static function getFullClassName() {
        return get_called_class();
    }
}