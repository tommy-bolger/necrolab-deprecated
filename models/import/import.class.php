<?php
namespace Modules\Necrolab\Models\Import;

class Import {    
    public function run() { 
        //$this->cache->clear();
    
        $start_time = time();
    
        $this->loadCharacterCache();
         
        $this->loadBlacklistCache();
         
        $this->loadUsersCache();
        
        $this->loadDailyRankingDayTypes();
        
        $this->loadLatestDailySeason();
         
        $this->importLeaderboards();

        $this->saveImportedLeaderboards();

        $this->importLeaderboardEntries();
        
        $this->generatePowerRankings();
        
        $this->saveLeaderboardEntries();
        
        $this->saveRankedPowerLeaderboards();
        
        $this->generateDailyRankingStats();
        
        $this->saveDailyRankingStats();
        
        //$this->cache->clear();
        
        $end_time = time();
        
        $run_time = $end_time - $start_time;
        
        $this->framework->coutLine("Import took {$run_time} seconds.");
    }
}