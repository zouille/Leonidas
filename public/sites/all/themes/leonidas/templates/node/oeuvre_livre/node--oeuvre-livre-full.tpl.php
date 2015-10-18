<div class="first-element first-element-personality" itemscope itemtype="http://schema.org/Book">
  <h1 itemprop="name"><?php print $node->title; ?></h1>
  <div class="holder">
    <?php if (!empty($content['field_illustration'])): ?>
      <div class="img">
        <?php if (!empty($microdata_image)): ?>
          <?php print $microdata_image; ?>
        <?php endif; ?>
        <?php print render($content['field_illustration']); ?>
      </div>
    <?php endif; ?>
    <div class="info related-links">
      <?php if (!empty($auteurs)): ?>
        <strong class="date"><?php print $auteurs; ?></strong>
      <?php endif; ?>
      <?php if (!empty($editeur)): ?>
        <?php print $editeur; ?>
      <?php endif; ?>
      <?php if (!empty($annee_edition)): ?>
        <?php print (!empty($editeur) ? '/ ' : '') . $annee_edition; ?>
      <?php endif; ?>
      <?php if (!empty($categories)): ?>
        <div class="tags"><?php print $categories; ?></div>
      <?php endif; ?>   
    </div>
    <?php if (!empty($content['field_body'])): ?>
      <div class="field-bio">
        <?php print render($content['field_body']); ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($content['field_lien_visionnage_du_livre']) || !empty($content['field_lien_fnac'])): ?>
      <div class="info related-links related-links-bottom">
        <?php if (!empty($content['field_lien_visionnage_du_livre'])): ?>
          <?php print render($content['field_lien_visionnage_du_livre']); ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php if (!empty($bloc_personnalites)): ?>
  <?php print render($bloc_personnalites); ?>
<?php endif; ?>