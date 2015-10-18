<div id="ar-node-fiche-emission-full" itemscope itemtype="http://schema.org/TVEpisode">
  <h1 class="first-sub"><meta itemprop="name" content="<?php print $title; ?>"><?php print $title; ?></h1>
  <?php if (!empty($date)): ?>
    <span class="date-display-single"><?php print $date; ?><?php if (!empty($duree)): ?><?php print $duree; ?> min<?php endif; ?></span>
  <?php endif; ?>
  <div class="top-video">
    <div
      class="video<?php ((!empty($trailer) || !empty($bonus)) && isset($message)) ? print " video-button" : ""; ?>"<?php if (!empty($microdata_video_object)): ?> itemscope itemtype="http://schema.org/VideoObject"<?php endif; ?>>
        <?php if (!empty($microdata_video_object)): ?>
          <?php print $microdata_video_object; ?>
        <?php endif; ?>
        <?php if (!empty($player_link)): ?>
          <?php print $player_link; ?>
          <?php if (!empty($player_script)): ?>
            <?php print $player_script; ?>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($image)): ?>
        <div class="live-image">
          <?php print $image; ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($trailer)): ?>
        <div class="trailer hide">
          <?php print $trailer; ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($bonus)): ?>
        <div class="trailer<?php if (isset($message)): ?> hide<?php endif; ?>">
          <?php print $bonus; ?>
        </div>
      <?php endif; ?>
      <?php if (isset($message)): ?>
        <div class="delay-message overlay">
          <table>
            <tbody>
              <tr>
                <td>
                  <div><?php print $message . (!empty($bonus) ? PHP_EOL . 'Découvrez la vidéo bonus.' : ''); ?></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
      <?php if ((!empty($trailer) || !empty($bonus)) && isset($message)): ?>
        <div class="btn-a-wrapper">
          <a href="javascript:void(0);" class="btn-a">
            <span>Voir la vidéo</span>
          </a>
        </div>
      <?php endif; ?>
      <?php if (!in_array($status, array(CULTUREBOX_LIVE_STATUS_LIVE_DIRECT, CULTUREBOX_LIVE_STATUS_LIVE_REPLAY))): ?>
        <?php if (!empty($live_status)): ?>
          <?php print $live_status; ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
  <?php if (!empty($media)): ?>
    <div id="article-full-main-media">
      <?php print $media; ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($description)): ?>
    <div class="content-img-text" itemprop="description">
      <?php print $description; ?>
    </div>
  <?php endif; ?>
  <section class="content-holder">
    <article class="container-left">
      <aside class="share-line">
        <?php if (!empty($share_links)): ?>
          <?php print $share_links; ?>
        <?php endif; ?>
        <?php if (!empty($inform_popup)): ?>
          <div class="clear">&nbsp;</div>
          <div class="box-diff">
            <div class="inform share-link">
              <span class="inform-link">M'avertir</span>
              <?php print $inform_popup; ?>
            </div>
          </div>
        <?php endif; ?>
      </aside>
      <?php if (!empty($content['field_body'])): ?>
        <?php if ($text_short != $text_long): ?>
          <div class="content-text show"><?php print $text_short; ?></div>
          <div class="content-text hide"><?php print $text_long; ?></div>
          <a class="plus-info action-link" href="#summary"><span class="picto"></span><span
              class="txt">Plus d'infos</span></a>
          <?php else: ?>
          <div class="content-text show"><?php print $text_long; ?></div>
        <?php endif; ?>
      <?php endif; ?>
      <?php if (!empty($content['field_emission_team']['#items'])): ?>
        <a class="ancore-link-dark action-link" href="#cast">Les invités</a>
      <?php endif; ?>
      <?php if (!empty($bloc_oeuvres)): ?>
        <?php print render($bloc_oeuvres); ?>
      <?php endif; ?>
      <?php if (!empty($bloc_personnalites)): ?>
        <?php print render($bloc_personnalites); ?>
      <?php endif; ?>
    </article>
  </section>
</div>
