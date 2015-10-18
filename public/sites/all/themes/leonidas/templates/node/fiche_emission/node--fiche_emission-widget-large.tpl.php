<div id="ar-node-fiche-emission-widget-large">
  <div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> no-video-icons clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
    <div class="stream">
      <div class="title">
        <?php if (!empty($live_status)): ?>
          <span class="labelstatus <?php print $label_class; ?>"><?php print $live_status; ?></span>
        <?php endif; ?>
        <span><?php print culturebox_site_l($node->title, "node/$node->nid", array('attributes' => array('target' => '_blank'))); ?></span>
      </div>
      <div class="mom">
        <?php if (!empty($player_link)): ?>
          <?php print $player_link; ?>
          <?php if (!empty($player_script)): ?>
            <?php print $player_script; ?>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($trailer)): ?>
          <div class="trailer" style="display: none">
            <?php print $trailer; ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($image)): ?>
          <div class="live-image small">
            <?php print $image; ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
          <div class="delay-message overlay">
            <table>
              <tbody>
                <tr>
                  <td>
                    <div><?php print $message; ?></div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
        <?php if (!in_array($status, array(CULTUREBOX_EMISSION_STATUS_DIFFUSION_DIRECT, CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY))): ?>
          <?php if (!empty($counter)): ?>
            <div class="counter" id="countdown"></div>
            <?php if (!empty($countdown_time)): ?>
              <div class="hide-me">
                <div class="countdown">
                  <?php print $countdown_time; ?>
                </div>
                <div class="offset">
                  <?php print $offset; ?>
                </div>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($trailer) && isset($message)): ?>
          <div class="btn-a-wrapper">
            <a href="javascript:void(0)" class="btn-a">
              <span>Voir la vidéo</span>
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
