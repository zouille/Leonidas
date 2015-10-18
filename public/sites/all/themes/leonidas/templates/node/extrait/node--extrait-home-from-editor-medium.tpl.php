<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($media)): ?>
    <div class="img">
      <?php echo $media; ?>
    </div>
  <?php endif; ?>
  <div class="infos-text">
    <?php if (!empty($tags)): ?>
      <span class="tags">
        <?php echo $tags; ?>
      </span>
    <?php endif; ?>
    <?php if (!empty($title)): ?>
      <div class="h3"><?php echo $title; ?></div>
    <?php endif; ?>
    <?php if (!empty($content['field_body'])): ?>
      <p><?php print render($content['field_body']); ?></p>
    <?php endif; ?>
    <?php if (!empty($know_more)): ?>
      <?php print render($know_more); ?>
    <?php endif; ?>
  </div>
</div>

