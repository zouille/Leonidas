<?php
/**
 * @file
 * Template for a culturebox home page layout.
 */
?>
<section id="main">
  <?php print $content['top_live']; ?>
  <div class="main-holder">
    <div class="content-block">
      <?php print $content['middle_live_left']; ?>
    </div>
    <?php print $content['middle_live_sidebar']; ?>
  </div>
  <?php print $content['bottom_evenements']; ?>
  <div class="derniers-lives ">
    <div class="ov">
      <div class="content-block">
        <?php print $content['derniers_search']; ?>
        <?php print $content['derniers_list']; ?>
      </div>
      <?php print $content['derniers_agenda']; ?>
    </div>
  </div>
  <?php print $content['three_column']; ?>
</section>
