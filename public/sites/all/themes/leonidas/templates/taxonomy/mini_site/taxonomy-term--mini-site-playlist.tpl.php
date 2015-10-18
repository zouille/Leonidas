<li class="search-result-event">
  <?php if (!empty($event_image)): ?>
    <div class="img">
      <?php print $event_image; ?>
    </div>
  <?php endif; ?>
  <div class="text">
    <?php if (!empty($event_date)): ?>
      <span class="trends">
        <?php print "$event_date"; ?>
      </span>
    <?php endif; ?>
    <?php if (!empty($term_name)): ?>
      <h3><?php print $term_name; ?></h3>
    <?php endif; ?>
    <?php if (!empty($term->description)): ?>
      <p><?php print truncate_utf8(strip_tags(assets_cut_filter_process($term->description)), 350, TRUE, TRUE); ?></p>
    <?php endif; ?>
  </div>
</li>
