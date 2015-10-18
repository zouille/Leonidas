<?php
/**
 * @file
 * template.
 */
?>
<?php if (!empty($content['field_asset_image']->title)):?>
  <?php unset($content['field_asset_image']->title);?>
<?php endif;?>
<?php if (!empty($content['field_asset_image'])): ?>
  <div class="img">
    <?php print drupal_render($content['field_asset_image']) ?>
  </div>
<?php endif; ?>
