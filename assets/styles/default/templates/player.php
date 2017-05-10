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
<br />
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#pbs" aria-controls="pbs" role="tab" data-toggle="tab"><span class="menu_smaller">PBs</span></a></li>
    <li role="presentation"><a href="#rankings" aria-controls="rankings" role="tab" data-toggle="tab"><span class="menu_smaller">Rankings</span></a></li>
    <li role="presentation"><a href="#leaderboards" aria-controls="leaderboards" role="tab" data-toggle="tab"><span class="menu_smaller">Leaderboards</span></a></li>
    <li role="presentation"><a href="#achievements" aria-controls="achievements" role="tab" data-toggle="tab"><span class="menu_smaller">Achievements</span></a></li>
</ul>
<div id="player_tabs" class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="pbs">
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
    </div>
    <div role="tabpanel" class="tab-pane" id="rankings">
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
    </div>
    <div role="tabpanel" class="tab-pane" id="leaderboards">
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
    </div>
    <div role="tabpanel" class="tab-pane" id="achievements">
        <br />
        <p>Progress</p>
        <div class="progress">
            <div id="achievements_progress_bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0px;"> 
                0%
            </div>
        </div>
        <br />
        <table id="player_achievements_table"></table>
    </div>
</div>