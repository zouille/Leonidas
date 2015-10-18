<?php
/**
 * @file
 * Template for article content-type full view mode.
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
<?php if (!empty($category) || !empty($city)) : ?>
  <span class="trends">
    <?php if (!empty($category)): ?>
      <?php print $category; ?>
    <?php endif; ?>
    <?php if (!empty($city)) : ?>
      <?php print $city; ?>
    <?php endif;?>
  </span>
<?php endif; ?>
<?php if (!empty($title)): ?>
  <h3><?php print $title_link; ?></h3>
<?php endif; ?>
