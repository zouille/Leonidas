<?php
/**
 * @file
 * Taxonomy-term--event-festivals.tpl.php
 */
?>
<!-- Hide term without related nodes -->
<?php if (!empty($nodes)): ?>
  <div class="event-post">
    <div class="ttl">
      <h2>événement <span><?php print $term->name; ?></span></h2>
      <?php print $term_link; ?>
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
          <div class="frame">
            <?php print $nodes; ?>
          </div>
      </div>
    </div>
  </div>
<?php endif; ?>