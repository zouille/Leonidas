<?php if (!empty($content)): ?>
  <div class="group-team-all-infos" itemprop="actor" itemscope itemtype="http://schema.org/Person">	
    <?php if (!empty($content['field_picture'])): ?>
      <?php if (!empty($microdata_image)): ?>
        <?php print $microdata_image; ?>
      <?php endif; ?>
      <?php print render($content['field_picture']); ?>
    <?php endif; ?>
    <fieldset class="infos-teamate first-element first-element-personality">
      <div class="field-nom" itemprop="name"><?php print $term->name; ?></div>
      <div class="info related-links">
        <?php if (!empty($content['field_function'])): ?>
          <div class="field-fonction"><strong class="date"><?php print render($content['field_function']); ?></strong></div>
        <?php endif; ?>		
          <?php if (!empty($personnality_link)): ?>
            <?php print $personnality_link; ?>
          <?php endif; ?>
        <?php if (!empty($content['field_facebook'])): ?>
          <?php print render($content['field_facebook']); ?>
        <?php endif; ?>		
        <?php if (!empty($content['field_emission_twitter'])): ?>
          <?php print render($content['field_emission_twitter']); ?>
        <?php endif; ?>		
        <?php if (!empty($content['field_emission_instagram'])): ?>
          <?php print render($content['field_emission_instagram']); ?>
        <?php endif; ?>
      </div>
      <?php if (!empty($description)): ?>
        <div class="field-bio">
          <?php print $description; ?>
        </div>
        <?php if (!empty($expander)): ?>
          <?php print $expander; ?>
        <?php endif; ?>
      <?php endif; ?>
    </fieldset>
    <div class="articles-et-lives">
      <?php if (!empty($content['field_emission_team_int_article'])): ?>
        <?php foreach ($content['field_emission_team_int_article']['#items'] as $key => $article): ?>
          <div class="item-lie"><?php print render(node_view($article['entity'], 'small')); ?></div>
        <?php endforeach; ?>
      <?php endif; ?>
      <?php if (!empty($content['field_emission_team_int_live'])): ?>
        <?php foreach ($content['field_emission_team_int_live']['#items'] as $key => $article): ?>
          <div class="item-lie live"><?php print render(node_view($article['entity'], 'slider')); ?></div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>