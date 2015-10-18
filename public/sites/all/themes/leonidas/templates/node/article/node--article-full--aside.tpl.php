<aside class="aside">
  <?php if (!empty($live)): ?>
    <div id="node-bonus-live" class="box live-page clearfix">
      <h3><?php print $live_title; ?></h3>
      <?php print render($live); ?>
    </div>
  <?php elseif (!empty($lives)): ?>
    <div id="node-bonus-live" class="box live-page clearfix">
      <h3>Voir les vidéos</h3>
      <ul class="derniers-lives-list">
        <?php foreach ($lives as $live): ?>
          <li><?php print render($live); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  <?php if (!empty($emission)): ?>
    <div id="node-bonus-extrait-diff" class="box live-page">
      <h3><?php print $emission_title; ?></h3>
      <?php print render($emission); ?>
    </div>
  <?php elseif (!empty($emissions)): ?>
    <div id="node-bonus-extrait-diff" class="box emission-content-related">
      <h3>Voir les vidéos</h3>
      <ul class="article-list">
        <?php foreach ($emissions as $emission): ?>
          <li><?php print render($emission); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  <?php if (!empty($recommend)): ?>
    <?php print $recommend; ?>
    <?php if (!empty($related_articles)): ?>
      <div class="box box-fusion">
        <ul class="more-list">
          <?php foreach ($related_articles as $related_article): ?>
            <li><?php print $related_article; ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  <?php if (!empty($related_articles) && empty($recommend)): ?>
    <div class="box">
      <?php if (!(!empty($node->field_pilier_associe[LANGUAGE_NONE][0]['value']) && $node->field_pilier_associe[LANGUAGE_NONE][0]['value'] == 'live')): ?>
      <div class="h3">La rédaction vous recommande</div>
      <?php else: ?>
      <div class="h3">Sur le même sujet</div>
      <?php endif; ?>
      <ul class="more-list">
        <?php foreach ($related_articles as $related_article): ?>
          <li><?php print $related_article; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  <?php if (!empty($events)): ?>
    <?php foreach ($events as $event): ?>
      <div class="box">
        <h3><?php print $event['title']; ?></h3>
        <ul class="more-list">
          <?php foreach ($event['nodes'] as $event_node): ?>
            <li><?php print l($event_node->title, "node/{$event_node->nid}"); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
  <?php if (!empty($thematics) || !empty($peoples) || !empty($department_links)): ?>
    <div class="box">
      <ul class="aside-tags">
        <?php foreach (array('thematics', 'peoples', 'department_links') as $tag_type): ?>
          <?php if (!empty(${$tag_type})): ?>
            <?php foreach (${$tag_type} as $tag): ?>
              <li class="<?php print $tag_type; ?>"><?php print $tag; ?></li>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</aside>