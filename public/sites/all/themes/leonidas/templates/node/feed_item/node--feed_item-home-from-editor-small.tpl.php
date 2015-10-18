<?php
/**
 * @file
 * Template for feed item content-type full view mode.
 */
?>
<?php if (!empty($category)) : ?>
  <span class="trends">
    <?php print $category; ?>
  </span>
<?php endif; ?>
<?php if (!empty($title_link)): ?>
  <h3><?php print $title_link; ?></h3>
<?php endif; ?>
