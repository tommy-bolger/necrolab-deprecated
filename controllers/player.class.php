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
        
        $steam_user = db()->getRow("
            SELECT *
            FROM steam_users
            WHERE steam_user_id = ?
        ", array(
            $this->steam_user_id
        ));
        
        if(isset(session()->steam_user_id) && $this->steam_user_id == session()->steam_user_id) {
            $authenticated_user_form = new TableForm('authenticated_user_form');
            $authenticated_user_form->disableJavascript();
            
            //$authenticated_user_form->addReadOnly('steam_username', 'Steam', "<a href=\"{$steam_user['profileurl']}\">{$steam_user['personaname']}</a>");
            $authenticated_user_form->addTextBox('twitch_username', 'Twitch', $steam_user['twitch_username']);
            $authenticated_user_form->addTextBox('twitter_username', 'Twitter', $steam_user['twitter_username']);
            $authenticated_user_form->addTextBox('website_url', 'Website', $steam_user['website']);
            $authenticated_user_form->addSubmit('submit', 'Save');
            
            if($authenticated_user_form->wasSubmitted() && $authenticated_user_form->isValid()) {
                $form_data = $authenticated_user_form->getData();
                
                SteamUsers::saveSocialMediaData($this->steam_user_id, $form_data['twitch_username'], $form_data['twitter_username'], $form_data['website_url']);
                
                $authenticated_user_form->addConfirmation('Your information has been updated.');
            }
            
            $user_template->addChild($authenticated_user_form, 'steam_user_form');
        }
        else {
            session()->end();
            
            $steam_login_form = new Form('steam_login', '', 'post', false);
            $steam_login_form->disableJavascript();
            $steam_login_form->addImageButton('login_button', "{$this->page->getImagesHttpPath()}/steam_login_small.png");
            
            if($steam_login_form->wasSubmitted() && $steam_login_form->isValid()) {
                $open_id->identity = 'https://steamcommunity.com/openid';

                Http::redirect($open_id->authUrl());
            }
            
            $website = '';
            
            if(!empty($steam_user['website'])) {
                $website = "<a href=\"{$steam_user['website']}\" target=\"_blank\">Link</a>";
            }
            
            $user_template->addChild($steam_login_form, 'steam_login_form');
            
            $steam_user_table = new Table('steam_user_info');
            
            $steam_user_table->addRows(array(
                array(
                    'Steam',
                    "<a href={$steam_user['profileurl']}>{$steam_user['personaname']}</a>"
                ),
                array(
                    'Twitch',
                    "<a href=\"http://www.twitch.tv/{$steam_user['twitch_username']}\" target=\"_blank\">{$steam_user['twitch_username']}</a>"
                ),
                array(
                    'Twitter',
                    "<a href=\"http://www.twitch.tv/{$steam_user['twitter_username']}\" target=\"_blank\">{$steam_user['twitter_username']}</a>"
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
            
            $power_rankings_table->setNumberofColumns(8);
            
            $power_rankings_table->addHeader(array(
                'rank' => array(
                    'contents' => "&nbsp;"
                ),
                'score' => array(
                    'contents' => "<div class=\"center menu_small\">Score</div>",
                    'colspan' => 2,
                ),
                'speed' => array(
                    'contents' => "<div class=\"center menu_small\">Speed</div>",
                    'colspan' => 2,
                ),
                'deathless_score' => array(
                    'contents' => "<div class=\"center menu_small\">Deathless</div>",
                    'colspan' => 2,
                ),
                'total' => array(
                    'contents' => "&nbsp;"
                )
            ));
            
            $power_rankings_table->addHeader(array(
                'rank' => 'Rank',
                'score_rank' => '<span>Rank</span>',
                'score_rank_points_total' => '<span>Points</span>',
                'speed_rank' => '<span>Rank</span>',
                'speed_rank_points_total' => '<span>Points</span>',            
                'deathless_score_rank' => '<span>Rank</span>',
                'deathless_score_rank_points_total' => '<span>Points</span>',
                'total_points' => '<span>Total Points</span>' 
            ));
            
            $power_rankings_table->addRow(array(
                'rank' => $power_ranking['rank'],
                'score_rank' => $power_ranking['score_rank'],
                'score_rank_points_total' => $power_ranking['score_rank_points_total'],
                'speed_rank' => $power_ranking['speed_rank'],
                'speed_rank_points_total' => $power_ranking['speed_rank_points_total'],            
                'deathless_score_rank' => $power_ranking['deathless_score_rank'],
                'deathless_score_rank_points_total' => $power_ranking['deathless_score_rank_points_total'],
                'total_points' => $power_ranking['total_points']
            ));
            
            $user_template->addChild($power_rankings_table, 'power_rankings_table');
        }
        
        $character_placeholder_image = "{$this->page->getImagesHttpPath()}/character_placeholder.png";
        
        $score_ranking = SteamUsers::getLatestScoreRanking($this->steam_user_id);
        
        if(!empty($score_ranking)) {
            $score_rankings_table = new Table('score_rankings');
            
            $score_rankings_table->setNumberofColumns(13);
            
            $score_rankings_table->addHeader(array(
                'score_rank' => 'Rank',
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
                'score_rank_points_total' => 'Total Points'
            ));
            
            $score_rankings_table->addRow(array(
                'score_rank' => $score_ranking['score_rank'],
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
                'score_rank_points_total' => $score_ranking['score_rank_points_total']
            ));
            
            $user_template->addChild($score_rankings_table, 'score_rankings_table');
        }
        
        $speed_ranking = SteamUsers::getLatestSpeedRanking($this->steam_user_id);
        
        if(!empty($speed_ranking)) {
            $speed_rankings_table = new Table('speed_rankings');
            
            $speed_rankings_table->setNumberofColumns(13);
            
            $speed_rankings_table->addHeader(array(
                'speed_rank' => 'Rank',
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
                'speed_rank_points_total' => 'Total Points'
            ));
            
            $speed_rankings_table->addRow(array(
                'speed_rank' => $speed_ranking['speed_rank'],
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
                'speed_rank_points_total' => $speed_ranking['speed_rank_points_total']
            ));
            
            $user_template->addChild($speed_rankings_table, 'speed_rankings_table');
        }
        
        $deathless_score_ranking = SteamUsers::getLatestDeathlessScoreRanking($this->steam_user_id);
        
        if(!empty($deathless_score_ranking)) {
            $deathless_score_rankings_table = new Table('deathless_score_rankings');
            
            $deathless_score_rankings_table->setNumberofColumns(11);
            
            $deathless_score_rankings_table->addHeader(array(
                'deathless_score_rank' => 'Rank',
                'cadence_deathless_score_rank' => "<img class=\"cadence_header\" src=\"{$character_placeholder_image}\" />",
                'bard_deathless_score_rank' => "<img class=\"bard_header\" src=\"{$character_placeholder_image}\" />",
                'monk_deathless_score_rank' => "<img class=\"monk_header\" src=\"{$character_placeholder_image}\" />",
                'aria_deathless_score_rank' => "<img class=\"aria_header\" src=\"{$character_placeholder_image}\" />",
                'bolt_deathless_score_rank' => "<img class=\"bolt_header\" src=\"{$character_placeholder_image}\" />",
                'dove_deathless_score_rank' => "<img class=\"dove_header\" src=\"{$character_placeholder_image}\" />",
                'eli_deathless_score_rank' => "<img class=\"eli_header\" src=\"{$character_placeholder_image}\" />",
                'melody_deathless_score_rank' => "<img class=\"melody_header\" src=\"{$character_placeholder_image}\" />",
                'dorian_deathless_score_rank' => "<img class=\"dorian_header\" src=\"{$character_placeholder_image}\" />",
                'deathless_score_rank_points_total' => 'Total Points'
            ));
            
            $deathless_score_rankings_table->addRow(array(
                'deathless_score_rank' => $deathless_score_ranking['deathless_score_rank'],
                'cadence_deathless_score_rank' => $deathless_score_ranking['cadence_deathless_score_rank'],
                'bard_deathless_score_rank' => $deathless_score_ranking['bard_deathless_score_rank'],
                'monk_deathless_score_rank' => $deathless_score_ranking['monk_deathless_score_rank'],
                'aria_deathless_score_rank' => $deathless_score_ranking['aria_deathless_score_rank'],
                'bolt_deathless_score_rank' => $deathless_score_ranking['bolt_deathless_score_rank'],
                'dove_deathless_score_rank' => $deathless_score_ranking['dove_deathless_score_rank'],
                'eli_deathless_score_rank' => $deathless_score_ranking['eli_deathless_score_rank'],
                'melody_deathless_score_rank' => $deathless_score_ranking['melody_deathless_score_rank'],
                'dorian_deathless_score_rank' => $deathless_score_ranking['dorian_deathless_score_rank'],
                'deathless_score_rank_points_total' => $deathless_score_ranking['deathless_score_rank_points_total']
            ));
            
            $user_template->addChild($deathless_score_rankings_table, 'deathless_score_rankings_table');
        }

        $this->page->body->addChild($user_template, 'content');
    }
    
    public function submit() {
        $form = $this->getForm();
        
        return $form->toJsonArray();
    }
    
    public function getPowerRankingData() {
        
    }
    
    public function getDailyRankingData() {
        
    }
}