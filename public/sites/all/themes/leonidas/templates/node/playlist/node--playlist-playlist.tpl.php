<div class="img">
  <div class="playlist-slider">
    <a href="<?php print url('node/' . $node->nid); ?>">
      <?php if (!empty($content['field_live_media'])): ?>
        <?php print render($content['field_live_media']); ?>
      <?php else: ?>
        <?php print theme('image', array('path' => 'sites/all/themes/culturebox/images/generique-playlists.png', 'width' => 186, 'height' => 186)) ?>
      <?php endif; ?>

    <div class="div-moq">
      <p class="p-moq"><span class="moq"><?php print $title; ?></span></p>
    </div>
    </a>
  </div>
</div>