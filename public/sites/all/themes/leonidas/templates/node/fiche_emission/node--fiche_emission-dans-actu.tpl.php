<?php if (!empty($image)): ?>
  <div class="img">
    <?php print $image; ?>
    <?php if (!empty($live_status)): ?>
      <?php print $live_status; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>
<div class="text clearfix">
  <?php if (!empty($category)): ?>
    <span><?php print $category; ?></span>
  <?php endif; ?>
  <?php if (!empty($title)): ?>
    <p><?php print culturebox_site_l($node->title, "node/$node->nid"); ?></p>
  <?php endif; ?>
</div>
