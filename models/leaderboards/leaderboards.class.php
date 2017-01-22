<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \DateTime;
use \Exception;
use \SimpleXMLElement;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \RegexIterator;
use \RecursiveRegexIterator;
use \Framework\Data\XMLWrite;
use \Framework\Utilities\File;
use \Framework\Modules\Module;
use \Modules\Necrolab\Models\Necrolab;

class Leaderboards
extends Necrolab {
    protected static $leaderboards = array();
    
    public static function loadAll() {}

    public static function get($lbid) {
        static::loadAll();
        
        $leaderboard_record = array();
        
        if(!empty(static::$leaderboards[$lbid])) {
            $leaderboard_record = static::$leaderboards[$lbid];
        }
        
        return $leaderboard_record;
    }
    
    public static function getDailyByDate(DateTime $date) {
        static::loadAll();
        
        $leaderboard_record = array();
        
        if(!empty(static::$leaderboards)) {            
            foreach(static::$leaderboards as $leaderboard) {
                if($leaderboard['is_daily_ranking'] == 1) {
                    $daily_date = new DateTime($leaderboard['daily_date']);
                    
                    if($daily_date == $date) {
                        $leaderboard_record = $leaderboard;
                        
                        break;
                    }
                }
            }
        }
        
        return $leaderboard_record;
    }
    
    public static function getLatestDaily() {
        static::loadAll();
        
        $leaderboard_record = array();
        $latest_date = NULL;
        $current_date = new DateTime();
        
        if(!empty(static::$leaderboards)) {            
            foreach(static::$leaderboards as $leaderboard) {
                if($leaderboard['is_daily_ranking'] == 1) {
                    $daily_date = new DateTime($leaderboard['daily_date']);
                    
                    if(($daily_date <= $current_date) && (empty($latest_date) || $daily_date > $latest_date)) {
                        $leaderboard_record = $leaderboard;
                        $latest_date = clone $daily_date;
                    }
                }
            }
        }
        
        return $leaderboard_record;
    }

    public static function getGroupedLeaderboards($category_name, array $ungrouped_leaderboards) {
        $grouped_score_leaderboards = array(
            'main' => array(
                'name' => 'All Zones Mode',
                'characters' => array()
            ),
            'seeded' => array(
                'name' => 'Seeded',
                'characters' => array()
            ),
            'custom' => array(
                'name' => 'Custom Music',
                'characters' => array()
            ),
            'seeded_custom' => array(
                'name' => 'Seeded Custom Music',
                'characters' => array()
            ),
            'co_op' => array(
                'name' => 'Co-op',
                'characters' => array()
            ),
            'seeded_co_op' => array(
                'name' => 'Seeded Co-op',
                'characters' => array()
            ),
            'co_op_custom' => array(
                'name' => 'Co-op Custom Music',
                'characters' => array()
            ),
            'seeded_co_op_custom' => array(
                'name' => 'Seeded Co-op Custom Music',
                'characters' => array()
            )
        );
        
        if(!empty($ungrouped_leaderboards)) {
            foreach($ungrouped_leaderboards as $leaderboard_record) {   
                if($category_name == 'deathless' || empty($leaderboard_record['is_deathless'])) {
                    $character_name = $leaderboard_record['character_name'];
                
                    $group_name_segments = array();

                    if($leaderboard_record['is_seeded'] == 1) {
                        $group_name_segments[] = 'seeded';
                    }
                    
                    if($leaderboard_record['is_co_op'] == 1) {
                        $group_name_segments[] = 'co_op';
                    }
                    
                    if($leaderboard_record['is_custom'] == 1) {
                        $group_name_segments[] = 'custom';
                    }

                    if(empty($group_name_segments)) {
                        $group_name_segments[] = 'main';
                    }
                    
                    $group_name = implode('_', $group_name_segments);
                    
                    $grouped_score_leaderboards[$group_name]['characters'][$character_name] = $leaderboard_record['lbid'];
                }
            }
        }
        
        return $grouped_score_leaderboards;
    }
    
    public static function getAllByCategory($category_name) {}
    
    public static function getFancyName($leaderboard_record, $prepend_run_type = false) {
        $raw_leaderboard_name = $leaderboard_record['name'];
        
        $fancy_leaderboard_name = '';
        
        if($prepend_run_type) {
            if($leaderboard_record['is_score_run'] == 1) {
                $fancy_leaderboard_name .= 'Score';
            }
            elseif($leaderboard_record['is_speedrun'] == 1) {
                $fancy_leaderboard_name .= 'Speedrun';
            }
            elseif($leaderboard_record['is_deathless'] == 1) {
                $fancy_leaderboard_name .= 'Deathless';
            }
            
            $fancy_leaderboard_name .= ' - ';
        }
        
        $second_half_name_segments = array();
        
        if($leaderboard_record['is_story_mode'] == 1) {
            $second_half_name_segments[] = 'Story Mode';
        }
        
        if($leaderboard_record['is_all_character'] == 1) {
            $second_half_name_segments[] = 'All Chars';
        }
        
        if($leaderboard_record['is_seeded'] == 1) {
            $second_half_name_segments[] = 'Seeded';
        }
        
        if($leaderboard_record['is_co_op'] == 1) {
            $second_half_name_segments[] = 'Co-op';
        }
        
        if($leaderboard_record['is_custom'] == 1) {
            $second_half_name_segments[] = 'Custom Music';
        }        
        
        if(empty($second_half_name_segments)) {
            $second_half_name_segments[] = 'All Zones Mode';
        }
        
        $second_half_name = implode(' ', $second_half_name_segments);
        
        $fancy_leaderboard_name .= $second_half_name;
        
        return $fancy_leaderboard_name;
    }
    
    public static function getSteamXml($leaderboards_url) {    
        $request = curl_init($leaderboards_url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        
        $leaderboards_xml = curl_exec($request);
        
        $http_response_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
        
        $request_error = curl_error($request);
        
        if(!empty($request_error)) {
            $error_code = curl_errno($request);
        
            if(!empty($error_code)) {
                $error_message = curl_strerror($error_code);
                
                throw new Exception("HTTP request to Steam leaderboards encountered an error with number '{$error_code}' and message '{$error_message}'.");
            }
            else {
                throw new Exception('Response from Steam leaderboards returned with an unknown error.');
            }
        }
        
        return $leaderboards_xml;
    }
    
    public static function getParsedXml($unparsed_xml) {
        //Strip any non UTF-8 character from the document that causes the XML to break.
        $leaderboards_xml = preg_replace('/[^[:print:]]/', '', $unparsed_xml);
        
        unset($unparsed_xml);
        
        $leaderboards = NULL;
        
        if(!empty($leaderboards_xml)) {
            $leaderboards = XMLWrite::convertXmlToObject(new SimpleXMLElement($leaderboards_xml));
        }
        
        unset($leaderboards_xml);
        
        return $leaderboards;
    }
    
    public static function deleteXml(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/{$date->format('Y-m-d')}";
        
        if(is_dir($snapshot_path)) {
            File::deleteDirectoryRecursive("{$snapshot_path}");
        }
    }
    
    public static function saveXml(DateTime $date, $xml) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/{$date->format('Y-m-d')}";
        
        if(!is_dir($snapshot_path)) {
            mkdir($snapshot_path);
        }
    
        file_put_contents("{$snapshot_path}/leaderboards.xml.gz", gzencode($xml, 9));
    }
    
    /*public static function getOldXml($file_path) {    
        return gzuncompress(file_get_contents($file_path));
    }*/
    
    public static function getXml($file_path) {    
        return gzdecode(file_get_contents($file_path));
    }
    
    public static function getXmlFiles(DateTime $date) {  
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/{$date->format('Y-m-d')}";
        
        $xml_file_groups = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, '/^.+\.gz$/i', RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                if(strpos($matched_file[0], 'leaderboards.xml.gz') !== false) {
                    $xml_file_groups['leaderboards_xml'] = $matched_file[0];
                }
                else {
                    $file_name = $matched_file[0];
                    $file_name_split = explode('/', $matched_file[0]);
                    
                    $xml_file_name = array_pop($file_name_split);
                    $xml_file_name_split = explode('_', $xml_file_name);
                    
                    $page_number = array_pop($xml_file_name_split);
                    $page_number = (int)str_replace('.xml.gz', '', $page_number);
                    
                    $lbid = array_pop($file_name_split);
                    
                    if(empty($xml_file_groups[$lbid])) {
                        $xml_file_groups[$lbid] = array();
                    }
                        
                    $xml_file_groups[$lbid][$page_number] = $matched_file[0];
                }
            }
            
            if(!empty($xml_file_groups)) {
                foreach($xml_file_groups as $lbid => &$xml_files) {
                    if($lbid != 'leaderboards_xml') {
                        ksort($xml_files);
                    }
                }
            }
        }
        
        return $xml_file_groups;
    }
}