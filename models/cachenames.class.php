<?php

namespace Modules\Necrolab\Models;

class CacheNames {    
    const ENTRIES = 'e';
    
    const PROPERTIES = 'pr';

    const FILTER = 'f';
    
    const TEMP_NAME = 't';
    
    const LAST_UPDATED = 'lu';
    
    public static function addTempPrefix($key_name) {
        return self::TEMP_NAME . ':' . $key_name;
    }
    
    public static function getFullClassName() {
        return get_called_class();
    }
}