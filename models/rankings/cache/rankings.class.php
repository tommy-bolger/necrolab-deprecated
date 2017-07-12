<?php
namespace Modules\Necrolab\Models\Rankings\Cache;

use \Modules\Necrolab\Models\Rankings\Rankings as BaseRankings;
use \Modules\Necrolab\Models\SteamUsers\Cache\SteamUsers;

class Rankings 
extends BaseRankings {
    public static function getModesUsed($release_id, $seeded, $cache) {
        return $cache->hGetAll(CacheNames::getPowerRankingModesName($release_id, $seeded));
    }
}