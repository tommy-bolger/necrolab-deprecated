<?php
/**
* The user stats page.
* Copyright (c) 2015, Tommy Bolger
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without 
* modification, are permitted provided that the following conditions 
* are met:
* 
* Redistributions of source code must retain the above copyright 
* notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright 
* notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* Neither the name of the author nor the names of its contributors may 
* be used to endorse or promote products derived from this software 
* without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
* POSSIBILITY OF SUCH DAMAGE.
*/
namespace Modules\Necrolab\Controllers;

use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Form\Form;
use \Framework\Html\Form\TableForm;
use \Framework\Html\Table\Table;
use \Modules\Necrolab\Models\SteamUsers;

class Player
extends Necrolab {    
    protected $right_id;
    
    protected $compare_id;

    public function __construct() {
        parent::__construct();
        
        $this->active_page = 'versus';
        
        $this->right_id = request()->right_id;
        
        $this->left_id = request()->left_id;
    }
    
    protected function loadModule() {
        parent::loadModule();
        
        $this->page->addCssFiles(array(
            'page/player.css'
        ));
    }
    
    protected function constructContent() {
        if(empty($this->right_id)) {
            Http::redirect('/');
        }
        
        if(empty($this->left_id)) {
            Http::redirect('/');
        }
        
        $left_steam_user_data = SteamUsers::getUser($this->left_id);
        
        if(empty($left_steam_user_data)) {
            Http::redirect('/');
        }
        
        $right_steam_user_data = SteamUsers::getUser($this->right_id);
        
        if(empty($right_user_data)) {
            Http::redirect('/');
        }
        
        $this->title = "{$left_steam_user_data['personaname']} vs. {$right_steam_user_data['personaname']}";
    
        $user_template = new TemplateElement("versus.php");
        
        //Twitch
        if(!empty($left_steam_user_data['twitch_username'])) {
            $user_template->addChild("<a href=\"http://www.twitch.tv/{$left_steam_user_data['twitch_username']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/twitch_small.png\" /></a>");
        }

        if(!empty($right_steam_user_data['twitch_username'])) {
            $user_template->addChild("<a href=\"http://www.twitch.tv/{$right_steam_user_data['twitch_username']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/twitch_small.png\" /></a>");
        }
        
        //Nico nico
        if(!empty($steam_user['nico_nico_url'])) {
            $user_template->addChild("<a href=\"{$left_steam_user_data['nico_nico_url']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/nico_nico_small.png\" /></a>");
        }
        
        $right_nico_nico_link = '';
        
        if(!empty($steam_user['nico_nico_url'])) {
            $user_template->addChild("<a href=\"{$right_steam_user_data['nico_nico_url']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/nico_nico_small.png\" /></a>");
        }
        
        //Hitbox
        if(!empty($steam_user['hitbox_username'])) {
            $user_template->addChild("<a href=\"http://www.hitbox.tv/{$left_steam_user_data['hitbox_username']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/hitboxicongreen_small.png\" /></a>");
        }
        
        if(!empty($steam_user['hitbox_username'])) {
            $user_template->addChild("<a href=\"http://www.hitbox.tv/{$right_steam_user_data['hitbox_username']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/hitboxicongreen_small.png\" /></a>");
        }
        
        //Twitter        
        if(!empty($steam_user['twitter_username'])) {
            $user_template->addChild("<a href=\"http://www.twitter.com/{$left_steam_user_data['twitter_username']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/twitter_logo_blue_small.png\" /></a>");
        }
        
        if(!empty($steam_user['twitter_username'])) {
            $user_template->addChild("<a href=\"http://www.twitter.com/{$right_website['twitter_username']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/twitter_logo_blue_small.png\" /></a>");
        }
        
        //Website
        if(!empty($steam_user['website'])) {
            $user_template->addChild("<a href=\"{$left_steam_user_data['website']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/external_link_small.png\" /></a>");
        }
        
        if(!empty($steam_user['website'])) {
            $user_template->addChild("<a href=\"{$left_steam_user_data['website']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/external_link_small.png\" /></a>");
        }
        
        $steam_user_table = new Table('steam_user_info');
        
        $steam_user_table->addRows(array(
            array(
                'Steam',
                "<a href={$steam_user['profileurl']}>{$steam_user['personaname']}</a>"
            ),
            array(
                'Twitch',
                $twitch_link
            ),
            array(
                'Nico Nico Link',
                $nico_nico_link
            ),
            array(
                'Hitbox',
                $hitbox_link
            ),
            array(
                'Twitter',
                $twitter_link
            ),
            array(
                'Website',
                $website
            )
        ));
        
        $user_template->addChild($steam_user_table, 'steam_user_table');
        
        $left_power_ranking = SteamUsers::getLatestPowerRanking($this->left_id);
        $right_power_ranking = SteamUsers::getLatestPowerRanking($this->right_id);

        if(!empty($power_ranking)) {
            $power_rankings_table = new Table('power_rankings');
            
            $power_rankings_table->setNumberofColumns(5);
            
            $power_rankings_table->addHeader(array(
                'type' => '&nbsp;',
                'score' => '<span>Score</span>',
                'speed' => '<span>Speed</span>',
                'deathless' => '<span>Deathless</span>',            
                'overall' => '<span>Overall</span>' 
            ));
            
            $power_rankings_table->addRow(array(
                'type' => 'Rank',
                'score' => $power_ranking['score_rank'],
                'speed' => $power_ranking['speed_rank'],         
                'deathless' => $power_ranking['deathless_score_rank'],
                'overall' => $power_ranking['rank']
            ));
            
            $power_rankings_table->addRow(array(
                'type' => 'Points',
                'score' => $this->roundNumber($power_ranking['score_rank_points_total']),
                'speed' => $this->roundNumber($power_ranking['speed_rank_points_total']),            
                'deathless' => $this->roundNumber($power_ranking['deathless_score_rank_points_total']),
                'overall' => $power_ranking['total_points']
            ));
            
            $user_template->addChild($power_rankings_table, 'power_rankings_table');
        }
        
        $character_placeholder_image = "{$this->page->getImagesHttpPath()}/character_placeholder.png";
        
        $score_ranking = SteamUsers::getLatestScoreRanking($this->right_id);
        
        if(!empty($score_ranking)) {
            $score_rankings_table = new Table('score_rankings');
            
            $score_rankings_table->setNumberofColumns(13);
            
            $score_rankings_table->addHeader(array(
                'type' => '&nbsp;',
                'cadence_score_rank' => "<img class=\"cadence_header\" src=\"{$character_placeholder_image}\" />",
                'bard_score_rank' => "<img class=\"bard_header\" src=\"{$character_placeholder_image}\" />",
                'monk_score_rank' => "<img class=\"monk_header\" src=\"{$character_placeholder_image}\" />",
                'aria_score_rank' => "<img class=\"aria_header\" src=\"{$character_placeholder_image}\" />",
                'bolt_score_rank' => "<img class=\"bolt_header\" src=\"{$character_placeholder_image}\" />",
                'dove_score_rank' => "<img class=\"dove_header\" src=\"{$character_placeholder_image}\" />",
                'eli_score_rank' => "<img class=\"eli_header\" src=\"{$character_placeholder_image}\" />",
                'melody_score_rank' => "<img class=\"melody_header\" src=\"{$character_placeholder_image}\" />",
                'dorian_score_rank' => "<img class=\"dorian_header\" src=\"{$character_placeholder_image}\" />",
                'all_score_rank' => "All Characters",
                'story_score_rank' => "Story Mode",
                'overall' => 'Overall'
            ));
            
            $score_rankings_table->addRow(array(
                'type' => 'Rank',
                'cadence_score_rank' => $score_ranking['cadence_score_rank'],
                'bard_score_rank' => $score_ranking['bard_score_rank'],
                'monk_score_rank' => $score_ranking['monk_score_rank'],
                'aria_score_rank' => $score_ranking['aria_score_rank'],
                'bolt_score_rank' => $score_ranking['bolt_score_rank'],
                'dove_score_rank' => $score_ranking['dove_score_rank'],
                'eli_score_rank' => $score_ranking['eli_score_rank'],
                'melody_score_rank' => $score_ranking['melody_score_rank'],
                'dorian_score_rank' => $score_ranking['dorian_score_rank'],
                'all_score_rank' => $score_ranking['all_score_rank'],
                'story_score_rank' => $score_ranking['story_score_rank'],
                'overall' => $score_ranking['score_rank']
            ));
            
            $score_rankings_table->addRow(array(
                'type' => 'Points',
                'cadence_score_rank' => $this->roundNumber($score_ranking['cadence_score_rank_points']),
                'bard_score_rank' => $this->roundNumber($score_ranking['bard_score_rank_points']),
                'monk_score_rank' => $this->roundNumber($score_ranking['monk_score_rank_points']),
                'aria_score_rank' => $this->roundNumber($score_ranking['aria_score_rank_points']),
                'bolt_score_rank' => $this->roundNumber($score_ranking['bolt_score_rank_points']),
                'dove_score_rank' => $this->roundNumber($score_ranking['dove_score_rank_points']),
                'eli_score_rank' => $this->roundNumber($score_ranking['eli_score_rank_points']),
                'melody_score_rank' => $this->roundNumber($score_ranking['melody_score_rank_points']),
                'dorian_score_rank' => $this->roundNumber($score_ranking['dorian_score_rank_points']),
                'all_score_rank' => $this->roundNumber($score_ranking['all_score_rank_points']),
                'story_score_rank' => $this->roundNumber($score_ranking['story_score_rank_points']),
                'overall' => $score_ranking['score_rank_points_total']
            ));
            
            $user_template->addChild($score_rankings_table, 'score_rankings_table');
        }
        
        $speed_ranking = SteamUsers::getLatestSpeedRanking($this->right_id);
        
        if(!empty($speed_ranking)) {
            $speed_rankings_table = new Table('speed_rankings');
            
            $speed_rankings_table->setNumberofColumns(13);
            
            $speed_rankings_table->addHeader(array(
                'type' => '&nbsp;',
                'cadence_speed_rank' => "<img class=\"cadence_header\" src=\"{$character_placeholder_image}\" />",
                'bard_speed_rank' => "<img class=\"bard_header\" src=\"{$character_placeholder_image}\" />",
                'monk_speed_rank' => "<img class=\"monk_header\" src=\"{$character_placeholder_image}\" />",
                'aria_speed_rank' => "<img class=\"aria_header\" src=\"{$character_placeholder_image}\" />",
                'bolt_speed_rank' => "<img class=\"bolt_header\" src=\"{$character_placeholder_image}\" />",
                'dove_speed_rank' => "<img class=\"dove_header\" src=\"{$character_placeholder_image}\" />",
                'eli_speed_rank' => "<img class=\"eli_header\" src=\"{$character_placeholder_image}\" />",
                'melody_speed_rank' => "<img class=\"melody_header\" src=\"{$character_placeholder_image}\" />",
                'dorian_speed_rank' => "<img class=\"dorian_header\" src=\"{$character_placeholder_image}\" />",
                'all_speed_rank' => "All Characters",
                'story_speed_rank' => "Story Mode",
                'overall' => 'Overall'
            ));
            
            $speed_rankings_table->addRow(array(
                'type' => 'Rank',
                'cadence_speed_rank' => $speed_ranking['cadence_speed_rank'],
                'bard_speed_rank' => $speed_ranking['bard_speed_rank'],
                'monk_speed_rank' => $speed_ranking['monk_speed_rank'],
                'aria_speed_rank' => $speed_ranking['aria_speed_rank'],
                'bolt_speed_rank' => $speed_ranking['bolt_speed_rank'],
                'dove_speed_rank' => $speed_ranking['dove_speed_rank'],
                'eli_speed_rank' => $speed_ranking['eli_speed_rank'],
                'melody_speed_rank' => $speed_ranking['melody_speed_rank'],
                'dorian_speed_rank' => $speed_ranking['dorian_speed_rank'],
                'all_speed_rank' => $speed_ranking['all_speed_rank'],
                'story_speed_rank' => $speed_ranking['story_speed_rank'],
                'overall' => $speed_ranking['speed_rank']
            ));
            
            $speed_rankings_table->addRow(array(
                'type' => 'Points',
                'cadence_speed_rank' => $this->roundNumber($speed_ranking['cadence_speed_rank_points']),
                'bard_speed_rank' => $this->roundNumber($speed_ranking['bard_speed_rank_points']),
                'monk_speed_rank' => $this->roundNumber($speed_ranking['monk_speed_rank_points']),
                'aria_speed_rank' => $this->roundNumber($speed_ranking['aria_speed_rank_points']),
                'bolt_speed_rank' => $this->roundNumber($speed_ranking['bolt_speed_rank_points']),
                'dove_speed_rank' => $this->roundNumber($speed_ranking['dove_speed_rank_points']),
                'eli_speed_rank' => $this->roundNumber($speed_ranking['eli_speed_rank_points']),
                'melody_speed_rank' => $this->roundNumber($speed_ranking['melody_speed_rank_points']),
                'dorian_speed_rank' => $this->roundNumber($speed_ranking['dorian_speed_rank_points']),
                'all_speed_rank' => $this->roundNumber($speed_ranking['all_speed_rank_points']),
                'story_speed_rank' => $this->roundNumber($speed_ranking['story_speed_rank_points']),
                'overall' => $speed_ranking['speed_rank_points_total']
            ));
            
            $user_template->addChild($speed_rankings_table, 'speed_rankings_table');
        }
        
        $deathless_score_ranking = SteamUsers::getLatestDeathlessScoreRanking($this->right_id);
        
        if(!empty($deathless_score_ranking)) {
            $deathless_score_rankings_table = new Table('deathless_score_rankings');
            
            $deathless_score_rankings_table->setNumberofColumns(11);
            
            $deathless_score_rankings_table->addHeader(array(
                'type' => '&nbsp;',
                'cadence_deathless_score_rank' => "<img class=\"cadence_header\" src=\"{$character_placeholder_image}\" />",
                'bard_deathless_score_rank' => "<img class=\"bard_header\" src=\"{$character_placeholder_image}\" />",
                'monk_deathless_score_rank' => "<img class=\"monk_header\" src=\"{$character_placeholder_image}\" />",
                'aria_deathless_score_rank' => "<img class=\"aria_header\" src=\"{$character_placeholder_image}\" />",
                'bolt_deathless_score_rank' => "<img class=\"bolt_header\" src=\"{$character_placeholder_image}\" />",
                'dove_deathless_score_rank' => "<img class=\"dove_header\" src=\"{$character_placeholder_image}\" />",
                'eli_deathless_score_rank' => "<img class=\"eli_header\" src=\"{$character_placeholder_image}\" />",
                'melody_deathless_score_rank' => "<img class=\"melody_header\" src=\"{$character_placeholder_image}\" />",
                'dorian_deathless_score_rank' => "<img class=\"dorian_header\" src=\"{$character_placeholder_image}\" />",
                'overall' => 'Overall'
            ));
            
            $deathless_score_rankings_table->addRow(array(
                'type' => 'Rank',
                'cadence_deathless_score_rank' => $deathless_score_ranking['cadence_deathless_score_rank'],
                'bard_deathless_score_rank' => $deathless_score_ranking['bard_deathless_score_rank'],
                'monk_deathless_score_rank' => $deathless_score_ranking['monk_deathless_score_rank'],
                'aria_deathless_score_rank' => $deathless_score_ranking['aria_deathless_score_rank'],
                'bolt_deathless_score_rank' => $deathless_score_ranking['bolt_deathless_score_rank'],
                'dove_deathless_score_rank' => $deathless_score_ranking['dove_deathless_score_rank'],
                'eli_deathless_score_rank' => $deathless_score_ranking['eli_deathless_score_rank'],
                'melody_deathless_score_rank' => $deathless_score_ranking['melody_deathless_score_rank'],
                'dorian_deathless_score_rank' => $deathless_score_ranking['dorian_deathless_score_rank'],
                'overall' => $deathless_score_ranking['deathless_score_rank']
            ));
            
            $deathless_score_rankings_table->addRow(array(
                'type' => 'Points',
                'cadence_deathless_score_rank' => $this->roundNumber($deathless_score_ranking['cadence_deathless_score_rank_points']),
                'bard_deathless_score_rank' => $this->roundNumber($deathless_score_ranking['bard_deathless_score_rank_points']),
                'monk_deathless_score_rank' => $this->roundNumber($deathless_score_ranking['monk_deathless_score_rank_points']),
                'aria_deathless_score_rank' => $this->roundNumber($deathless_score_ranking['aria_deathless_score_rank_points']),
                'bolt_deathless_score_rank' => $this->roundNumber($deathless_score_ranking['bolt_deathless_score_rank_points']),
                'dove_deathless_score_rank' => $this->roundNumber($deathless_score_ranking['dove_deathless_score_rank_points']),
                'eli_deathless_score_rank' => $this->roundNumber($deathless_score_ranking['eli_deathless_score_rank_points']),
                'melody_deathless_score_rank' => $this->roundNumber($deathless_score_ranking['melody_deathless_score_rank_points']),
                'dorian_deathless_score_rank' => $this->roundNumber($deathless_score_ranking['dorian_deathless_score_rank_points']),
                'overall' => $deathless_score_ranking['deathless_score_rank_points_total']
            ));
            
            $user_template->addChild($deathless_score_rankings_table, 'deathless_score_rankings_table');
        }
        
        $daily_ranking = SteamUsers::getLatestDailyRanking($this->right_id);
        
        if(!empty($daily_ranking)) {
            $images_http_path = $this->page->getImagesHttpPath();
        
            $daily_ranking_table = new Table("daily_rankings", false);
        
            $daily_ranking_table->setNumberofColumns(12);
            
            $daily_ranking_table->addHeader(array(
                'rank' => 'Rank',  
                'first_place_ranks' => "<img src=\"{$images_http_path}/sort-1st.png\" />",
                'top_5_ranks' => "<img src=\"{$images_http_path}/sort-top5.png\" />",
                'top_10_ranks' => "<img src=\"{$images_http_path}/sort-top10.png\" />",
                'top_20_ranks' => "<img src=\"{$images_http_path}/sort-top20.png\" />",
                'top_50_ranks' => "<img src=\"{$images_http_path}/sort-top50.png\" />",
                'top_100_ranks' => "<img src=\"{$images_http_path}/sort-top100.png\" />",
                'total_points' => "<img src=\"{$images_http_path}/sort-total.png\" />",
                'points_per_day' => "<img src=\"{$images_http_path}/sort-avg.png\" />",
                'total_dailies' => "<img src=\"{$images_http_path}/sort-totaldailies.png\" />",
                'total_wins' => "<img src=\"{$images_http_path}/sort-totalwins.png\" />",
                'average_place' => "<img src=\"{$images_http_path}/sort-avgplace.png\" />"
            ));
            
            $daily_ranking_table->addRow(array(
                'rank' => $daily_ranking['rank'],  
                'first_place_ranks' => $daily_ranking['first_place_ranks'],
                'top_5_ranks' => $daily_ranking['top_5_ranks'],
                'top_10_ranks' => $daily_ranking['top_10_ranks'],
                'top_20_ranks' => $daily_ranking['top_20_ranks'],
                'top_50_ranks' => $daily_ranking['top_50_ranks'],
                'top_100_ranks' => $daily_ranking['top_100_ranks'],
                'total_points' => $daily_ranking['total_points'],
                'points_per_day' => $daily_ranking['points_per_day'],
                'total_dailies' => $daily_ranking['total_dailies'],
                'total_wins' => $daily_ranking['total_wins'],
                'average_place' => $daily_ranking['average_place']
            ));
            
            $user_template->addChild($daily_ranking_table, 'daily_ranking_table');
        }

        $this->page->body->addChild($user_template, 'content');
    }
}