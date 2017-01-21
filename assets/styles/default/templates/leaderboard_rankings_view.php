<br />
<div class="menu_small">
    <?php if($this->has_character_image): ?>
    <img class="<?php echo $this->character_name; ?>_header" src=<?php echo $this->character_placeholder_image_url; ?> alt="<?php echo $this->character_name; ?>" />
    <?php endif; ?>
    <?php echo $this->leaderboard_name; ?>
</div>
<?php echo $this->rankings_table; ?>