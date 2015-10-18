<?php
/**
 * @file
 * Template for article content-type full view mode.
 */
?>
<?php if (!empty($image)): ?>
  <div class="img">
    <?php print $image; ?>
    <?php if ($media_icon): ?>
      <?php print $media_icon; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php if (!empty($title)): ?>
  <div class="text">
    <p><?php print culturebox_site_l($title, "node/$nid", array('html' => TRUE)); ?></p>
  </div>
<?php endif; ?>
