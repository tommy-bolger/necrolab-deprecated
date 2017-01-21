<?php
namespace Modules\Necrolab\Models\Leaderboards\Cache;

use \Modules\Necrolab\Models\Leaderboards\Blacklist as BaseBlacklist;
use \Modules\Necrolab\Models\Leaderboards\Database\Blacklist as DatabaseBlacklist;

class Blacklist
extends BaseBlacklist {    
    public static function load() {
        if(empty(static::$blacklist)) {
            $blacklist = cache('read')->lRange(CacheNames::getBlacklistName(), 0, -1);
            
            static::$blacklist = array_combine($blacklist, $blacklist);
        }
    }
    
    public static function loadIntoCache() {
        $blacklisted_leaderboards = DatabaseBlacklist::getBlacklistedLeaderboards();
    
        if(!empty($blacklisted_leaderboards)) {
            $transaction = cache('write')->transaction();
            
            $transaction->delete(CacheNames::getBlacklistName());
            
            foreach($blacklisted_leaderboards as $blacklisted_leaderboard_lbid) {
                $transaction->rPush(CacheNames::getBlacklistName(), (int)$blacklisted_leaderboard_lbid);
            }
            
            $transaction->commit();
        }
    }
}