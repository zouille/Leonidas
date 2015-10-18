<?php
/**
 * @file
 * Template node with specified view mode.
 */
?>
<div class="holder">
  <?php if (!empty($link_all)): ?>
    <?php print $link_all; ?>
  <?php endif; ?>
  <h3><?php print $category; ?></h3>
</div>
<div class="img<?php if (!empty($media_icon)) print ' img-icon'; ?>">
  <?php if (!empty($media)): ?>
    <?php print $media; ?>
  <?php endif; ?>
  <?php if (!empty($media_icon)): ?>
    <?php print $media_icon; ?>
  <?php endif; ?>
</div>
<h4 class="title">
  <?php print culturebox_site_l($node->title, "node/$nid"); ?>
</h4>
