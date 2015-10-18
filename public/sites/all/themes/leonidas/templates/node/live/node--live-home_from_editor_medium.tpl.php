<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($media)): ?>
    <div class="img">
      <?php echo $media; ?>
      <?php if (!empty($live_status)): ?>
        <?php echo $live_status; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($tags)): ?>
    <span class="tags">
      <?php if (!empty($channel)): ?>
        <img class="small-img" src="<?php print ($GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . $channel . '.png'); ?>" alt="<?php print $channel; ?>">
      <?php endif; ?>
      <?php echo $tags; ?>
    </span>
  <?php endif; ?>
  <?php if (!empty($title)): ?>
    <p><?php echo $title; ?></p>
  <?php endif; ?>
</div>
