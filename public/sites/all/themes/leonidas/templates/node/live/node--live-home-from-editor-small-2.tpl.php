<div class="top-video">
  <div class="holder">
    <?php if (!empty($rendered_article_media)): ?>
      <?php print $rendered_article_media; ?>
    <?php endif; ?>
    <?php if (!empty($media_icon)): ?>
      <?php print $media_icon; ?>
    <?php endif; ?>
  </div>
  <?php if (!empty($category) || !empty($city)): ?>
    <span class="trends">
      <?php if (!empty($channel)): ?>
        <img src="<?php print ($GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . $channel . '.png'); ?>"
        alt=" logo de <?php print $channel ?>">
      <?php endif; ?>
      <?php if (!empty($category) || !empty($tags)): ?>
        <?php print ($tags)?:$category; ?>
      <?php endif; ?>
      <?php if (!empty($city)): ?>
        <?php print $city; ?>
      <?php endif; ?>
    </span>
  <?php endif; ?>
    <h2><?php print $title_link; ?></h2>
  <?php if (!empty($date)): ?>
    <span class="date-f-emission"><?php print $date;
      if (!empty($duree)) { print $duree . ' min'; } ?>
    </span>
  <?php endif; ?>
</div>
