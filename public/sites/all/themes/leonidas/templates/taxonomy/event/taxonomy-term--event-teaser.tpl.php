<?php
/**
 * @file
 * Taxonomy-term--event-teaser.tpl.php
 */
?>
<?php if (!empty($event_image)): ?>
  <div class="aside-img">
    <?php print $event_image; ?>
  </div>
<?php endif; ?>
<?php if (!empty($term_name)): ?>
  <strong><?php print htmlspecialchars_decode(culturebox_site_l($term_name, "taxonomy/term/$tid"), ENT_QUOTES); ?></strong>
<?php endif; ?>
<?php if (!empty($event_cities) && !empty($event_date)): ?>
  <strong>
    <span><?php print reset($event_cities); ?></span>
    <?php print " $event_date"; ?>
  </strong>
<?php endif; ?>
