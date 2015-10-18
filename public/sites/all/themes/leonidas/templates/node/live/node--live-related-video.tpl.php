<?php
/**
 * @file
 * Template for live content-type live related video view mode.
 */
?>
<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($image)): ?>
    <div class="img">
      <?php print $image; ?>
      <?php if (!empty($live_status)): ?>
        <?php print $live_status; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($title)): ?>
    <div class="p"><?php print culturebox_site_l($node->title, "node/$node->nid"); ?></div>
  <?php endif; ?>
</div>
