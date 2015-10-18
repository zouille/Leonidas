<?php
/**
 * @file
 * Template for article content-type read also festivals view mode.
 */
?>
<?php if (!empty($rendered_article_media)): ?>
  <div class="img">
    <?php print $rendered_article_media; ?>
  </div>
<?php endif; ?>
<?php if (!empty($title)): ?>
  <div class="text">
    <p>
      <?php if (!empty($title_link)): ?>
        <?php print $title_link; ?>
      <?php endif; ?>
    </p>
    <?php if (!empty($localization)): ?>
      <p><?php print $localization; ?></p>
    <?php endif; ?>
  </div>
<?php endif; ?>
