<li class="black">
  <?php if (!empty($image)): ?>
    <div class="img">
      <?php print $image; ?>
      <?php if (!empty($live_status)): ?>
        <?php print $live_status; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="text">
    <?php if (!empty($category)): ?>
      <span class="trends">
        <?php print $category; ?>
      </span>
    <?php endif; ?>
    <?php if (!empty($node->title)): ?>
      <h3><?php print culturebox_site_l($node->title, "node/$node->nid"); ?></h3>
    <?php endif; ?>
    <?php if (!empty($content['field_body'])): ?>
      <p><?php print assets_cut_filter_process(render($content['field_body'])); ?></p>
    <?php endif; ?>
  </div>
</li>