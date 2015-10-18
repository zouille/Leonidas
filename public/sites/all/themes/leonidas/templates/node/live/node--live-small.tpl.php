<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php if (!empty($content['field_live_media'])): ?>
    <div class="img">
      <a href="<?php print url('node/' . $node->nid); ?>"<?php if (!empty($node->cb_festival_player)): ?> target="_blank"<?php endif; ?>>
        <?php print render($content['field_live_media']); ?>
      </a>
    </div>
  <?php endif; ?>
  <?php if (!empty($title)): ?>
    <div class="text">
      <a href="<?php print url('node/' . $node->nid); ?>"<?php if (!empty($node->cb_festival_player)): ?> target="_blank"<?php endif; ?>>
        <p><?php print $date; ?><p>
          <strong><?php print $title; ?></strong>
      </a>
    </div>
  <?php endif; ?>
</div>
