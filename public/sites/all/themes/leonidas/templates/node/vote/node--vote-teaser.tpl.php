<div class="vote-teaser">
  <?php print culturebox_site_l($description, 'node/' . $node->nid, array('attributes' => array('alt' => 'Voir le témoignage', 'title' => 'Voir le témoignage', 'class' => array('bloc-lien')))); ?>
  <blockquote>
    <?php if (!empty($description)): ?>
      <div><?php print culturebox_site_l($description, 'node/' . $node->nid); ?></div>
    <?php endif; ?>
    <?php if (!empty($content['field_body']) && $display_comment): ?>
      <span class="date-f-emission"><?php print render($content['field_body']); ?></span>
    <?php endif; ?>
  </blockquote>
</div>