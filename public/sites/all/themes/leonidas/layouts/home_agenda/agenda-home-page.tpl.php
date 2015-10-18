<?php
/**
 * @file
 * Template for a culturebox home page layout.
 */
?>
<?php if (!empty($content['top'])): ?>
  <section id="main" class="ov">
    <div class="main-block">
      <?php print $content['top']; ?>
    </div>
  </section>
<?php endif; ?>
<?php if (!empty($content['middle_left']) || !empty($content['middle_right'])): ?>
  <?php if (!empty($content['middle_left'])): ?>
    <div class="main-holder main-holder-agenda">
      <div class="content-block">
        <div class="main-block">
          <?php print $content['middle_left']; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if (!empty($content['middle_right'])): ?>
      <div class="side-block">
        <?php print $content['middle_right']; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php if (!empty($content['bottom'])): ?>
  <div class="tabs tabs2">
    <?php print $content['bottom']; ?>
  </div>
<?php endif; ?>
