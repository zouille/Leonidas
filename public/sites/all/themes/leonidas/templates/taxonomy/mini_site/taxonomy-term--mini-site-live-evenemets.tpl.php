<?php
/**
 * @file
 * Taxonomy-term--event-live-evenements.tpl.php
 */
?>
<?php if (!empty($live_nodes)): ?>
  <div class="event<?php echo !empty($actu) ? ' home' : ''; ?>">
    <?php if (!empty($media)): ?>
      <div class="big-img">
        <?php echo $media; ?>
      </div>
    <?php endif; ?>
    <div class="event-block">
      <div class="ttl">
        <h2>événement <span><?php echo $name; ?></span></h2>
        <?php if (!empty($show_arrows)): ?>
          <span class="prev">Précédent</span>
          <span class="next">Suivant</span>
        <?php endif; ?>
      </div>
      <div class="event-slider">
        <div class="frame">
          <?php echo $live_nodes; ?>
        </div>
      </div>
    </div>
  </div>
<?php else: ?>
  <?php print ''; ?>
<?php endif; ?>
