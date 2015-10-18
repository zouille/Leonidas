<p></p>
<div class='illustration'>
  <?php if (!empty($content['field_asset_image'])): ?>
    <?php print render($content['field_asset_image']); ?>
  <?php endif; ?>
  <br />
  <?php if (!empty($content['field_asset_description'])): ?>
    <span class="legende"><?php print strip_tags((render($content['field_asset_description'])), '<a><span>'); ?></span>
  <?php endif; ?>
  <?php if (!empty($content['field_asset_image_copyright'])): ?>
    <span class="copyright">&copy; <?php print strip_tags(render($content['field_asset_image_copyright']), '<a><span>'); ?></span>
  <?php endif; ?>
</div>
<p></p>
