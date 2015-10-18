<?php if (!empty($rendered_article_media)): ?>
  <div class="img">
    <?php print $rendered_article_media; ?>
    <?php if ($media_icon): ?>
      <?php print $media_icon; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php if (!empty($title)): ?>
  <div class="text">
    <div class="p">
      <?php if (!empty($title_link)): ?>
        <?php print $title_link; ?>
      <?php endif; ?>
    </div>
    <?php if (!empty($localization)): ?>
      <div class="p"><?php print $localization; ?></div>
    <?php endif; ?>
  </div>
<?php endif; ?>
