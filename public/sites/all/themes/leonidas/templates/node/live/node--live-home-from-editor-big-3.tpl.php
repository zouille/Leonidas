<div class="<?php print $classes; ?> no-video-icons clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <div class="top-video">
    <div class="video">
      <?php if (!empty($media)): ?>
        <?php echo $media; ?>
      <?php endif; ?>
      <!-- Status block -->
      <?php if (!empty($live_status)): ?>
        <?php if (!empty($counter)): ?>
          <div class="counter" id="countdown"></div>
        <?php else: ?>
          <?php print $live_status; ?>
        <?php endif; ?>
      <?php endif; ?>
      <!-- Status block end -->
      <?php if (!empty($content['field_live_catchline_une_title'])): ?>
        <?php $accroche = $content['field_live_catchline_une_title']; ?>
      <?php elseif (!empty($content['field_live_catchline_title'])): ?>
        <?php $accroche = $content['field_live_catchline_title']; ?>
      <?php endif; ?>
      <?php if (!empty($accroche)): ?>
        <div class="text-info">
          <p>“ <?php echo render($accroche); ?> ”</p>
        </div>
      <?php endif; ?>
      <div class="line">
        <div class="frame">
          <h2><?php echo $title_link; ?></h2>
          <div class="row">
            <?php if (!empty($main_thematic)): ?>
              <span class="soul"><?php echo $main_thematic; ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
