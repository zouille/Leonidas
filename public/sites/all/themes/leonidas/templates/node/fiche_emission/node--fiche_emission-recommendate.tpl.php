<div class="content">
  <div class="img-extrait-sommaire">
    <?php print render($image); ?>
    <?php if (!empty($live_status)): ?>
      <?php print $live_status; ?>
    <?php endif; ?>
  </div>
  <div class="sommaire">
    <div class="field-sub"><?php print culturebox_site_l($node->title, 'node/' . $node->nid); ?></div>
    <div class="field-desc"><?php print render($content['field_fe_chapo']); ?></div>
  </div>
</div>