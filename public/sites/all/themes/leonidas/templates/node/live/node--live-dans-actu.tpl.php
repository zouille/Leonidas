<div class="content extrait-dans-actu">
  <div class="img">
    <?php if (!empty($image)) : ?>
      <?php print render($image); ?>
      <div class="fleche-play play-small"></div>
    <?php endif; ?>
  </div>
  <div class="text">
    <?php if (!empty($category)): ?>
      <span><?php print $category; ?></span>
    <?php endif; ?>
    <p><?php print culturebox_site_l($node->title, 'node/' . $node->nid); ?></p>
  </div>
</div>
