<?php
/**
 * @file
 * Template for live content-type live widget related video view mode.
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
  <?php if (!empty($category)): ?>
    <strong>
      <?php print $category; ?>
    </strong>
  <?php endif; ?>
  <p><?php print culturebox_site_l($node->title, "node/$node->nid", array('attributes' => array('target' => '_blank', 'class' => array('widget-player-link')))); ?></p>
</div>
