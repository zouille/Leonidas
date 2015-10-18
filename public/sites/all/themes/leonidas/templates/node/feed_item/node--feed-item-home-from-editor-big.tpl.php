<?php
/**
 * @file
 * Template for feed item content-type full view mode.
 */
?>
<div class="top-video">
  <?php if (!empty($feed_item_image)): ?>
    <div class="holder">
      <?php print $feed_item_image; ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($category)): ?>
    <span class="trends">
      <?php print $category; ?>
    </span>
  <?php endif; ?>
  <h2><?php print $title_link; ?></h2>
</div>
