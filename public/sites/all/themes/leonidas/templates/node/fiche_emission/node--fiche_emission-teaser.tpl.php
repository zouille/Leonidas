<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($year)): ?>
    <div class="live-year hide-me"><?php print "$year"; ?><!-- year end --></div>
  <?php endif; ?>
  <?php if (!empty($image)): ?>
    <div class="img">
      <?php print $image; ?>
      <?php if (!empty($live_status)): ?>
        <?php print $live_status; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="text">
    <?php if (!empty($name_emission)): ?>
      <span><?php echo $name_emission ?></span>
    <?php endif; ?>
    <?php if (!empty($title)): ?>
      <h3><?php print culturebox_site_l($node->title, "node/$node->nid"); ?><h3>
    <?php endif; ?>
  </div>
  <?php if (!empty($date)): ?>
    <span class="date-f-emission"><?php echo $date;  if(!empty($duree)){ echo $duree . ' min'; }  ?></span>
  <?php endif; ?>
</div>
