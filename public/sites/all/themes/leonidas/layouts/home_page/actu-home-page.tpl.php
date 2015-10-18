<?php
/**
 * @file
 * Template for a culturebox home page layout.
 */
?>

<?php print $content['head']?>


<section id="main"<?php if ($is_category_page): ?>class="theme-holder"<?php endif; ?>>
  <article id="content">
    <?php print $content['top_left']; ?>
  </article>
  <aside>
    <?php print $content['top_right']; ?>
  </aside>
</section>
<?php print $content['above_middle_top']; ?>
<?php if (!empty($content['middle_top_left']) || !empty($content['middle_top_right'])): ?>
  <div class="main-holder">
    <?php if (!empty($content['middle_top_left'])): ?>
      <div class="content-block">
        <div class="main-block">
          <?php print $content['middle_top_left']; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if (!empty($content['middle_top_right'])): ?>
      <div class="side-block">
        <?php print $content['middle_top_right']; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php if (!empty($content['middle_middle_bottom_one']) || !empty($content['middle_middle_bottom_two']) || !empty($content['middle_middle_bottom_three'])): ?>
    <div class="three-column dmdm">
      <?php foreach (array($content['middle_middle_bottom_one'], $content['middle_middle_bottom_two'], $content['middle_middle_bottom_three']) as $key => $col_content): ?>
        <?php if (!empty($col_content)): ?>
          <div class="column310<?php if ($key == 0): ?> alpha<?php endif; ?>">
            <?php print $col_content; ?>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($content['middle_middle'])): ?>
  <div class="tabs">
    <?php print $content['middle_middle']; ?>
  </div>
<?php endif; ?>

<?php if (!empty($content['middle_bottom'])): ?>
  <div class="clearfix">
    <?php print $content['middle_bottom']; ?>
  </div>
<?php endif; ?>
