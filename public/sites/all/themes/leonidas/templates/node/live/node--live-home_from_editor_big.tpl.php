<?php if (!$vid_switch): ?>
  <div class="<?php print $classes; ?> no-video-icons clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
    <div class="top-video">
      <?php if (!$emission_section): ?>
        <h1>Live <span>La Une</span></h1>
      <?php endif; ?>
      <div class="video">
        <?php if (!empty($media)): ?>
          <?php echo $media; ?>
        <?php endif; ?>
        <?php if (!empty($live_status)): ?>
          <?php if (!empty($counter)): ?>
            <div class="counter" id="countdown"></div>
          <?php else: ?>
            <?php print $live_status; ?>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($content['field_live_catchline_une_title'])): ?>
          <?php $accroche = $content['field_live_catchline_une_title']; ?>
        <?php elseif (!empty($content['field_live_catchline_title'])): ?>
          <?php $accroche = $content['field_live_catchline_title']; ?>
        <?php endif; ?>
        <?php if (!empty($accroche)): ?>
          <div class="text-info">
            <p> <?php echo render($accroche); ?> </p>
          </div>
        <?php endif; ?>
        <div class="line">
          <?php if (!empty($more_button)): ?>
            <div class="box">
              <?php echo $more_button; ?>
            </div>
          <?php endif; ?>
          <div class="frame">
            <?php if (!empty($title_link)): ?>
              <h2><?php echo $title_link; ?></h2>
            <?php endif; ?>
            <div class="row">
              <?php if (!empty($main_thematic)): ?>
                <span class="soul">
                  <?php if (!empty($channel)): ?>
                    <img class="small-img" src="<?php print ($GLOBALS['base_path'] . CULTUREBOX_THEME_PATH . '/images/little-' . $channel . '.png'); ?>" alt="<?php print $channel; ?>">
                  <?php endif; ?>
                  <?php echo $main_thematic; ?>
                </span>
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
<?php else: ?>
  <div class="top-video">
    <?php if (!$emission_section): ?>
      <h1>Concerts et spectacles <span>en live</span></h1>
    <?php endif; ?>
    <div class="video player">
      <div class="line">
        <div class="frame player">
          <?php if (!empty($title_link)): ?>
            <h2><?php echo $title_link; ?></h2>
          <?php endif; ?>
          <?php if (!empty($content['field_live_catchline_une_title'])): ?>
            <?php $accroche = $content['field_live_catchline_une_title']; ?>
          <?php elseif (!empty($content['field_live_catchline_title'])): ?>
            <?php $accroche = $content['field_live_catchline_title']; ?>
          <?php endif; ?>
          <?php if (!empty($accroche)): ?>
            <div class="text-info">
              <p> <?php echo render($accroche); ?> </p>
              <span class="open-share player">
            <a class="share-button fb" target="_blank" href="http://www.facebook.com/sharer.php?u=<?php print $custom_share_url; ?>&t=<?php print $custom_share_text; ?>"><span id="cb-fixed-header-inner-right-fb"></span></a>
            <a class="share-button tw" target="_blank" href="http://twitter.com/share?url=<?php print $custom_share_url; ?>&text=<?php print $custom_share_text; ?>&via=Culturebox"><span id="cb-fixed-header-inner-right-tw"></span></a>
          </span>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <?php if (!empty($player_link)): ?>
        <?php print $player_link; ?>
        <?php if (!empty($player_script)): ?>
          <?php print $player_script; ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
