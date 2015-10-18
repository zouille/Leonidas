<?php
/**
 * @file
 * Template for page statique content-type search view mode.
 */
?>
<div class="text">
  <?php if (!empty($title)): ?>
    <h3><?php print $title; ?></h3>
  <?php endif; ?>
  <?php if (!empty($content['field_body'])): ?>
    <?php print render($content['field_body']); ?>
  <?php endif; ?>
</div>
