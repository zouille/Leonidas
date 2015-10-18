<?php
/**
 * @file
 * Template for article content-type live widget related video view mode.
 */
?>
<?php if (!empty($feed_item_image)): ?>
<div class="img">
  <?php print $feed_item_image; ?>
</div>
<?php endif; ?>
<?php if (!empty($title)): ?>
  <div class="text">
    <?php if (!empty($category)): ?>
      <strong><?php print $category; ?></strong>
    <?php endif; ?>
    <p>
      <?php if (!empty($title_link)): ?>
        <?php print $title_link; ?>
      <?php endif; ?>
    </p>
  </div>
<?php endif; ?>
