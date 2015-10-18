<?php if (!empty($content['field_body'])): ?>
  <div class="main-block clearfix extrait"<?php print $attributes; ?>>
    <div class="about-video content-text">
      <?php print render($content['field_body']); ?>
    </div>
  </div>
<?php endif; ?>
