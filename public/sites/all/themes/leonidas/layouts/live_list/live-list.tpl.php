<?php
/**
 * @file
 * Template for a culturebox live list page layout.
 */
?>
<div class="theme-head">
  <div class="ttl">
    <?php print $content['top_live']; ?>
  </div>
</div>
<section id="main">
  <div class="concert-holder">
    <div class="content-block">
      <?php print $content['live_left']; ?>
    </div>
    <div class="sidebar">
      <?php print $content['live_sidebar']; ?>
    </div>
  </div>
</section>
<?php print $content['live_bottom']; ?>
