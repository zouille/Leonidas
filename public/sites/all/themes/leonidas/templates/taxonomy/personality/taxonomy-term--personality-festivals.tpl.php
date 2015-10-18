<?php if (!empty($content)): ?>
<?php if ($is_linkable): ?>
  <?php print culturebox_site_l($term->name, 'taxonomy/term/' . $term->tid, array('attributes' => array('class' => 'bloc-lien', 'alt' => 'Voir la fiche auteur', 'title' => 'Voir la fiche auteur'))); ?>
<?php endif; ?>
  <div id="personality-festivals" class="group-team-all-infos clearfix">
    <?php if (!empty($content['field_picture'])): ?>
      <?php print render($content['field_picture']); ?>
    <?php endif; ?>
    <fieldset class="infos-teamate first-element first-element-personality">
      <div class="field-nom"><?php if ($is_linkable): ?><?php print culturebox_site_l($term->name, 'taxonomy/term/' . $term->tid); ?><?php else: ?><?php print $term->name; ?><?php endif; ?></div>
      <div class="info related-links">
        <?php if (!empty($content['field_function'])): ?>
          <div class="field-fonction field-auteurs"><?php print render($content['field_function']); ?></div>
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