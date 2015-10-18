<div class="first-element" itemscope itemtype="http://schema.org/Event">
  <?php if (!empty($term_name)): ?>
    <h1 itemprop="name"><span><?php print $term_name; ?></span></h1>
  <?php endif; ?>
  <div class="holder">
    <?php if (!empty($content['field_illustration'])): ?>
      <div class="img">
        <?php if (!empty($microdata_image)): ?>
          <?php print $microdata_image; ?>
        <?php endif; ?>
        <?php print render($content['field_illustration']); ?>
      </div>
    <?php endif; ?>
    <?php print drupal_render($content['description']); ?>
  </div>
</div>
