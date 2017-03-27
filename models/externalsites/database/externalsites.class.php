<?php
namespace Modules\Necrolab\Models\ExternalSites\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\ExternalSites\ExternalSites as BaseExternalSites;
use \Modules\Necrolab\Models\Users\Twitch\Database\Twitch as TwitchUsersModel;
use \Modules\Necrolab\Models\Users\Discord\Database\Discord as DiscordUsersModel;
use \Modules\Necrolab\Models\Users\Reddit\Database\Reddit as RedditUsersModel;
use \Modules\Necrolab\Models\Users\Youtube\Database\Youtube as YoutubeUsersModel;
use \Modules\Necrolab\Models\Users\Twitter\Database\Twitter as TwitterUsersModel;
use \Modules\Necrolab\Models\Users\Beampro\Database\Beampro as BeamproUsersModel;
use \Modules\Necrolab\Models\Users\Hitbox\Database\Hitbox as HitboxUsersModel;

class ExternalSites
extends BaseExternalSites {
    protected static function loadAll() {        
        if(empty(static::$sites)) {        
            $sites = db()->getAll("
                SELECT *
                FROM external_sites
            ");
            
            if(!empty($sites)) {
                static::$sites = $sites;
            }
        }
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
    
    public static function addSiteUserJoin($resultset, $site_name) {    
        switch($site_name) {
            case 'twitch':
                $resultset = TwitchUsersModel::addSiteUserJoin($resultset);
                break;
            case 'discord':
                $resultset = DiscordUsersModel::addSiteUserJoin($resultset);
                break;
            case 'reddit':
                $resultset = RedditUsersModel::addSiteUserJoin($resultset);
                break;
            case 'youtube':
                $resultset = YoutubeUsersModel::addSiteUserJoin($resultset);
                break;
            case 'twitter':
                $resultset = TwitterUsersModel::addSiteUserJoin($resultset);
                break;
            case 'beampro':
                $resultset = BeamproUsersModel::addSiteUserJoin($resultset);
                break;
            case 'hitbox':
                $resultset = HitboxUsersModel::addSiteUserJoin($resultset);
                break;
            default:
                throw new Exception("Specified site '{$site_name}' is invalid.");
                break;
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
                'field' => 'tu.user_display_name',
                'alias' => 'twitch_username'
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
                'field' => "ru.username",
                'alias' => 'reddit_username'
            ),
            array(
                'field' => "yu.youtube_id",
                'alias' => 'youtube_username'
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
                'field' => 'bu.username',
                'alias' => 'beampro_username'
            ),
            array(
                'field' => 'NULL',
                'alias' => 'hitbox_username'
            ),
            array(
                'field' => 'su.website',
                'alias' => 'website'
            )
        ));
    
        $resultset->addLeftJoinCriteria('twitch_users tu ON tu.twitch_user_id = su.twitch_user_id');
        $resultset->addLeftJoinCriteria('discord_users du ON du.discord_user_id = su.discord_user_id');
        $resultset->addLeftJoinCriteria('reddit_users ru ON ru.reddit_user_id = su.reddit_user_id');
        $resultset->addLeftJoinCriteria('youtube_users yu ON yu.youtube_user_id = su.youtube_user_id');
        $resultset->addLeftJoinCriteria('twitter_users twu ON twu.twitter_user_id = su.twitter_user_id');
        $resultset->addLeftJoinCriteria('beampro_users bu ON bu.beampro_user_id = su.beampro_user_id');
        //$resultset->addLeftJoinCriteria('hitbox_users hu ON hu.hitbox_user_id = su.twitch_user_id');
    }
}