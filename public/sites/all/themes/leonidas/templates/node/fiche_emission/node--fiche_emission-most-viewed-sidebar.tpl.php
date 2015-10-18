<?php $page = panels_get_current_page_display(); ?>
<?php if ($page->context['context_emission_home_page_1']->title !== "emission_home_page_context"): ?>
  <!--l--><div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
    <?php if (!empty($image)): ?>
      <div class="img">
        <?php print $image; ?>
        <?php if (!empty($live_status)): ?>
          <?php print $live_status; ?>
        <?php endif; ?>
        <?php if (!empty($num)): ?>
          <span class="num">
            <?php print $num; ?>
          </span>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($title)): ?>
      <div class="text">
        <?php if (!empty($name_emission)) : ?>
          <span><?php print $name_emission; ?></span>
        <?php endif ?>
        <p><?php print culturebox_site_l($node->title, "node/$node->nid"); ?></p>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <!--l--><div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
    <?php if (!empty($image)): ?>
      <div class="img">
        <?php print $image; ?>
        <?php if (!empty($live_status)): ?>
          <?php print $live_status; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <img class="logo-channel" src="<?php print ($GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . "$channel" . '.png'); ?>" alt="<?php print" le nom de la chaine"; ?>">
    <span class="date-f-emission"><?php echo $date; ?>
      <?php if (!empty($title)): ?>
        <div class="text">
          <?php if (!empty($title)) : ?>
            <p><?php print culturebox_site_l($node->title, "node/$node->nid"); ?></p>
          <?php endif ?>
        </div>
      <?php endif; ?>
  </div>
<?php endif; ?>
