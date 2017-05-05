<br />
<div class="clear login_bar">
    <?php if(!empty($this->steam_login_form)): ?>
    <div class="login_text">
        Want to edit your player info? Login through Steam and you'll be redirected back here.
    </div>
    <div class="login_form">
        <?php echo $this->steam_login_form; ?>
    </div>
    <div class="clear"></div>
    <?php else: ?>
    <div class="menu_small">Edit Your Information</div>
    <br />
    <?php endif; ?>
</div>
<table id="player_info_table"></table>
<br />
<div class="menu_smaller">Achievements</div>
<br />
<p>Progress</p>
<div class="progress">
    <div id="achievements_progress_bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0px;"> 
        0%
    </div>
</div>
<br />
<table id="player_achievements_table"></table>
<br />
<br />
<div class="menu_small">PBs</div>
<br />
<div class="menu_smaller">Score</div>
<br />
<table id="player_score_pbs_table"></table>
<br />
<div class="menu_smaller">Speed</div>
<br />
<table id="player_speed_pbs_table"></table>
<br />
<div class="menu_smaller">Deathless</div>
<br />
<table id="player_deathless_pbs_table"></table>
<br />
<br />
<div class="menu_small">Rankings</div>
<br />
<div class="menu_smaller">Power</div>
<br />
<table id="player_power_rankings_table"></table>
<br />
<div class="menu_smaller">Score</div>
<br />
<table id="player_score_rankings_table"></table>
<br />
<div class="menu_smaller">Speed</div>
<br />
<table id="player_speed_rankings_table"></table>
<br />
<div class="menu_smaller">Deathless</div>
<br />
<table id="player_deathless_rankings_table"></table>
<br />
<div class="menu_smaller">Character</div>
<br />
<table id="player_character_rankings_table"></table>
<br />
<div class="menu_smaller">Daily</div>
<br />
<table id="player_daily_rankings_table"></table>
<br />
<br />
<div class="menu_small">Leaderboards</div>
<br />
<div class="menu_smaller">Score</div>
<br />
<table id="player_score_leaderboards_table"></table>
<br />
<div class="menu_smaller">Speed</div>
<br />
<table id="player_speed_leaderboards_table"></table>
<br />
<div class="menu_smaller">Deathless</div>
<br />
<table id="player_deathless_leaderboards_table"></table>
<br />
<div class="menu_smaller">Daily</div>
<br />
<table id="player_daily_leaderboards_table"></table>