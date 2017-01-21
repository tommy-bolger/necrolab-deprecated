<?php
namespace Modules\Necrolab\Controllers\Cli\Rankings;

use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Characters\Database\Characters as DatabaseCharacters;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\Entries as DatabaseLeaderboardEntries;
use \Modules\Necrolab\Models\Rankings\Database\Rankings as DatabaseRankings;
use \Modules\Necrolab\Models\Rankings\Database\Entries as DatabaseEntries;
use \Modules\Necrolab\Models\Rankings\Cache\Entries as CacheEntries;
use \Modules\Necrolab\Models\Rankings\Cache\Entry as CacheEntry;
use \Modules\Necrolab\Models\Rankings\Cache\Power\Rankings as CachePowerRankings;
use \Modules\Necrolab\Models\Rankings\Cache\Speed\Rankings as CacheSpeedRankings;
use \Modules\Necrolab\Models\Rankings\Cache\Score\Rankings as CacheScoreRankings;
use \Modules\Necrolab\Models\Rankings\Cache\Deathless\Rankings as CacheDeathlessRankings;
use \Modules\Necrolab\Models\Rankings\Cache\Character\Rankings as CacheCharacterRankings;
use \Modules\Necrolab\Models\Rankings\Database\RecordModels\PowerRankingEntry;

class Power
extends Cli { 
    protected $cache;
    
    protected $as_of_date;
    
    public function init() {
        $this->cache = cache();
    }
    
    public function actionGenerate($date = NULL) {
        $date = new DateTime($date);
        
        $this->as_of_date = $date;
        
        $leaderboard_entries_resulset = DatabaseLeaderboardEntries::getPowerRankingsResultset($date);
        
        $leaderboard_entries = $leaderboard_entries_resulset->prepareExecuteQuery();

        $transaction = $this->cache->transaction();
        
        while($leaderboard_entry = db()->getStatementRow($leaderboard_entries)) {
            $leaderboard_record = Leaderboards::get($leaderboard_entry['lbid']);
        
            CacheEntry::saveFromLeaderboardEntry($leaderboard_entry, $leaderboard_record, $transaction);
        }
        
        $transaction->commit();    
        
        CacheSpeedRankings::generateRanksFromPoints($this->as_of_date);
        CacheScoreRankings::generateRanksFromPoints($this->as_of_date);
        CacheDeathlessRankings::generateRanksFromPoints($this->as_of_date);

        $characters = DatabaseCharacters::getActive();
        
        if(!empty($characters)) {
            foreach($characters as $character) {
                CacheCharacterRankings::generateRanksFromPoints($this->as_of_date, $character['name']);
            }
        }
        
        CachePowerRankings::generateRanksFromPoints($this->as_of_date);
        
        db()->beginTransaction();
        
        $power_ranking_id = DatabaseRankings::save($this->as_of_date);
        
        DatabaseEntries::clear($power_ranking_id, $this->as_of_date);
    
        CacheEntries::saveToDatabase($power_ranking_id, $this->as_of_date, $this->cache);
        
        db()->commit();
    }
}