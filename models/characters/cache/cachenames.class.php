<?php

namespace Modules\Necrolab\Models\Characters\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;

class CacheNames {
    const CHARACTERS = 'ch';
    
    const IDS_BY_NAME = 'n';
    
    public static function getEntriesName() {
        return self::CHARACTERS . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getEntryName($character_name) {
        return self::getEntriesName() . ":{$character_name}";
    }
    
    public static function getIdsByName() {
        return self::CHARACTERS . ':' . self::IDS_BY_NAME;
    }
}