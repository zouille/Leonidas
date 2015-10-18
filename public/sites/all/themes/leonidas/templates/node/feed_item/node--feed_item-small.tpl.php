<?php
/**
 * @file
 * Template for feed item content-type small view mode.
 */
?>
<?php if (!empty($feed_item_image)): ?>
  <div class="img">
    <?php print $feed_item_image; ?>
  </div>
<?php endif; ?>
<?php if (!empty($category)): ?>
  <strong><?php print $category; ?></strong>
<?php endif; ?>
<?php if (!empty($title_link)): ?>
  <p><?php print $title_link; ?></p>
<?php endif; ?>
