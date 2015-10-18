<?php
/**
 * @file
 * Template for article content-type very small view mode.
 */
?>
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
    <p><?php print $title_link; ?></p>
  </div>
<?php endif; ?>
