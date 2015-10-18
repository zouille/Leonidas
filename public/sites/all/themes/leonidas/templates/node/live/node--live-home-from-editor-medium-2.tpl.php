<?php if (!empty($media)): ?>
  <div class="img">
    <?php echo $media; ?>
    <?php if (!empty($live_status)): ?>
      <div class="<?php if (!$show_picto_play_status): ?>no-video-icons <?php endif; ?>bientot-direct">
        <?php echo $live_status; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php if (!empty($tags)) : ?>
  <?php if (!empty($channel)): ?>
    <img class="small-img" src="<?php print ($GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . $channel . '.png'); ?>" alt="<?php print $channel; ?>">
  <?php endif; ?>
  <span class="<?php print (!empty($node->home_from_editor_medium_2_custom_display) ? 'tags' : 'genre'); ?>">
    <?php print $tags; ?>
  </span>
<?php endif; ?>
<?php if (!empty($title)): ?>
  <?php if (!empty($node->home_from_editor_medium_2_custom_display)): ?>
    <div class="h3"><?php print $title; ?></div>
  <?php else: ?>
    <h3><?php print $title; ?></h3>
  <?php endif; ?>
<?php endif; ?>
<?php if (!empty($description)): ?>
  <p><?php print $description; ?></p>
<?php endif; ?>
