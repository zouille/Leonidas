<?php if (!empty($content['above_section'])): ?>
  <?php print $content['above_section']; ?>
<?php endif; ?>
<section id="main">
  <?php if (!empty($content['left_top']) || !empty($content['left_middle']) || !empty($content['left_bottom'])): ?>
    <article id="content"<?php if (!empty($classes)) print ' class="' . $classes . ((!empty($renderer->display->context['argument_entity_id:node_1']->data->type) && $renderer->display->context['argument_entity_id:node_1']->data->type == 'article') ? ' article' : '') . '"'; ?><?php if (!empty($renderer->display->context['argument_entity_id:node_1']->data->type) && $renderer->display->context['argument_entity_id:node_1']->data->type == 'article'): ?> itemscope itemtype="http://schema.org/Article"<?php endif; ?>>
      <?php print $content['left_top']; ?>
      <?php if (!empty($content['left_middle'])): ?>
        <div class="panel">
          <?php print $content['left_middle']; ?>
        </div>
      <?php endif; ?>
      <?php print $content['left_bottom']; ?>
    </article>
  <?php endif; ?>
  <!-- content end -->

  <?php if (!empty($content['right'])): ?>
    <aside>
      <?php print $content['right']; ?>
    </aside>
  <?php endif; ?>
  <!-- aside end -->
</section>
