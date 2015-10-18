<?php
/**
 * @file
 * Template for layout page
 */
?>
<div id="art-diapo" class="top-main-block">
  <?php if (!empty($content['above_section'])): ?>
    <?php print $content['above_section']; ?>
  <?php endif; ?>
  <?php if(!empty($content['top'])):?>
    <div class="panel-pad-diapo"> 
      <div class="panel">
        <?php print $content['top']; ?>
      </div>
    </div>
  <?php endif;?>
  <div>
    <?php if (!empty($content['top']) || !empty($content['top_left']) || !empty($content['top_right']) || !empty($content['top_bottom'])): ?>
      <?php if (!empty($content['middle'])): ?>
        <?php print $content['middle']; ?>
      <?php endif; ?>
    <?php endif; ?>
    <?php if (!empty($content['bottom'])): ?>
      <?php print $content['bottom']; ?>
    <?php endif; ?>
  </div>
</div>
<section id="main">
  <?php if (!empty($content['footer_left'])): ?>
    <article id="content" class="article">
      <?php print $content['footer_left']; ?>
    </article>
  <?php endif; ?>

  <?php if (!empty($content['footer_right'])): ?>
    <aside>
      <?php print $content['footer_right']; ?>
    </aside>
  <?php endif; ?>
</section>