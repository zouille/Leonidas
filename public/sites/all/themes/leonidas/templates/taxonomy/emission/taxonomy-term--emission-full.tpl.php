<?php if (!empty($content['description'])): ?>
  <div class="content-block presentation-emission">
    <h2>Présentation de l'émission</h2>
    <?php if (!empty($content['field_emission_sub_title_web'])): ?>
      <div class="sub-title-pres">
        <?php print render($content['field_emission_sub_title_web']); ?>
      </div>
    <?php endif; ?>
    <?php if ($text_short != $text_long) : ?>
      <div class="desc show"><?php print $text_short; ?></div>
      <div class="desc hide" itemprop="description"><?php print $text_long; ?></div>
      <span class="emission read-more"><span class="picto"></span><span class="txt">Lire la suite</span></span>
      <?php else: ?>
        <div class="desc-crop" itemprop="description"><?php print $content['description']['#markup']; ?></div>
      <?php endif; ?>
  </div>
<?php else: ?>
  <?php // Fix to not display unthemed fields if description of term is not set. ?>
  <?php print '&nbsp;' ?>
<?php endif; ?>