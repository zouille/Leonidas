<?php
/**
 * @file
 * Template for live content-type live teaser view mode.
 */
?>
<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($image)): ?>
    <div class="img">
      <?php print $image; ?>
      <?php if (!empty($media_icon)): ?>
        <?php print $media_icon; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($category)): ?>
    <strong><?php print $category; ?></strong>
  <?php endif; ?>
  <?php if (!empty($title)): ?>
    <p><?php print culturebox_site_l($node->title, "node/$node->nid"); ?></p>
  <?php endif; ?>
</div>
