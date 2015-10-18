<?php
/**
 * @file
 * Template node with specified view mode.
 */
?>
<?php if (!empty($media)): ?>
  <div class="img<?php if (!empty($media_icon)) print ' img-icon'; ?>">
    <?php print $media; ?>
    <?php if (!empty($media_icon)): ?>
      <?php print $media_icon; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>
<h4 class="title">
  <?php print culturebox_site_l($node->title, "node/$nid"); ?>
</h4>
