<div class="fiche-emission-very-small">
  <div class="img">
    <?php print culturebox_site_l(render($content['field_fiche_emission_main_media']), "node/{$node->nid}", array('html' => TRUE)); ?>
    <div class="play play-medium"></div>
  </div>
  <?php if (!empty($channel)): ?>
    <img class="mini-channel"
         src="<?php print ($GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . $channel . '.png'); ?>"
         alt="<?php print $channel; ?>" />
       <?php endif; ?>
       <?php if (!empty($emission)): ?>
    <span class="genre">
      <?php print culturebox_site_l($emission->name, "taxonomy/term/{$emission->tid}"); ?>
    </span>
  <?php endif; ?>
  <p><?php print culturebox_site_l($node->title, "node/{$node->nid}"); ?></p>
</div>
