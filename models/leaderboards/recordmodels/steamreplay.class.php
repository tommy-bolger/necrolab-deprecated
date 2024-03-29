<?php

namespace Modules\Necrolab\Models\Leaderboards\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;
use \Modules\Necrolab\Models\Leaderboards\Replays;

class SteamReplay
extends RecordModel {
    protected $run_result;
    
    protected $is_win;

    protected $replay_version;

    protected $seed;
    
    public function setPropertiesFromArray(array $property_values, $error_on_invalid_property = false) {}
    
    public function setPropertiesFromIndexedArray(array $indexed_property_values) {}
    
    public function setPropertiesFromObject($property_values, $error_on_invalid_property = false) {}
    
    public function setPropertiesFromReplayData($replay_file_data) {
        $replay_file_split = explode('%*#%*', $replay_file_data);
        
        if(count($replay_file_split) == 2) {
            $this->run_result = $replay_file_split[0];
            
            if(empty($this->run_result)) {
                $this->is_win = 1;
                $this->run_result = 'WIN';
            }
            else {
                $this->is_win = 0;
            }
        
            $replay_data = $replay_file_split[1];
            
            $replay_data_segments = explode('\\n', $replay_data);
            
            $this->replay_version = $replay_data_segments[0];
            
            if($this->replay_version < 82) {
                $zone_1_seed = $replay_data_segments[10];

                $this->seed = Replays::getSeedFromZ1Seed($zone_1_seed);
            }
            elseif($this->replay_version < 84) {
                $zone_1_seed = $replay_data_segments[10];

                $this->seed = Replays::getOldDLCSeedFromZ1Seed($zone_1_seed);
            }
            else {
                $zone_1_seed = $replay_data_segments[7];

                $this->seed = Replays::getDLCSeedFromZ1Seed($zone_1_seed);
            }
        }
    }
    
    public function setPropertiesFromReplayFile($replay_file_path) {
        $this->setPropertiesFromReplayData(Replays::getFile($replay_file_path));
    }
}