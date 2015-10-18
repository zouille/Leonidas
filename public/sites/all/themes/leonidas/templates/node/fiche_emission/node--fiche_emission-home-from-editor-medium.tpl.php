<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($media)): ?>
    <div class="img">
      <?php echo $media; ?>
      <?php if (!empty($live_status)): ?>
        <?php echo $live_status; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="infos-text">
    <?php if (!empty($name_emission)): ?>
      <span class="tags">
        <?php if (!empty($channel)): ?>
          <img class="small-img" src="<?php print ($GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . $channel . '.png'); ?>" alt="<?php print $channel; ?>">
        <?php endif; ?>
        <?php echo $name_emission; ?>
      </span>
    <?php endif; ?>
    <?php if (!empty($title)): ?>
      <div class="h3"><?php echo $title; ?></div>
    <?php endif; ?>
    <?php if (!empty($date)): ?>
      <span class="date-f-emission"><?php echo $date; ?><?php if (!empty($duree)): ?><?php echo $duree . ' min'; ?><?php endif; ?></span>
    <?php endif; ?>
    <?php if (!empty($content['field_fe_chapo'])): ?>
      <p><?php print render($content['field_fe_chapo']); ?></p>
    <?php endif; ?>
  </div>
</div>

