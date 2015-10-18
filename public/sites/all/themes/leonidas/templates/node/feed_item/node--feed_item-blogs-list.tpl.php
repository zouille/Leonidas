<?php
/**
 * @file
 * Template for feed_item teaser.
 */
?>
<?php if (!empty($content['field_image'])): ?>
  <div class="img">
    <a href="#"><?php print $content['field_image']; ?></a>
  </div>
<?php endif; ?>
<div class="text">
  <?php print $title_link; ?>
</div>