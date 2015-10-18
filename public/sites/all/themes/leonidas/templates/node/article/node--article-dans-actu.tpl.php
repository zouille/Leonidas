<?php if (!empty($rendered_article_media)): ?>
  <div class="img">
    <?php print $rendered_article_media; ?>
  </div>
<?php endif; ?>
<?php if (!empty($title)): ?>
  <div class="text">
    <?php if (!empty($category)): ?>
      <span><?php print $category; ?></span>
    <?php endif; ?>
    <div class="p"><?php print $title_link; ?></dvi>
  </div>
<?php endif; ?>
