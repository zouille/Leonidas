<div class="<?php print $classes; ?> no-video-icons clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <div class="top-video">
    <div class="video">
      <?php if (!empty($media)): ?>
        <?php echo $media; ?>
      <?php endif; ?>
      <div class="line">
        <?php if (!empty($more_button)): ?>
          <div class="box">
            <?php echo $more_button; ?>
          </div>
        <?php endif; ?>
        <div class="frame">   
          <h2><?php echo $title_link; ?></h2>
          <div class="row">
            <?php if (!empty($main_thematic)): ?>
              <span class="soul"><?php echo $main_thematic; ?></span>
            <?php endif; ?>
            <?php if (!empty($live_start_date)): ?>
              <span class="date">
                <?php echo $live_start_date; ?>
              </span>
            <?php endif; ?>
            <?php if (!empty($reagir)): ?>
              <span class="react">
                <?php print $reagir; ?>
              </span>
            <?php endif; ?>
            <div class="inform share-link">
            </div>
            <?php if (!empty($share_links)): ?>
              <?php echo $share_links; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
