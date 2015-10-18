<?php
/**
 * @file template.
 */
?>
<?php if (!empty($image)): ?>
  <div class="content-img">
    <?php print $image; ?>
  </div>
<?php endif; ?>
<?php if (!empty($legend) || !empty($copyright)): ?>
  <span class="description">
    <?php if (!empty($legend)): ?>
      <?php print $legend; ?>
    <?php endif; ?>
    <?php if (!empty($copyright)): ?>
      <span>© <?php print $copyright; ?></span>
    <?php endif; ?>
  </span>
<?php endif; ?>
