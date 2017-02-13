<?php
/**
* The user stats page.
* Copyright (c) 2017, Tommy Bolger
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
namespace Modules\Necrolab\Controllers\Page\Players\Player;

use \Exception;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Form\Form;
use \Framework\Html\Form\TableForm;
use \Framework\Html\Table\Table;
use \Modules\Necrolab\Controllers\Page\Players\Players;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as SteamUsersModel;
use \Modules\Necrolab\Models\Leaderboards;
use \Modules\Necrolab\Models\DailyRankingSeasons;
use \Modules\Necrolab\Objects\PowerRankingEntry;
use \LightOpenID;

class Player
extends Players {    
    protected $steamid;
    
    protected $steam_user_record = array();
    
    protected function setSteamId() {
        $this->steamid = request()->get->getVariable('id', 'integer');
    }
    
    public function init() {
        $this->active_page = 'players';
        
        $this->setSteamId();
        
        if(empty($this->steamid)) {
            Http::redirect('/players/');
        }
        
        $this->steam_user_record = SteamUsersModel::get($this->steamid);
        
        if(empty($this->steam_user_record)) {
            Http::redirect('/players/');
        }
    }
    
    public function setup() {
        $this->title = "Profile for {$this->steam_user_record['personaname']}";
    
        parent::setup();
        
        $this->page->addCssFiles(array(
            'page/player.css',
            'characters_header.css'
        ));
    }
    
    public function actionGet() {     
        $user_template = new TemplateElement("player.php");

        if(isset(session()->steamid) && $this->steamid == session()->steamid) {
            $authenticated_user_form = new TableForm('authenticated_user_form');
            $authenticated_user_form->disableJavascript();
            
            //$authenticated_user_form->addReadOnly('steam_username', 'Steam', "<a href=\"{$this->steam_user_record['profileurl']}\">{$this->steam_user_record['personaname']}</a>");
            $authenticated_user_form->addTextBox('twitch_username', 'Twitch Channel', $this->steam_user_record['twitch_username']);
            $authenticated_user_form->addTextBox('nico_nico_url', 'Nico Nico Url', $this->steam_user_record['nico_nico_url']);
            $authenticated_user_form->addTextBox('hitbox_username', 'Hitbox Channel', $this->steam_user_record['hitbox_username']);
            $authenticated_user_form->addTextBox('twitter_username', 'Twitter User', $this->steam_user_record['twitter_username']);
            $authenticated_user_form->addTextBox('website_url', 'Website', $this->steam_user_record['website']);
            $authenticated_user_form->addSubmit('submit', 'Save');
            
            if($authenticated_user_form->wasSubmitted() && $authenticated_user_form->isValid()) {
                $form_data = $authenticated_user_form->getData();
                
                SteamUsers::saveSocialMediaData($this->steamid, $form_data['twitch_username'], $form_data['nico_nico_url'], $form_data['hitbox_username'], $form_data['twitter_username'], $form_data['website_url']);
                
                $authenticated_user_form->addConfirmation('Your information has been updated.');
            }
            
            $user_template->addChild($authenticated_user_form, 'steam_user_form');
        }
        else {            
            $steam_login_form = new Form('steam_login', Http::generateUrl('/players/player/login/steam', array(
                'id' => $this->steamid
            )), 'post', false);
            $steam_login_form->disableJavascript();
            $steam_login_form->addHidden('id', $this->steamid);
            $steam_login_form->addImageButton('login_button', "{$this->page->getImagesHttpPath()}/steam_login_small.png");
            
            $twitch_link = '';
            
            if(!empty($this->steam_user_record['twitch_username'])) {
                $twitch_link = "<a href=\"http://www.twitch.tv/{$this->steam_user_record['twitch_username']}\" target=\"_blank\">{$this->steam_user_record['twitch_username']}</a>";
            }
            
            $nico_nico_link = '';
            
            if(!empty($this->steam_user_record['nico_nico_url'])) {
                $nico_nico_link = "<a href=\"{$this->steam_user_record['nico_nico_url']}\" target=\"_blank\">Link</a>";
            }
            
            $hitbox_link = '';
            
            if(!empty($this->steam_user_record['hitbox_username'])) {
                $hitbox_link = "<a href=\"http://www.hitbox.tv/{$this->steam_user_record['hitbox_username']}\" target=\"_blank\">{$this->steam_user_record['hitbox_username']}</a>";
            }
            
            $twitter_link = '';
            
            if(!empty($this->steam_user_record['twitter_username'])) {
                $twitter_link = "<a href=\"http://www.twitter.com/{$this->steam_user_record['twitter_username']}\" target=\"_blank\">{$this->steam_user_record['twitter_username']}</a>";
            }
            
            $website = '';
            
            if(!empty($this->steam_user_record['website'])) {
                $website = "<a href=\"{$this->steam_user_record['website']}\" target=\"_blank\">Link</a>";
            }
            
            $user_template->addChild($steam_login_form, 'steam_login_form');
            
            $steam_user_table = new Table('steam_user_info');
            
            $steam_user_table->addRows(array(
                /*array(
                    'Steam',
                    "<a href={$this->steam_user_record['profileurl']}>{$steam_user_data['personaname']}</a>"
                ),*/
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
        
        /*$power_ranking = SteamUsers::getLatestPowerRankingFromCache($this->steamid);
        
        $power_ranking_record = new PowerRankingEntry();

        if(!empty($power_ranking)) {
            $power_ranking_record->setPropertiesFromArray($power_ranking);
        
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
                'score' => $power_ranking_record->score_rank,
                'speed' => $power_ranking_record->speed_rank,
                'deathless' => $power_ranking_record->deathless_score_rank,
                'overall' => $power_ranking_record->rank
            ));
            
            $power_rankings_table->addRow(array(
                'type' => 'Points',
                'score' => $this->roundNumber($power_ranking_record->score_rank_points_total),
                'speed' => $this->roundNumber($power_ranking_record->speed_rank_points_total),
                'deathless' => $this->roundNumber($power_ranking_record->deathless_score_rank_points_total),
                'overall' => $this->roundNumber($power_ranking_record->total_points)
            ));
            
            $user_template->addChild($power_rankings_table, 'power_rankings_table');
        }
        
        $character_placeholder_image = "{$this->page->getImagesHttpPath()}/character_placeholder.png";
        
        if(!empty($power_ranking_record->score_rank)) {
            $score_rankings_table = new Table('score_rankings');
            
            $score_rankings_table->setNumberofColumns(14);
            
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
                'cadence_score_rank' => $power_ranking_record->cadence_score_rank,
                'bard_score_rank' => $power_ranking_record->bard_score_rank,
                'monk_score_rank' => $power_ranking_record->monk_score_rank,
                'aria_score_rank' => $power_ranking_record->aria_score_rank,
                'bolt_score_rank' => $power_ranking_record->bolt_score_rank,
                'dove_score_rank' => $power_ranking_record->dove_score_rank,
                'eli_score_rank' => $power_ranking_record->eli_score_rank,
                'melody_score_rank' => $power_ranking_record->melody_score_rank,
                'dorian_score_rank' => $power_ranking_record->dorian_score_rank,
                'coda_score_rank' => $power_ranking_record->coda_score_rank,
                'all_score_rank' => $power_ranking_record->all_score_rank,
                'story_score_rank' => $power_ranking_record->story_score_rank,
                'overall' => $power_ranking_record->score_rank
            ));
            
            $score_rankings_table->addRow(array(
                'type' => 'Points',
                'cadence_score_rank' => $this->roundNumber($power_ranking_record->cadence_score_rank_points),
                'bard_score_rank' => $this->roundNumber($power_ranking_record->bard_score_rank_points),
                'monk_score_rank' => $this->roundNumber($power_ranking_record->monk_score_rank_points),
                'aria_score_rank' => $this->roundNumber($power_ranking_record->aria_score_rank_points),
                'bolt_score_rank' => $this->roundNumber($power_ranking_record->bolt_score_rank_points),
                'dove_score_rank' => $this->roundNumber($power_ranking_record->dove_score_rank_points),
                'eli_score_rank' => $this->roundNumber($power_ranking_record->eli_score_rank_points),
                'melody_score_rank' => $this->roundNumber($power_ranking_record->melody_score_rank_points),
                'dorian_score_rank' => $this->roundNumber($power_ranking_record->dorian_score_rank_points),
                'coda_score_rank' => $this->roundNumber($power_ranking_record->coda_score_rank_points),
                'all_score_rank' => $this->roundNumber($power_ranking_record->all_score_rank_points),
                'story_score_rank' => $this->roundNumber($power_ranking_record->story_score_rank_points),
                'overall' => $this->roundNumber($power_ranking_record->score_rank_points_total)
            ));
            
            $user_template->addChild($score_rankings_table, 'score_rankings_table');
        }
        
        if(!empty($power_ranking_record->speed_rank)) {
            $speed_rankings_table = new Table('speed_rankings');
            
            $speed_rankings_table->setNumberofColumns(14);
            
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
                'cadence_speed_rank' => $power_ranking_record->cadence_speed_rank,
                'bard_speed_rank' => $power_ranking_record->bard_speed_rank,
                'monk_speed_rank' => $power_ranking_record->monk_speed_rank,
                'aria_speed_rank' => $power_ranking_record->aria_speed_rank,
                'bolt_speed_rank' => $power_ranking_record->bolt_speed_rank,
                'dove_speed_rank' => $power_ranking_record->dove_speed_rank,
                'eli_speed_rank' => $power_ranking_record->eli_speed_rank,
                'melody_speed_rank' => $power_ranking_record->melody_speed_rank,
                'dorian_speed_rank' => $power_ranking_record->dorian_speed_rank,
                'coda_speed_rank' => $power_ranking_record->coda_speed_rank,
                'all_speed_rank' => $power_ranking_record->all_speed_rank,
                'story_speed_rank' => $power_ranking_record->story_speed_rank,
                'overall' => $power_ranking_record->speed_rank
            ));
            
            $speed_rankings_table->addRow(array(
                'type' => 'Points',
                'cadence_speed_rank' => $this->roundNumber($power_ranking_record->cadence_speed_rank_points),
                'bard_speed_rank' => $this->roundNumber($power_ranking_record->bard_speed_rank_points),
                'monk_speed_rank' => $this->roundNumber($power_ranking_record->monk_speed_rank_points),
                'aria_speed_rank' => $this->roundNumber($power_ranking_record->aria_speed_rank_points),
                'bolt_speed_rank' => $this->roundNumber($power_ranking_record->bolt_speed_rank_points),
                'dove_speed_rank' => $this->roundNumber($power_ranking_record->dove_speed_rank_points),
                'eli_speed_rank' => $this->roundNumber($power_ranking_record->eli_speed_rank_points),
                'melody_speed_rank' => $this->roundNumber($power_ranking_record->melody_speed_rank_points),
                'dorian_speed_rank' => $this->roundNumber($power_ranking_record->dorian_speed_rank_points),
                'coda_speed_rank' => $this->roundNumber($power_ranking_record->coda_speed_rank_points),
                'all_speed_rank' => $this->roundNumber($power_ranking_record->all_speed_rank_points),
                'story_speed_rank' => $this->roundNumber($power_ranking_record->story_speed_rank_points),
                'overall' => $this->roundNumber($power_ranking_record->speed_rank_points_total)
            ));
            
            $user_template->addChild($speed_rankings_table, 'speed_rankings_table');
        }
        
        if(!empty($power_ranking_record->deathless_score_rank)) {
            $deathless_score_rankings_table = new Table('deathless_score_rankings');
            
            $deathless_score_rankings_table->setNumberofColumns(12);
            
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
                'cadence_deathless_score_rank' => $power_ranking_record->cadence_deathless_score_rank,
                'bard_deathless_score_rank' => $power_ranking_record->bard_deathless_score_rank,
                'monk_deathless_score_rank' => $power_ranking_record->monk_deathless_score_rank,
                'aria_deathless_score_rank' => $power_ranking_record->aria_deathless_score_rank,
                'bolt_deathless_score_rank' => $power_ranking_record->bolt_deathless_score_rank,
                'dove_deathless_score_rank' => $power_ranking_record->dove_deathless_score_rank,
                'eli_deathless_score_rank' => $power_ranking_record->eli_deathless_score_rank,
                'melody_deathless_score_rank' => $power_ranking_record->melody_deathless_score_rank,
                'dorian_deathless_score_rank' => $power_ranking_record->dorian_deathless_score_rank,
                'coda_deathless_score_rank' => $power_ranking_record->coda_deathless_score_rank,
                'overall' => $power_ranking_record->deathless_score_rank
            ));
            
            $deathless_score_rankings_table->addRow(array(
                'type' => 'Points',
                'cadence_deathless_score_rank' => $this->roundNumber($power_ranking_record->cadence_deathless_score_rank_points),
                'bard_deathless_score_rank' => $this->roundNumber($power_ranking_record->bard_deathless_score_rank_points),
                'monk_deathless_score_rank' => $this->roundNumber($power_ranking_record->monk_deathless_score_rank_points),
                'aria_deathless_score_rank' => $this->roundNumber($power_ranking_record->aria_deathless_score_rank_points),
                'bolt_deathless_score_rank' => $this->roundNumber($power_ranking_record->bolt_deathless_score_rank_points),
                'dove_deathless_score_rank' => $this->roundNumber($power_ranking_record->dove_deathless_score_rank_points),
                'eli_deathless_score_rank' => $this->roundNumber($power_ranking_record->eli_deathless_score_rank_points),
                'melody_deathless_score_rank' => $this->roundNumber($power_ranking_record->melody_deathless_score_rank_points),
                'dorian_deathless_score_rank' => $this->roundNumber($power_ranking_record->dorian_deathless_score_rank_points),
                'coda_deathless_score_rank' => $this->roundNumber($power_ranking_record->coda_deathless_score_rank_points),
                'overall' => $this->roundNumber($power_ranking_record->deathless_score_rank_points_total)
            ));
            
            $user_template->addChild($deathless_score_rankings_table, 'deathless_score_rankings_table');
        }
        
        $images_http_path = $this->page->getImagesHttpPath();
        
        $daily_header = array(
            'name' => '&nbsp;',
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
        );
        
        $daily_rankings = SteamUsers::getLatestDailyRankingsFromCache($this->steamid);
        
        if(!empty($daily_rankings)) {        
            $daily_ranking_table = new Table("daily_rankings", false);
        
            $daily_ranking_table->setNumberofColumns(13);
            
            $daily_ranking_table->addHeader($daily_header);
            
            foreach($daily_rankings as $number_of_days => $daily_ranking) {
                $day_type_name = '';
                
                if($number_of_days == 'all_time') {
                    $day_type_name = 'All Time';
                }
                else {
                    $day_type_name = "{$number_of_days} Day";
                }
            
                $daily_ranking_table->addRow(array(
                    'name' => $day_type_name,
                    'rank' => $daily_ranking['rank'],  
                    'first_place_ranks' => $daily_ranking['first_place_ranks'],
                    'top_5_ranks' => $daily_ranking['top_5_ranks'],
                    'top_10_ranks' => $daily_ranking['top_10_ranks'],
                    'top_20_ranks' => $daily_ranking['top_20_ranks'],
                    'top_50_ranks' => $daily_ranking['top_50_ranks'],
                    'top_100_ranks' => $daily_ranking['top_100_ranks'],
                    'total_points' => $this->roundNumber($daily_ranking['total_points']),
                    'points_per_day' => $this->roundNumber($daily_ranking['points_per_day']),
                    'total_dailies' => $daily_ranking['total_dailies'],
                    'total_wins' => $daily_ranking['total_wins'],
                    'average_place' => $this->roundNumber($daily_ranking['average_rank'])
                ));
            }
            
            $user_template->addChild($daily_ranking_table, 'daily_ranking_table');
        }
        
        $daily_ranking_season_record = SteamUsers::getLatestDailyRankingSeasonEntryFromCache($this->steamid);
        
        if(!empty($daily_ranking_season_record)) {            
            $user_template->addChild(DailyRankingSeasons::getSeasonFancyName(), 'season_title');
        
            $images_http_path = $this->page->getImagesHttpPath();
        
            $daily_season_table = new Table("daily_rankings", false);
        
            $daily_season_table->setNumberofColumns(12);
            
            $season_header = $daily_header;
            unset($season_header['name']);
            
            $daily_season_table->addHeader($season_header);
            
            $daily_season_table->addRow(array(
                'rank' => $daily_ranking_season_record['rank'],  
                'first_place_ranks' => $daily_ranking_season_record['first_place_ranks'],
                'top_5_ranks' => $daily_ranking_season_record['top_5_ranks'],
                'top_10_ranks' => $daily_ranking_season_record['top_10_ranks'],
                'top_20_ranks' => $daily_ranking_season_record['top_20_ranks'],
                'top_50_ranks' => $daily_ranking_season_record['top_50_ranks'],
                'top_100_ranks' => $daily_ranking_season_record['top_100_ranks'],
                'total_points' => $this->roundNumber($daily_ranking_season_record['total_points']),
                'points_per_day' => $this->roundNumber($daily_ranking_season_record['points_per_day']),
                'total_dailies' => $daily_ranking_season_record['total_dailies'],
                'total_wins' => $daily_ranking_season_record['total_wins'],
                'average_place' => $this->roundNumber($daily_ranking_season_record['average_rank'])
            ));
            
            $user_template->addChild($daily_season_table, 'daily_season_table');
        }*/
        
        /* ---------- Score Leaderboards ---------- */
        
        /*$score_leaderboard_entries = SteamUsers::getUserScoreLeaderboards($this->steamid);
        
        if(!empty($score_leaderboard_entries)) {
            $score_leaderboards_table = new Table('score_rankings');
            
            $score_leaderboards_table->setNumberofColumns(13);
            
            $score_leaderboards_table->addHeader(array(
                'leaderboard_name' => '&nbsp;',
                'cadence' => "<img class=\"cadence_header\" src=\"{$character_placeholder_image}\" />",
                'bard' => "<img class=\"bard_header\" src=\"{$character_placeholder_image}\" />",
                'monk' => "<img class=\"monk_header\" src=\"{$character_placeholder_image}\" />",
                'aria' => "<img class=\"aria_header\" src=\"{$character_placeholder_image}\" />",
                'bolt' => "<img class=\"bolt_header\" src=\"{$character_placeholder_image}\" />",
                'dove' => "<img class=\"dove_header\" src=\"{$character_placeholder_image}\" />",
                'eli' => "<img class=\"eli_header\" src=\"{$character_placeholder_image}\" />",
                'melody' => "<img class=\"melody_header\" src=\"{$character_placeholder_image}\" />",
                'dorian' => "<img class=\"dorian_header\" src=\"{$character_placeholder_image}\" />",
                'coda' => "<img class=\"coda_header\" src=\"{$character_placeholder_image}\" />",
                'all' => "All<br />Chars",
                'story' => "Story<br />Mode"
            ));
        
            $score_leaderboards_table->addRows(SteamUsers::getUserCategoryLeaderboards($this->steamid, 'score', $score_leaderboard_entries));
            
            $user_template->addChild($score_leaderboards_table, 'score_leaderboards_table');
        }*/
        
        /* ---------- Speed Leaderboards ---------- */
        
        /*$speed_leaderboard_entries = SteamUsers::getUserSpeedLeaderboards($this->steamid);
        
        if(!empty($speed_leaderboard_entries)) {
            $speed_leaderboards_table = new Table('score_rankings');
            
            $speed_leaderboards_table->setNumberofColumns(13);
            
            $speed_leaderboards_table->addHeader(array(
                'leaderboard_name' => '&nbsp;',
                'cadence' => "<img class=\"cadence_header\" src=\"{$character_placeholder_image}\" />",
                'bard' => "<img class=\"bard_header\" src=\"{$character_placeholder_image}\" />",
                'monk' => "<img class=\"monk_header\" src=\"{$character_placeholder_image}\" />",
                'aria' => "<img class=\"aria_header\" src=\"{$character_placeholder_image}\" />",
                'bolt' => "<img class=\"bolt_header\" src=\"{$character_placeholder_image}\" />",
                'dove' => "<img class=\"dove_header\" src=\"{$character_placeholder_image}\" />",
                'eli' => "<img class=\"eli_header\" src=\"{$character_placeholder_image}\" />",
                'melody' => "<img class=\"melody_header\" src=\"{$character_placeholder_image}\" />",
                'dorian' => "<img class=\"dorian_header\" src=\"{$character_placeholder_image}\" />",
                'coda' => "<img class=\"coda_header\" src=\"{$character_placeholder_image}\" />",
                'all' => "All<br />Chars",
                'story' => "Story<br />Mode"
            ));
        
            $speed_leaderboards_table->addRows(SteamUsers::getUserCategoryLeaderboards($this->steamid, 'speed', $speed_leaderboard_entries));
            
            $user_template->addChild($speed_leaderboards_table, 'speed_leaderboards_table');
        }*/
        
        /* ---------- Deathless Leaderboards ---------- */
        
        /*$deathless_leaderboard_entries = SteamUsers::getUserDeathlessLeaderboards($this->steamid);
        
        if(!empty($deathless_leaderboard_entries)) {
            $deathless_leaderboards_table = new Table('score_rankings');
            
            $deathless_leaderboards_table->setNumberofColumns(11);
            
            $deathless_leaderboards_table->addHeader(array(
                'leaderboard_name' => '&nbsp;',
                'cadence' => "<img class=\"cadence_header\" src=\"{$character_placeholder_image}\" />",
                'bard' => "<img class=\"bard_header\" src=\"{$character_placeholder_image}\" />",
                'monk' => "<img class=\"monk_header\" src=\"{$character_placeholder_image}\" />",
                'aria' => "<img class=\"aria_header\" src=\"{$character_placeholder_image}\" />",
                'bolt' => "<img class=\"bolt_header\" src=\"{$character_placeholder_image}\" />",
                'dove' => "<img class=\"dove_header\" src=\"{$character_placeholder_image}\" />",
                'eli' => "<img class=\"eli_header\" src=\"{$character_placeholder_image}\" />",
                'melody' => "<img class=\"melody_header\" src=\"{$character_placeholder_image}\" />",
                'dorian' => "<img class=\"dorian_header\" src=\"{$character_placeholder_image}\" />",
                'coda' => "<img class=\"coda_header\" src=\"{$character_placeholder_image}\" />"
            ));
        
            $deathless_leaderboards_table->addRows(SteamUsers::getUserCategoryLeaderboards($this->steamid, 'deathless', $deathless_leaderboard_entries));
            
            $user_template->addChild($deathless_leaderboards_table, 'deathless_leaderboards_table');
        }*/

        $this->page->body->addChild($user_template, 'content');
    }
    
    /*public function apiGetProfile() {
        if(empty($this->steamid)) {
            throw new Exception("id cannot be empty.");
        }
        
        $steam_user_data = SteamUsers::getUser($this->steamid);
        
        return array(
            'steam_id' => $steam_user_data['steamid'],
            'steam_username' => $steam_user_data['personaname'],
            'necrolab_player_profile_id' => $steam_user_data['steam_user_id'],
            'twitch_username' => $steam_user_data['twitch_username'],
            'twitter_username' => $steam_user_data['twitter_username'],
            'website' => $steam_user_data['website']
        );
    }*/
}