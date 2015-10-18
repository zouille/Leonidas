<?php
/**
 * @file
 * Template node with specified view mode.
 */
?>
<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($media)): ?>
    <div class="img">
      <?php print $media; ?>
      <?php if (!empty($live_status)): ?>
        <?php print $live_status; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($thematic)): ?>
    <h4><?php echo $thematic; ?></h4>
  <?php endif; ?>
  <strong>
    <?php print culturebox_site_l($node->title, "node/$nid"); ?>
  </strong>
</div>
