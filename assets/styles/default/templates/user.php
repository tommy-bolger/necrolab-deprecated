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
<div class="menu_small">Player Info</div>
<?php echo $this->steam_user_table; ?>
<?php echo $this->steam_user_form; ?>
<?php if(!empty($this->power_rankings_table)): ?>
<br />
<br />
<div class="menu_small">Power Ranking</div>
<?php echo $this->power_rankings_table; ?>
<?php endif; ?>

<?php if(!empty($this->score_rankings_table)): ?>
<br />
<br />
<div class="menu_small">Score Ranking</div>
<?php echo $this->score_rankings_table; ?>
<?php endif; ?>

<?php if(!empty($this->speed_rankings_table)): ?>
<br />
<br />
<div class="menu_small">Speed Ranking</div>
<?php echo $this->speed_rankings_table; ?>
<?php endif; ?>

<?php if(!empty($this->deathless_score_rankings_table)): ?>
<br />
<br />
<div class="menu_small">Deathless Score Ranking</div>
<?php echo $this->deathless_score_rankings_table; ?>
<?php endif; ?>
<div id="power_ranking"></div>
<div id="daily_ranking"></div>