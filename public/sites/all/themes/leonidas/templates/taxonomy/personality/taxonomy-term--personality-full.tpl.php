<div class="first-element first-element-personality" itemscope itemtype="http://schema.org/Person">
  <?php if (!empty($term_name)): ?>
    <h1 itemprop="name"><?php print $term_name; ?></h1>
  <?php endif; ?>
  <?php if (!empty($category)): ?>
    <strong class="tag">
      <?php print $category; ?>
    </strong>
  <?php endif; ?>
  <div class="holder">
    <?php if (!empty($content['field_picture'])): ?>
      <div class="img">
        <?php if (!empty($microdata_image)): ?>
          <?php print $microdata_image; ?>
        <?php endif; ?>
        <?php print render($content['field_picture']); ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($content['field_function']) || !empty($content['field_emission_twitter']) || !empty($content['field_emission_instagram'])): ?>
      <div class="info related-links">
        <?php if (!empty($content['field_function'])): ?>
          <strong class="date"><?php print render($content['field_function']); ?></strong>
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
    <?php endif; ?>
    <?php print drupal_render($content['description']); ?>
    <div class="articles-et-lives-peronnalite">
      <?php if (!empty($content['field_emission_team_int_article'])): ?>
        <?php foreach ($content['field_emission_team_int_article']['#items'] as $key => $article): ?>
          <div class="item-lie"><?php $node_view = node_view($article['entity'], 'small'); print render($node_view); ?></div>
        <?php endforeach; ?>
      <?php endif; ?>
      <?php if (!empty($content['field_emission_team_int_live'])): ?>
        <?php foreach ($content['field_emission_team_int_live']['#items'] as $key => $article): ?>
          <div class="item-lie live"><?php $node_view = node_view($article['entity'], 'slider'); print render($node_view); ?></div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
