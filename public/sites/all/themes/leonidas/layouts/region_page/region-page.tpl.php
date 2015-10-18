<?php
/**
 * @file
 * Template for a culturebox home page layout.
 */
?>
<?php if (!empty($content['head'])): ?>
  <div class="theme-head">
   <?php print $content['head']; ?>
  </div>
<?php endif; ?>
<section id="main"<?php if ($is_region): ?>class="theme-holder"<?php endif; ?>>
  <article id="content">
    <?php print $content['top_left']; ?>
  </article>
  <aside>
    <?php print $content['top_right']; ?>
  </aside>
</section>
