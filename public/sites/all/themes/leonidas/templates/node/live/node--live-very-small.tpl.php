<div id="ar-node-live-very-small">
  <div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> no-video-icons clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
    <?php if (!empty($player_link)): ?>
      <?php print $player_link; ?>
      <script>
        (function($) {
          if ($('#player').length) {
            var width = 660;
            var height = 330;
            var autoplay = false;
            // On place un marqueur pour détecter la fin de la vidéo (et pas la fin de la publicité).
            var publicite = false;
            if (Drupal.settings.CultureboxLive != undefined && Drupal.settings.CultureboxLive.playerWidth != undefined) {
              width = Drupal.settings.CultureboxLive.playerWidth;
            }
            if (Drupal.settings.CultureboxLive != undefined && Drupal.settings.CultureboxLive.playerHeight != undefined) {
              height = Drupal.settings.CultureboxLive.playerHeight;
            }
            if (Drupal.settings.CultureboxLive != undefined && Drupal.settings.CultureboxLive.autoplay != undefined) {
              autoplay = Drupal.settings.CultureboxLive.autoplay;
            }
            $('#player').player({
              width: width,
              height: height,
              showPlayButton: autoplay,
              <?php if (!empty($startTimecode)): echo 'startTimecode: ' . $startTimecode . ',' . "\n"; endif; ?>
              triggerLinkEvents: 'playerReady'
            }).on('advertisement_start', function () {
              publicite = true;
            }).on('advertisement_finished', function () {
              publicite = false;
            }).on('video_end', function() {
              if (!publicite) {
                var pl = $('#node-playlist-full');
                pl.find('.ar-player.active').next().click();
              }
            });
          }
        })(jQuery);
      </script>
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
    <?php if (!in_array($status, array(CULTUREBOX_LIVE_STATUS_LIVE_DIRECT, CULTUREBOX_LIVE_STATUS_LIVE_REPLAY))): ?>
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
  </div>
</div>
