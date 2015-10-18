<div class="top-video">
  <div class="holder">
    <?php if (!empty($rendered_article_media)): ?>
      <?php print $rendered_article_media; ?>
    <?php endif; ?>
    <?php if (!empty($live_status)): ?>
      <?php print $live_status; ?>
    <?php endif; ?>
  </div>
  <?php if (!empty($emssion_parent)): ?>
    <span class="trends">
       <?php if (!empty($channel)): ?>
         <img src="<?php print ( $GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . $channel . '.png'); ?>" alt=" logo de <?php print $channel?>">
       <?php endif; ?>
      <?php if (!empty($emssion_parent)): ?>
        <?php print $emssion_parent; ?>
      <?php endif; ?>
    </span>
  <?php endif; ?>
  <h2><?php print $title_link; ?></h2>
  <?php if (!empty($date)): ?>
    <span class="date-f-emission"><?php print $date;  if(!empty($duree)){ print $duree . ' min'; } ?></span>
  <?php endif; ?>
</div>
