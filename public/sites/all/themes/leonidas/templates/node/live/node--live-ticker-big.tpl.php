<div class="block left">
  <?php if (!empty($image)): ?>
    <?php print $image; ?>
  <?php endif; ?>
</div>
<div class="block middle">
  <span class="close"></span>
  <?php print $status; ?>
  <strong><?php print culturebox_site_l(truncate_utf8($node->title, 75, TRUE, TRUE), $url); ?></strong>
  <?php print culturebox_site_l($more_link_text, $url, array('attributes' => array('class' => array('more-link')))); ?>
</div>
