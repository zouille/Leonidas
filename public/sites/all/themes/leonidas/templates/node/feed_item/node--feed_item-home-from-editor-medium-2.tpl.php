<?php
/**
 * @file
 * Template for article content-type full view mode.
 */
?>
<?php if (!empty($feed_item_image)): ?>
  <div class="img">
    <?php print $feed_item_image; ?>
  </div>
<?php endif; ?>
<?php if (!empty($category)): ?>
  <span class="genre">
    <?php if (!empty($category)): ?>
      <?php print $category; ?>
    <?php endif; ?>
  </span>
<?php endif; ?>
<?php if (!empty($title)): ?>
  <h3><?php print $title_link; ?></h3>
<?php endif; ?>
<?php if (!empty($description)): ?>
  <p><?php print $description; ?></p>
<?php endif; ?>
