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

use \Framework\Core\Loader;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Form\Form;
use \Framework\Html\Form\TableForm;
use \Framework\Html\Table\Table;
use \Modules\Necrolab\Models\SteamUsers;
use \LightOpenID;

class Player
extends Necrolab {    
    protected $steam_user_id;

    public function __construct() {
        parent::__construct();
        
        $this->active_page = 'players';
        
        $this->steam_user_id = request()->steam_user_id;
    }
    
    protected function loadModule() {
        parent::loadModule();
        
        $this->page->addCssFiles(array(
            'page/player.css'
        ));
    }
    
    protected function constructContent() {
        if(empty($this->steam_user_id)) {
            Http::redirect('/');
        }
        
        $steam_user_data = SteamUsers::getUser($this->steam_user_id);
        
        if(empty($steam_user_data)) {
            Http::redirect('/');
        }
        
        $this->title = "Profile for {$steam_user_data['personaname']}";
    
        $user_template = new TemplateElement("user.php");
        
        Loader::load('openid.php');
            
        $open_id = new LightOpenID(Http::getBaseUrl());
        
        $open_id_mode = $open_id->mode;
        
        if(!empty($open_id_mode) && $open_id_mode != 'cancel' && !empty($open_id->validate())) {
            $steam_user_url = $open_id->identity;
            
            $steam_id = str_replace('http://steamcommunity.com/openid/id/', '', $steam_user_url);
            
            $steam_user_id = db()->getOne("
                SELECT steam_user_id
                FROM steam_users
                WHERE steamid = ?
            ", array(
                $steam_id
            ));
            
            if(!empty($steam_user_id)) {
                session()->steam_user_id = $steam_user_id;
            }
        }
        
        if(isset(session()->steam_user_id) && $this->steam_user_id == session()->steam_user_id) {
            $authenticated_user_form = new TableForm('authenticated_user_form');
            $authenticated_user_form->disableJavascript();
            
            //$authenticated_user_form->addReadOnly('steam_username', 'Steam', "<a href=\"{$steam_user_data['profileurl']}\">{$steam_user_data['personaname']}</a>");
            $authenticated_user_form->addTextBox('twitch_username', 'Twitch Channel', $steam_user_data['twitch_username']);
            $authenticated_user_form->addTextBox('nico_nico_url', 'Nico Nico Url', $steam_user_data['nico_nico_url']);
            $authenticated_user_form->addTextBox('hitbox_username', 'Hitbox Channel', $steam_user_data['hitbox_username']);
            $authenticated_user_form->addTextBox('twitter_username', 'Twitter User', $steam_user_data['twitter_username']);
            $authenticated_user_form->addTextBox('website_url', 'Website', $steam_user_data['website']);
            $authenticated_user_form->addSubmit('submit', 'Save');
            
            if($authenticated_user_form->wasSubmitted() && $authenticated_user_form->isValid()) {
                $form_data = $authenticated_user_form->getData();
                
                SteamUsers::saveSocialMediaData($this->steam_user_id, $form_data['twitch_username'], $steam_user_data['nico_nico_url'], $steam_user_data['hitbox_username'], $form_data['twitter_username'], $form_data['website_url']);
                
                $authenticated_user_form->addConfirmation('Your information has been updated.');
            }
            
            $user_template->addChild($authenticated_user_form, 'steam_user_form');
        }
        else {
            unset(session()->steam_user_id);
            
            $steam_login_form = new Form('steam_login', '', 'post', false);
            $steam_login_form->disableJavascript();
            $steam_login_form->addImageButton('login_button', "{$this->page->getImagesHttpPath()}/steam_login_small.png");
            
            if($steam_login_form->wasSubmitted() && $steam_login_form->isValid()) {
                $open_id->identity = 'https://steamcommunity.com/openid';

                Http::redirect($open_id->authUrl());
            }
            
            $twitch_link = '';
            
            if(!empty($steam_user_data['twitch_username'])) {
                $twitch_link = "<a href=\"http://www.twitch.tv/{$steam_user_data['twitch_username']}\" target=\"_blank\">{$steam_user_data['twitch_username']}</a>";
            }
            
            $nico_nico_link = '';
            
            if(!empty($steam_user_data['nico_nico_url'])) {
                $nico_nico_link = "<a href=\"{$steam_user_data['nico_nico_url']}\" target=\"_blank\">Link</a>";
            }
            
            $hitbox_link = '';
            
            if(!empty($steam_user_data['hitbox_username'])) {
                $hitbox_link = "<a href=\"http://www.hitbox.tv/{$steam_user_data['hitbox_username']}\" target=\"_blank\">{$steam_user_data['hitbox_username']}</a>";
            }
            
            $twitter_link = '';
            
            if(!empty($steam_user_data['twitter_username'])) {
                $twitter_link = "<a href=\"http://www.twitter.com/{$steam_user_data['twitter_username']}\" target=\"_blank\">{$steam_user_data['twitter_username']}</a>";
            }
            
            $website = '';
            
            if(!empty($steam_user_data['website'])) {
                $website = "<a href=\"{$steam_user_data['website']}\" target=\"_blank\">Link</a>";
            }
            
            $user_template->addChild($steam_login_form, 'steam_login_form');
            
            $steam_user_table = new Table('steam_user_info');
            
            $steam_user_table->addRows(array(
                array(
                    'Steam',
                    "<a href={$steam_user_data['profileurl']}>{$steam_user_data['personaname']}</a>"
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
        }
        
        $power_ranking = SteamUsers::getLatestPowerRanking($this->steam_user_id);

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
        
        $score_ranking = SteamUsers::getLatestScoreRanking($this->steam_user_id);
        
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
                'coda_score_rank' => "<img class=\"coda_header\" src=\"{$character_placeholder_image}\" />",
                'all_score_rank' => "All<br />Chars",
                'story_score_rank' => "Story<br />Mode",
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
                'coda_score_rank' => $score_ranking['coda_score_rank'],
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
                'coda_score_rank' => $this->roundNumber($score_ranking['coda_score_rank_points']),
                'all_score_rank' => $this->roundNumber($score_ranking['all_score_rank_points']),
                'story_score_rank' => $this->roundNumber($score_ranking['story_score_rank_points']),
                'overall' => $score_ranking['score_rank_points_total']
            ));
            
            $user_template->addChild($score_rankings_table, 'score_rankings_table');
        }
        
        $speed_ranking = SteamUsers::getLatestSpeedRanking($this->steam_user_id);
        
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
                'coda_speed_rank' => "<img class=\"coda_header\" src=\"{$character_placeholder_image}\" />",
                'all_speed_rank' => "All<br />Chars",
                'story_speed_rank' => "Story<br />Mode",
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
                'coda_speed_rank' => $speed_ranking['coda_speed_rank'],
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
                'coda_speed_rank' => $this->roundNumber($speed_ranking['coda_speed_rank_points']),
                'all_speed_rank' => $this->roundNumber($speed_ranking['all_speed_rank_points']),
                'story_speed_rank' => $this->roundNumber($speed_ranking['story_speed_rank_points']),
                'overall' => $speed_ranking['speed_rank_points_total']
            ));
            
            $user_template->addChild($speed_rankings_table, 'speed_rankings_table');
        }
        
        $deathless_score_ranking = SteamUsers::getLatestDeathlessScoreRanking($this->steam_user_id);
        
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
                'coda_deathless_score_rank' => "<img class=\"coda_header\" src=\"{$character_placeholder_image}\" />",
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
                'coda_deathless_score_rank' => $deathless_score_ranking['coda_deathless_score_rank'],
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
                'coda_deathless_score_rank' => $this->roundNumber($deathless_score_ranking['coda_deathless_score_rank_points']),
                'overall' => $deathless_score_ranking['deathless_score_rank_points_total']
            ));
            
            $user_template->addChild($deathless_score_rankings_table, 'deathless_score_rankings_table');
        }
        
        $daily_ranking = SteamUsers::getLatestDailyRanking($this->steam_user_id);
        
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
    
    public function submit() {
        $form = $this->getForm();
        
        return $form->toJsonArray();
    }
    
    public function apiGetProfile() {
        if(empty($this->steam_user_id)) {
            throw new \Exception("steam_user_id cannot be empty.");
        }
        
        $steam_user_data = SteamUsers::getUser($this->steam_user_id);
        
        return array(
            'steam_id' => $steam_user_data['steamid'],
            'steam_username' => $steam_user_data['personaname'],
            'necrolab_player_profile_id' => $steam_user_data['steam_user_id'],
            'twitch_username' => $steam_user_data['twitch_username'],
            'twitter_username' => $steam_user_data['twitter_username'],
            'website' => $steam_user_data['website']
        );
    }
    
    public function apiGetRankings() {
        
    }
}