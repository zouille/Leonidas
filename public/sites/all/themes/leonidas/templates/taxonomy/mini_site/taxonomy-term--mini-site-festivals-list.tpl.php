<?php
/**
 * @file
 * Taxonomy-term--event-festivals.tpl.php
 */
?>
<div class="event-post">
  <div class="ttl">
    <h2><?php print $type_title; ?> <span><?php print $term->name; ?></span></h2>
    <?php print culturebox_site_l('Toutes les vidéos', "taxonomy/term/{$term->tid}", array('attributes' => array('class' => array('btn-01'))));?>
  </div>
  <div class="holder">
    <?php if (!empty($media)): ?>
    <div class="big-img">
      <?php print $media; ?>
    </div>
    <?php endif; ?>
    <div class="block">
      <div class="ttl">
        <?php if (isset($show_arrows) && $show_arrows):?>
          <span class="prev">Précédent</span>
          <span class="next">Suivant</span>
        <?php endif; ?>
        <ul class="ttl-list">
          <?php if (!empty($date)): ?>
            <li><?php print $date; ?></li>
          <?php endif; ?>
          <?php if (!empty($categories) || !empty($localizations)): ?>
          <li>
            <?php if (!empty($categories)): ?>
              <?php print $categories; ?>
            <?php endif; ?>
            <?php if (!empty($localizations)): ?>
              <?php foreach($localizations as $localization): ?>
                <?php print $localization; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </li>
          <?php endif; ?>
        </ul>
      </div>
      <?php if (!empty($lives)): ?>
        <div class="frame">
          <?php print $lives; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
