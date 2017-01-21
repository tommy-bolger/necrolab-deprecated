<br />
<div class="menu_small">Player Info</div>
<?php echo $this->steam_user_table; ?>
<?php echo $this->steam_user_form; ?>
<?php if(!empty($this->power_rankings_table)): ?>
<br />
<br />
<div class="menu_small">Power Ranking</div>
<?php echo $this->power_rankings_table; ?>
<?php endif; ?>

<?php if(!empty($this->daily_ranking_table)): ?>
<br />
<br />
<div class="menu_small">Daily Ranking</div>
<?php echo $this->daily_ranking_table; ?>
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
<div class="menu_small">Deathless Ranking</div>
<?php echo $this->deathless_score_rankings_table; ?>
<?php endif; ?>
<div id="power_ranking"></div>
<div id="daily_ranking"></div>