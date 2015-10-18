<!--d--><?php if (!empty($rendered_article_media)): ?>
  <div class="img">
    <?php print $rendered_article_media; ?>
    <?php if ($media_icon): ?>
      <?php print $media_icon; ?>
    <?php endif; ?>
    <span class="num"><?php print $position; ?></span>
  </div>
<?php endif; ?>
<?php if (!empty($category)): ?>
  <strong><?php print $category; ?></strong>
<?php endif; ?>
<?php if (!empty($title_link)): ?>
  <p><?php print $title_link; ?></p>
<?php endif; ?>
