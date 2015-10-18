<?php
/**
 * @file
 * Template for article content-type in slider view mode.
 */
?>
  <!-- Override template view for live home page -->
<?php if ($_GET['q'] == 'live'): ?>
  <div class='img'>
    <?php if (!empty($rendered_article_media)): ?>
      <?php print $rendered_article_media; ?>
    <?php endif; ?>
    <?php if ($is_video || $is_slider): ?>
      <?php print $icon_link; ?>
    <?php endif; ?>
  </div>
  <?php if (!empty($category)): ?>
    <h4><?php print $category; ?></h4>
  <?php endif; ?>
  <?php if (!empty($title)): ?>
    <strong><?php print $title_link; ?></strong>
  <?php endif; ?>
<?php else: ?>
  <ul class="side-list slider-list">
    <li>
      <div class='thematic-block-image-holder'>
        <?php if (!empty($rendered_article_media)): ?>
          <?php print $rendered_article_media; ?>
        <?php endif; ?>
        <?php if ($is_video || $is_slider): ?>
          <?php print $icon_link; ?>
        <?php endif; ?>
      </div>
      <?php if (!empty($category)): ?>
        <strong class="name"><?php print $category; ?></strong>
      <?php endif; ?>
      <?php if (!empty($title)): ?>
        <p><?php print $title_link; ?></p>
      <?php endif; ?>
    </li>
  </ul>
<?php endif; ?>