<div class="<?php print $classes; ?> no-video-icons clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <div class="top-video">
    <div class="video">
      <?php if (!empty($media)): ?>
        <?php echo $media; ?>
      <?php endif; ?>
      <!-- Status block -->
      <?php if (!empty($live_status)): ?>
        <?php print $live_status; ?>
      <?php endif; ?>
      <!-- Status block end -->
      <?php if (!empty($content['field_fe_chapo'])):
        ?>
        <div class="text-info">
          <p>“ <?php echo render($content['field_fe_chapo']); ?> ”</p>
        </div>
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
            <?php if (!empty($name_emission)): ?>
              <span class="soul"><?php echo $name_emission; ?></span>
            <?php endif; ?>
            <?php if (!empty($live_start_date)): ?>
              <span class="date<?php print (empty($main_thematic) ? ' no-category' : ''); ?>">
                <?php echo $live_start_date; ?>
              </span>
            <?php endif; ?>
            <?php if (!empty($reagir)): ?>
              <span class="react">
                <?php print $reagir; ?>
              </span>
            <?php endif; ?>
            <div class="inform share-link">
              <?php if (!empty($inform_popup)): ?>
                <span class="inform-link">M'avertir</span>
                <?php print $inform_popup; ?>
              <?php endif; ?>
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
