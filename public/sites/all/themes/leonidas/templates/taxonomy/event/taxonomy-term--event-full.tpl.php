<div class="first-element" itemscope itemtype="http://schema.org/Event">
  <?php if (!empty($term_name)): ?>
    <h1 itemprop="name"><span><?php print $term_name; ?></span></h1>
  <?php endif; ?>
  <strong class="tag">
    <?php if (!empty($categories)): ?>
      <?php print $categories; ?>
    <?php endif; ?>
    <?php if (!empty($localizations)): ?>
      <?php foreach ($localizations as $localization): ?>
        <span><?php print $localization; ?></span>
      <?php endforeach; ?>
    <?php endif; ?>
  </strong>
  <div class="holder">
    <?php if (!empty($content['field_illustration'])): ?>
      <div class="img">
        <?php if (!empty($microdata_image)): ?>
          <?php print $microdata_image; ?>
        <?php endif; ?>
        <?php print render($content['field_illustration']); ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($date) || !empty($links)): ?>
      <div class="info">
        <?php if (!empty($date)): ?>
          <strong class="date"><?php print $date; ?></strong>
        <?php endif; ?>
        <?php if (!empty($links)): ?>
          <?php print $links; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <?php print drupal_render($content['description']); ?>
    <?php if (!empty($persons)): ?>
      <h5>Personnalités liées</h5>
      <ul class="more-list-event">
        <?php foreach ($persons as $person): ?>
          <li><?php print $person; ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>
