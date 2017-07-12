<?php
namespace Modules\Necrolab\Models;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Users\Twitch\Database\Twitch as TwitchUsersModel;
use \Modules\Necrolab\Models\Users\Discord\Database\Discord as DiscordUsersModel;
use \Modules\Necrolab\Models\Users\Reddit\Database\Reddit as RedditUsersModel;
use \Modules\Necrolab\Models\Users\Youtube\Database\Youtube as YoutubeUsersModel;
use \Modules\Necrolab\Models\Users\Twitter\Database\Twitter as TwitterUsersModel;
use \Modules\Necrolab\Models\Users\Beampro\Database\Beampro as BeamproUsersModel;
use \Modules\Necrolab\Models\Users\Hitbox\Database\Hitbox as HitboxUsersModel;
use \Modules\Necrolab\Models\CacheNames;

class ExternalSites
extends Necrolab {
    protected static $sites = array();
    
    protected static $sites_by_name = array();
    
    protected static $active_sites = array();
    
    protected static $active_sites_by_name = array();
    
    public static function loadAll() {        
        if(empty(static::$sites)) {
            $sites = array();
            
            $cache_key = 'sites';
            
            $local_cache = cache('local');
        
            $sites = $local_cache->get($cache_key);
        
            if(empty($sites)) {
                $sites = db()->getAll("
                    SELECT *
                    FROM external_sites
                    ORDER BY name ASC
                ");
                
                if(!empty($sites)) {
                    $local_cache->set($cache_key, $sites, NULL, 86400);
                }
            }
            
            if(!empty($sites)) {
                static::$sites = $sites;
                
                foreach($sites as $site) {
                    static::$sites[$site['external_site_id']] = $site;
                
                    static::$sites_by_name[$site['name']] = $site;
                }
            }
        }
    }
    
    protected static function loadActive() {
        static::loadAll();
        
        if(!empty(static::$sites)) {
            foreach(static::$sites as $site) {
                if(!empty($site['active'])) {
                    static::$active_sites[$site['external_site_id']] = $site;
                    
                    static::$active_sites_by_name[$site['name']] = $site;
                }
            }
        }
    }
    
    public static function getAll() {
        static::loadAll();
        
        return static::$sites;
    }
    
    public static function getActive() {
        static::loadActive();
        
        return static::$active_sites;
    }
    
    public static function getById($external_site_id) {
        static::loadAll();
        
        $external_site_record = array();
        
        if(!empty(static::$sites[$external_site_id])) {
            $external_site_record = static::$sites[$external_site_id];
        }
        
        return $external_site_record;
    }
    
    public static function getByName($name) {
        static::loadAll();
        
        $external_site_record = array();
        
        if(!empty(static::$sites_by_name[$name])) {
            $external_site_record = static::$sites_by_name[$name];
        }
        
        return $external_site_record;
    }
    
    public static function getActiveById($external_site_id) {
        static::loadActive();
        
        $external_site_record = array();
        
        if(!empty(static::$active_sites_by_name[$external_site_id])) {
            $external_site_record = static::$active_sites_by_name[$external_site_id];
        }
        
        return $external_site_record;
    }
    
    public static function getActiveByName($name) {
        static::loadActive();
        
        $external_site_record = array();
        
        if(!empty(static::$active_sites_by_name[$name])) {
            $external_site_record = static::$active_sites_by_name[$name];
        }
        
        return $external_site_record;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return array(
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name']
        );
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE external_sites;");
    }
    
    public static function getAllBaseResultset() {    
        $resultset = new SQL("external_sites");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'name'
            ),
            array(
                'field' => 'display_name'
            )
        ));
        
        $resultset->setFromTable('external_sites');
        
        $resultset->addSortCriteria('name', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllActiveBaseResultset() {
        $resultset = static::getAllBaseResultset();
        
        $resultset->addFilterCriteria('active = 1');
        
        return $resultset;
    }
    
    public static function addSiteUserFilter($resultset, $site_name) {    
        switch($site_name) {
            case 'twitch':
                $resultset = TwitchUsersModel::addSiteUserFilter($resultset);
                break;
            case 'discord':
                $resultset = DiscordUsersModel::addSiteUserFilter($resultset);
                break;
            case 'reddit':
                $resultset = RedditUsersModel::addSiteUserFilter($resultset);
                break;
            case 'youtube':
                $resultset = YoutubeUsersModel::addSiteUserFilter($resultset);
                break;
            case 'twitter':
                $resultset = TwitterUsersModel::addSiteUserFilter($resultset);
                break;
            case 'beampro':
                $resultset = BeamproUsersModel::addSiteUserFilter($resultset);
                break;
            case 'hitbox':
                $resultset = HitboxUsersModel::addSiteUserFilter($resultset);
                break;
            default:
                throw new Exception("Specified site '{$site_name}' is invalid.");
                break;
        }
    }
    
    public static function addSiteIdSelectFields($resultset) {        
        $active_sites = static::getActive();
        
        if(!empty($active_sites)) {
            foreach($active_sites as $active_site) {
                $field_name = "{$active_site['name']}_user_id";
            
                $resultset->addSelectField("su.{$field_name}", $field_name);
            }
        }
    }
    
    public static function addSiteUserLeftJoins($resultset) {  
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            ),
            array(
                'field' => 'su.personaname',
                'alias' => 'steam_personaname'
            ),
            array(
                'field' => 'su.profileurl',
                'alias' => 'steam_profile_url'
            ),
            array(
                'field' => 'su.beampro_user_id',
                'alias' => 'beampro_user_id'
            ),
            array(
                'field' => 'bu.beampro_id',
                'alias' => 'beampro_id'
            ),
            array(
                'field' => 'bu.username',
                'alias' => 'beampro_username'
            ),
            array(
                'field' => 'su.discord_user_id',
                'alias' => 'discord_user_id'
            ),
            array(
                'field' => "du.discord_id",
                'alias' => 'discord_id'
            ),
            array(
                'field' => "du.username",
                'alias' => 'discord_username'
            ),
            array(
                'field' => "du.discriminator",
                'alias' => 'discord_discriminator'
            ),
            array(
                'field' => 'su.reddit_user_id',
                'alias' => 'reddit_user_id'
            ),
            array(
                'field' => "ru.reddit_id",
                'alias' => 'reddit_id'
            ),
            array(
                'field' => "ru.username",
                'alias' => 'reddit_username'
            ),
            array(
                'field' => 'su.twitch_user_id',
                'alias' => 'twitch_user_id'
            ),
            array(
                'field' => 'tu.twitch_id',
                'alias' => 'twitch_id'
            ),
            array(
                'field' => 'tu.user_display_name',
                'alias' => 'twitch_username'
            ),
            array(
                'field' => 'su.twitter_user_id',
                'alias' => 'twitter_user_id'
            ),
            array(
                'field' => 'twu.twitter_id',
                'alias' => 'twitter_id'
            ),
            array(
                'field' => 'twu.nickname',
                'alias' => 'twitter_nickname'
            ),
            array(
                'field' => 'twu.name',
                'alias' => 'twitter_name'
            ),
            array(
                'field' => 'su.youtube_user_id',
                'alias' => 'youtube_user_id'
            ),
            array(
                'field' => "yu.youtube_id",
                'alias' => 'youtube_id'
            ),
            array(
                'field' => "yu.youtube_id",
                'alias' => 'youtube_username'
            )            
        ));
    
        $resultset->addLeftJoinCriteria('beampro_users bu ON bu.beampro_user_id = su.beampro_user_id');
        $resultset->addLeftJoinCriteria('discord_users du ON du.discord_user_id = su.discord_user_id');
        $resultset->addLeftJoinCriteria('reddit_users ru ON ru.reddit_user_id = su.reddit_user_id');
        $resultset->addLeftJoinCriteria('twitch_users tu ON tu.twitch_user_id = su.twitch_user_id');
        $resultset->addLeftJoinCriteria('twitter_users twu ON twu.twitter_user_id = su.twitter_user_id');
        $resultset->addLeftJoinCriteria('youtube_users yu ON yu.youtube_user_id = su.youtube_user_id');        
    }
    
    public static function addToSiteIdIndexes(&$indexes, $entry, $base_index_name, $index_value, $index_key = NULL) {
        $active_sites = static::getActive();
        
        if(!empty($active_sites)) {
            foreach($active_sites as $active_site) {
                $site_name = $active_site['name'];
            
                if(!empty($entry["{$site_name}_user_id"])) {
                    if(!isset($index_key)) {
                        $indexes[CacheNames::getIndexName($base_index_name, array(
                            $active_site['external_site_id']
                        ))][] = $index_value;
                    }
                    else {
                        $indexes[CacheNames::getIndexName($base_index_name, array(
                            $active_site['external_site_id']
                        ))][$index_key] = $index_value;
                    }
                }
            }
        }
        
        if(!isset($index_key)) {
            $indexes[CacheNames::getIndexName($base_index_name, array(
                CacheNames::NO_ID
            ))][] = $index_value;
        }
        else {
            $indexes[CacheNames::getIndexName($base_index_name, array(
                CacheNames::NO_ID
            ))][$index_key] = $index_value;
        }
    }
}