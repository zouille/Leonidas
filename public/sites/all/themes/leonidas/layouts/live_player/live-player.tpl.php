<?php if (!empty($content['live_top'])): ?>
  <div class="video-container">
    <?php print $content['live_top']; ?>
  </div>
<?php endif; ?>
<section id="main">
  <div class="player-holder">
    <?php if (!empty($content['live_content'])): ?>
      <section class="content-block">
        <?php print $content['live_content']; ?>
      </section>
    <?php endif; ?>
    <?php if (!empty($content['live_sidebar'])): ?>
      <aside class="sidebar">
        <?php print $content['live_sidebar']; ?>
      </aside>
    <?php endif; ?>
  </div>
</section>

