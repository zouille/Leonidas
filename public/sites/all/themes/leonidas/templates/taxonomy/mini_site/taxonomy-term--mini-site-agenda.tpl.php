<?php if (!empty($content['field_mini_site_main_image'])): ?>  
  <div class="aside-img">
    <?php print culturebox_site_l(render($content['field_mini_site_main_image']), "taxonomy/term/$term->tid", array('html' => TRUE)); ?>
  </div>
<?php endif; ?>
<p><?php print htmlspecialchars_decode(culturebox_site_l($term->name, "taxonomy/term/$term->tid"), ENT_QUOTES); ?></p>
<small><?php print $subtitle; ?></small>

