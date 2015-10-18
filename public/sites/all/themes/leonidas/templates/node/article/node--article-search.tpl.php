<?php
/**
 * @file
 * Template for article content-type search view mode.
 */
?>
<li>
  <?php if (!empty($image)): ?>
    <div class="img">
      <?php print $image; ?>
      <?php if ($media_icon): ?>
        <?php print $media_icon; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="text">
    <span class="trends">
      <?php if (!empty($category)): ?>
        <?php print ' ' . $category; ?>
      <?php endif; ?>
    </span>
    <?php if(!empty($title)): ?>
      <h3><?php print $title; ?></h3>
    <?php endif; ?>
    <?php if (!empty($content['field_article_catchline'])): ?>
      <p><?php print assets_cut_filter_process(render($content['field_article_catchline'])); ?></p>
    <?php endif; ?>
  </div>
</li>