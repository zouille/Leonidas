<?php
/**
 * @file
 * Template for article content-type teaser view mode.
 */
?>
<?php if (!empty($image)): ?>
  <div class="img">
    <?php print $image; ?>
    <?php if (!empty($media_icon)): ?>
      <?php print $media_icon; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php if (!empty($category)): ?>
  <strong class="genre"><?php print $category; ?></strong>
<?php endif; ?>
<?php if (!empty($title)): ?>
  <h3><?php print culturebox_site_l($title, "node/$nid", array('html' => TRUE)); ?></h3>
<?php endif; ?>
<?php if (!empty($description)): ?>
  <p><?php print $description; ?></p>
<?php endif; ?>
