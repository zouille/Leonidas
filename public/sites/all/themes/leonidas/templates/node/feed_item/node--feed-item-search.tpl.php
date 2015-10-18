<li>
  <?php if (!empty($content['field_image'])): ?>
    <div class="img">
      <?php print render($content['field_image']); ?>
    </div>
  <?php endif; ?>
  <div class="text">
    <?php if (!empty($category)): ?>
      <span class="trends">
        <?php print ' ' . $category; ?>
      </span>
    <?php endif; ?>
    <?php if (!empty($title)): ?>
      <h3><?php print $title; ?></h3>
    <?php endif; ?>
    <?php if (!empty($content['field_description'])): ?>
      <p><?php print render($content['field_description']); ?></p>
    <?php endif; ?>
  </div>
</li>