<?php if (!empty($content['above_section'])): ?>
  <?php print $content['above_section']; ?>
<?php endif; ?>
<section id="main" itemscope itemtype="http://schema.org/TVSeries">
  <?php if (!empty($content['header'])): ?>
    <header>
      <?php print $content['header']; ?>
      <?php if (!empty($content['header_bottom'])): ?>
        <div id="header-bottom">
          <div class="header-bottom-wrapper clearfix">
            <?php print $content['header_bottom']; ?>
          </div>
        </div>
      <?php endif; ?>
    </header>
  <?php endif; ?>
  <section id="bottom-main">
    <?php if (!empty($content['left']) || !empty($content['left_top'])): ?>
      <div id="content"<?php if (!empty($classes)) print ' class="' . $classes . '"'; ?>>
        <?php if (!empty($content['left_top'])): ?>
          <div class="panel">
            <?php print $content['left_top']; ?>
          </div>
        <?php endif; ?>
        <?php print $content['left']; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($content['right'])): ?>
      <aside>
        <?php print $content['right']; ?>
      </aside>
    <?php endif; ?>
    <?php if (!empty($content['footer'])): ?>
      <footer>
        <?php print $content['footer']; ?>
      </footer>
    <?php endif; ?>
  </section>
</section>
