<?php
/**
 * @file
 * Template for feed_item teaser.
 */
?>
<?php if (!empty($image)): ?>
  <div class="img">
    <?php print $image; ?>
  </div>
<?php endif; ?>
<?php if (!empty($title_link)): ?>
  <div class="text">
    <?php print $title_link; ?>
  </div>
<?php endif; ?>
