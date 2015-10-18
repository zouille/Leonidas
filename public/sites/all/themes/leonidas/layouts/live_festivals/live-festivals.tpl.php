<?php
/**
 * @file
 * Template for a culturebox live festivals page layout.
 */
?>
<section id="main">
  <?php if (!empty($content['top_live'])): ?>
  <div class="festival-container">
    <?php print $content['top_live']; ?>
  </div>
  <?php endif; ?>
  <div class="festival-holder">
    <div class="content-block">
      <?php print $content['live_left']; ?>
    </div>
    <div class="sidebar">
      <?php print $content['live_sidebar']; ?>
    </div>
  </div>
</section>
