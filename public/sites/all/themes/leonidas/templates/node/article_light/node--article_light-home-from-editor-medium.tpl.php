<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($media)): ?>
    <div class="img">
      <?php echo $media; ?>
      <?php if (!empty($live_status)): ?>
        <?php echo $live_status; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="infos-text">
    <?php if (!empty($tags)): ?>
      <span class="tags">
        <?php echo $tags; ?>
      </span>
    <?php endif; ?>
    <?php if (!empty($title)): ?>
      <h3><?php echo $title; ?></h3>
    <?php endif; ?>
    <?php if (!empty($content['field_fe_chapo'])): ?>
      <p><?php print render($content['field_fe_chapo']); ?></p>
    <?php endif; ?>
    <?php if (!empty($know_more)): ?>
      <?php print render($know_more); ?>
    <?php endif; ?>
  </div>
</div>

