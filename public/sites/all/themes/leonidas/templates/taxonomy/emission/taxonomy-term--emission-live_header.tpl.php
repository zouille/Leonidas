<div class="holder">
  <?php if (!empty($emission_image)): ?>
    <div class="big-img">
      <?php print $emission_image; ?>
    </div>
  <?php endif; ?>
  <div class="block<?php print (empty($emission_image) ? ' em-img' : ''); ?>">
    <?php if (!empty($content['field_emission_sub_title_web'])): ?>
      <div class="sub-title-pres">
        <?php print render($content['field_emission_sub_title_web']); ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($text_short)): ?>
      <div class="desc show"><?php print $text_short; ?></div>
    <?php endif; ?>
    <?php print l('<span class="picto"></span><span class=txt>Lire la suite</span>', 'taxonomy/term/' . $term->tid, array('html' => TRUE, 'attributes' => array('class' => 'rd-more'))); ?>
  </div>
</div>
