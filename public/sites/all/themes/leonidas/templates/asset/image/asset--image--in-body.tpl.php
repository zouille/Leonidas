<div class="illustration<?php print (!empty($custom_classes) ? $custom_classes : ''); ?>">
  <?php if (!empty($content['field_asset_image'])): ?>
    <div class="content-img">
      <?php print render($content['field_asset_image']); ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($content['field_asset_description']) || !empty($content['field_asset_image_copyright'])): ?>
    <span class="description">
      <?php if (!empty($content['field_asset_description'])): ?>
        <p><?php print strip_tags((render($content['field_asset_description'])), '<a><span>'); ?></p>
      <?php endif; ?>
      <?php if (!empty($copyright)): ?>
        <span>&copy; <?php print $copyright; ?></span>
      <?php endif; ?>
    </span>
  <?php endif; ?>
</div>
