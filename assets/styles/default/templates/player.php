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