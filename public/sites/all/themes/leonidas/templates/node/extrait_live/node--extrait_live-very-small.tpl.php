<div id="ar-node-extrait-live-very-small">
  <div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> no-video-icons clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
    <?php if (!empty($content['field_extrait_video'])): ?>
      <?php print render($content['field_extrait_video']); ?>
    <?php endif; ?>
  </div>
</div>
