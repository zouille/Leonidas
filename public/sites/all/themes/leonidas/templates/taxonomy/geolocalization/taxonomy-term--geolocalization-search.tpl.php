<?php
/**
 * @file
 * Taxonomy-term--geolocalization-search.tpl.php
 */
?>
<div class="text">
  <?php if (!empty($term_name)): ?>
    <h3><?php print $term_name; ?></h3>
  <?php endif; ?>
  <?php if (!empty($description)): ?>
    <?php print $description; ?>
  <?php endif; ?>
</div>
