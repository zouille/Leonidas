<?php if (!$vid_switch) : ?>
  <div class="top-video">
    <?php if (!empty($media)): ?>
      <div class="holder">
        <?php echo $media; ?>
        <?php if (!empty($live_status)): ?>
          <div class="no-video-icons bientot-direct bientot-direct-big">
            <?php echo $live_status; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($tags)): ?>
      <span class="trends">
      <?php if (!empty($channel)): ?>
        <img class="small-img"
             src="<?php print ($GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . $channel . '.png'); ?>"
             alt="<?php print $channel; ?>">
      <?php endif; ?>
        <?php print $tags; ?>
    </span>
    <?php endif; ?>
    <?php if (!empty($title)): ?>
    <div class="h3"><?php print $title; ?></div>
    <?php endif; ?>
  </div>
<?php else : ?>
  <div class="top-video">
    <div class="holder">
      <?php if (!empty($player_link)): ?>
        <?php print $player_link; ?>
        <?php if (!empty($player_script)): ?>
          <?php print $player_script; ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php if (!empty($tags)): ?>
      <span class="trends">
        <?php print $tags; ?>
    </span>
    <?php endif; ?>
    <?php if (!empty($title)): ?>
      <div class="h3"><?php print $title; ?></div>
    <?php endif; ?>
  </div>
<?php endif; ?>
