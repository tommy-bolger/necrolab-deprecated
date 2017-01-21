<br />
<div class="menu_small">
    <?php if(!empty($this->has_character_image)): ?>
    <img class="<?php echo $this->character_name; ?>_header" src=<?php echo $this->character_placeholder_image_url; ?> alt="<?php echo $this->character_name; ?>" />
    <?php endif; ?>
    <?php echo $this->table_title; ?>
</div>
<table id="entries_table"></table>