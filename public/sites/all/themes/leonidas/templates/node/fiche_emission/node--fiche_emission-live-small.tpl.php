<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <div class="holder<?php print $class; ?>">
    <?php if (!empty($live_image)): ?>
      <div class="img">
        <?php print $live_image; ?>
        <?php if (!empty($live_status)): ?>
          <?php print $live_status; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($category)): ?>
      <strong><?php print $category; ?></strong>
    <?php endif; ?>
    <p><?php print $live_title; ?></p>
  </div>
</div>
