<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($image)): ?>
    <div class="img">
      <?php print $image; ?>
    </div>
  <?php endif; ?>
  <div class="text">
    <?php if (!empty($category)): ?>
      <span><?php print $category; ?></span>
    <?php endif; ?>
    <?php if (!empty($title)): ?>
      <p><?php print culturebox_site_l($node->title, "node/$node->nid"); ?></p>
    <?php endif; ?>
  </div>
</div>
