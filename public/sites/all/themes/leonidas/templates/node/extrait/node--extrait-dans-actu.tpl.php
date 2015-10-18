<div class="content extrait-dans-actu">
  <div class="img">
    <?php print render($image); ?>
    <?php if ($have_video): ?>
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