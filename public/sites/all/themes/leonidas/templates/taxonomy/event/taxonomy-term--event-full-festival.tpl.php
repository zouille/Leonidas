<?php
/**
 * @file
 * Taxonomy-term--event-full-festival.tpl.php
 */
?>
<div class="resultat-recherche">
  <div class="first-element">
    <div class="head">
      <?php if (!empty($export_link)):?>
        <?php print $export_link;?>
      <?php endif;?>
      <?php if (!empty($term_name)): ?>
        <h1>Les lives <span><?php print $term_name; ?></span></h1>
      <?php endif; ?>
      <div class="line">
        <?php if (!empty($date)): ?>
          <span class="date"><?php print $date; ?></span>
        <?php endif; ?>
        <?php if (!empty($categories)): ?>
          <span class="tag"><?php print $categories; ?></span>
        <?php endif; ?>
        <?php if (!empty($localizations)): ?>
          <?php foreach($localizations as $localization): ?>
            <span><?php print $localization; ?></span>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    <div class="top-holder">
      <?php if (!empty($content['field_illustration'])): ?>
        <div class="img">
          <?php print render($content['field_illustration']); ?>
        </div>
      <?php endif; ?>
      <div class="text">
        <?php print drupal_render($content['description']); ?>
      </div>
    </div>
  </div>
</div>
