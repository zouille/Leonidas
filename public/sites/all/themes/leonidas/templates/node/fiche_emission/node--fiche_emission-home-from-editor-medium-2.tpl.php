<?php if (!empty($media)): ?>
  <div class="img">
    <?php echo $media; ?>
    <?php if (!empty($live_status)): ?>
      <?php echo $live_status; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php if (!empty($emssion_parent)): ?>
  <span class="trends"><?php print $emssion_parent; ?></span>
<?php endif; ?>
<?php if (!empty($title)): ?>
  <span>
    <?php print $title; ?>
  </span>
<?php endif; ?>
<?php if (!empty($channel)): ?>
  <span class="genre">
    <img src="<?php print ($GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . $channel . '.png'); ?>" alt="<?php print $channel; ?>">
  <?php endif; ?>
  <?php if (!empty($date)): ?>
    <span class="date-f-emission"><?php print $date; ?></span>
  </span>
<?php endif; ?>
