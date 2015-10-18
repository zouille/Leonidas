<li>
  <?php if (!empty($image)): ?>
    <div class="img">
      <?php print $image; ?>
    </div>
  <?php endif; ?>
  <div class="text">
    <span class="trends">
      <?php if (!empty($category)): ?>
        <?php print $category; ?>
      <?php endif; ?>
    </span>
    <?php if (!empty($term_name)): ?>
      <h3><?php print $term_name; ?></h3>
    <?php endif; ?>
    <?php if (!empty($description)): ?>
      <?php print $description; ?>
    <?php endif; ?>
  </div>
</li>