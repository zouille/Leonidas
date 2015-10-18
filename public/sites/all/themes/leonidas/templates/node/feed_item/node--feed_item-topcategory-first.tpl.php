<?php
/**
 * @file
 * Template node with specified view mode.
 */
?>
<?php if (!empty($feed_item_image)): ?>
  <div class="img">
    <?php print $feed_item_image; ?>
  </div>
<?php endif; ?>
<?php if (!empty($title_link)): ?>
  <h4 class="title">
    <?php print $title_link; ?>
  </h4>
<?php endif; ?>
