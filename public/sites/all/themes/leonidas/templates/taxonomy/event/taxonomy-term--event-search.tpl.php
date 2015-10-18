<?php
/**
 * @file
 * Taxonomy-term--event-teaser.tpl.php
 */
?>
<?php if (!empty($event_image)): ?>
  <div class="img">
    <?php print $event_image; ?>
  </div>
<?php endif; ?>
<div class="text">
  <?php if (!empty($event_date)): ?>
    <span class="date">
      <?php print "$event_date"; ?>
    </span>
  <?php endif; ?>
  <span class="trends">
    <?php if (!empty($category)): ?>
      <?php print ' ' . $category; ?>
    <?php endif; ?>
  </span>
  <?php if (!empty($term_name)): ?>
    <h3><?php print $term_name; ?></h3>
  <?php endif; ?>
</div>
