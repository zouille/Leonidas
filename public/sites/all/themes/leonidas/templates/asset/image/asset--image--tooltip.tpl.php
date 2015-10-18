<?php
/**
 * @file template.
 */
?>
<div class="<?php print $classes; ?>">
  <?php if ($content['field_asset_image']): ?>
    <?php print render($content['field_asset_image']); ?>
  <?php endif; ?>
</div>
