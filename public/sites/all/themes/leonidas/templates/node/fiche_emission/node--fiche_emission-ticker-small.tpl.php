<div class="block left">
  <?php if (!empty($image)): ?>
    <?php print $image; ?>
    <?php if ($show_play): ?>
      <span class="play">&nbsp;</span>
    <?php endif; ?>
  <?php endif; ?>
</div>
<div class="block middle">
  <?php print $status; ?>
  <strong><?php print culturebox_site_l(truncate_utf8($node->title, 90, TRUE, TRUE), $url); ?></strong>
</div>
