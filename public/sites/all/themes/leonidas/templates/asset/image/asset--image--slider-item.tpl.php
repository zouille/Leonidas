<?php
/**
 * @file template.
 */
?>
<?php if (!empty($content['field_asset_image'])): ?>
  <?php print drupal_render($content['field_asset_image']) ?>
<?php endif; ?>
<?php if (!empty($content['field_asset_description'])): ?>
  <?php print drupal_render($content['field_asset_description']) ?>
<?php endif; ?>
