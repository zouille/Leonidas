<?php if (!empty($content)): ?>
  <?php print culturebox_site_l($node->title, 'node/' . $node->nid, array('attributes' => array('class' => 'bloc-lien', 'alt' => 'Voir la fiche livre', 'title' => 'Voir la fiche livre'))); ?>
  <div id="personality-festivals" class="group-team-all-infos group-team-all-infos-book clearfix">
    <?php if (!empty($content['field_illustration'])): ?>
      <?php print render($content['field_illustration']); ?>
    <?php endif; ?>
    <fieldset class="infos-teamate first-element first-element-personality">
      <div class="field-nom"><?php print culturebox_site_l($node->title, 'node/' . $node->nid); ?></div>
      <div class="info related-links">
        <?php if (!empty($auteurs)): ?>
          <div class="field-fonction field-auteurs">
            <?php print $auteurs; ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($editeur) || !empty($annee_edition)): ?>
          <div class="field-fonction">
            <?php if (!empty($editeur)): ?>
              <?php print $editeur; ?>
            <?php endif; ?>
            <?php if (!empty($annee_edition)): ?>
              <?php print (!empty($editeur) ? '/ ' : '') . $annee_edition; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
      <?php if (!empty($description)): ?>
        <div class="field-bio">
          <?php print $description; ?>
        </div>
      <?php endif; ?>
    </fieldset>
  </div>
<?php endif; ?>