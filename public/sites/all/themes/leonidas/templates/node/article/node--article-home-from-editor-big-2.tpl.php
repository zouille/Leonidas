<?php
/**
 * @file
 * Template for article content-type full view mode.
 */
?>
<div class="top-video">
  <div class="holder">
    <?php if (!empty($rendered_article_media)): ?>
      <?php print $rendered_article_media; ?>
    <?php endif; ?>
    <?php if ($media_icon): ?>
      <?php print $media_icon; ?>
    <?php endif; ?>
  </div>
  <?php if (!empty($category) || !empty($city)): ?>
    <span class="trends">
      <?php if (!empty($category)): ?>
        <?php print $category; ?>
      <?php endif; ?>
      <?php if (!empty($city)): ?>
        <?php print $city; ?>
      <?php endif;?>
    </span>
  <?php endif; ?>
  <h2><?php print $title_link; ?></h2>
</div>
