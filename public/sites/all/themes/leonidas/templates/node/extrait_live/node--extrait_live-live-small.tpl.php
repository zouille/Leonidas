<div class="extrait-live"<?php print $attributes; ?>>
  <div class="holder<?php print $class; ?>">
    <?php if (!empty($live_image)): ?>
      <div class="img">
        <?php print $live_image; ?>
        <div class="fleche-play play-small"></div>
      </div>
    <?php endif; ?>
    <strong>Extrait live</strong>
    <p><?php print $live_title; ?></p>
  </div>
</div>
