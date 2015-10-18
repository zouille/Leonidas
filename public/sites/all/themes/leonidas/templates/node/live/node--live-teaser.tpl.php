<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($year)): ?>
    <div class="live-year hide-me"><?php print "$year"; ?><!-- year end --></div>
  <?php endif; ?>
  <?php if (!empty($image)): ?>
    <div class="img">
      <?php print $image; ?>
      <?php if (!empty($live_status)): ?>
        <?php print $live_status; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="text">
    <?php if (!empty($category)): ?>
      <span class="clearfix">
        <?php if (!empty($channel)): ?>
          <img class="small-img" src="<?php print ($GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . $channel . '.png'); ?>" alt="<?php print $channel; ?>">
        <?php endif; ?>
        <?php print $category; ?>
      </span>
    <?php endif; ?>
    <?php if (!empty($title)): ?>
      <div class="p"><?php print culturebox_site_l($node->title, "node/$node->nid", array('html' => TRUE)); ?></div>
    <?php endif; ?>
  </div>
</div>
