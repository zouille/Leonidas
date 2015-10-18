<?php
/**
 * @file
 * Template for a culturebox home page layout.
 */
?>
<section id="main">
  <?php print $content['top_live']; ?>

  <div class="main-holder">
    <?php print $content['middle_agenda']; ?>
  </div>
  <div class="main-holder">
    <div class="content-block">
      <?php print $content['middle_festivals_left']; ?>
    </div>
    <?php print $content['middle_festivals_sidebar']; ?>
  </div>
  <div class="main-holder">
    <?php print $content['middle_festivals']; ?>
  </div>
  <div class="main-block">
    <?php print $content['bottom_festivals']; ?>
  </div>
  <div class="main-holder">
    <div class="content-block">
      <?php print $content['bottom_left']; ?>
    </div>
    <?php print $content['bottom_sidebar']; ?>
  </div>
</section>
