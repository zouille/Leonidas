<div id="ar-node-live-full">
  <div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> no-video-icons clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
    <?php // Dans le cas d'un player double, on affiche le titre du live au-dessus des players. ?>
    <?php if (!empty($player_link_additions)): ?>
      <div class="video-title clearfix first">
        <div class="info">
          <?php if (!empty($main_thematic)): ?>
            <b itemprop="genre"><?php print $main_thematic; ?></b>
          <?php endif; ?>
          <?php if (!empty($place)): ?>
            <span><?php print $place; ?></span>
          <?php endif; ?>
          <?php if (!empty($start_date) ): ?>
            <span class="date"><?php print $start_date; ?></span>
          <?php endif; ?>
          <?php if (!empty($replay_end_date) && !$replay_en_attente): ?>
            <em><?php print $replay_end_date; ?></em>
          <?php endif; ?>
        </div>
        <div class="ttl">
          <h1><?php print $title; ?></h1>
        </div>
      </div>
    <?php endif; ?>
    <div class="top-video clearfix">
      <div<?php print (!empty($player_link_additions) ? ' id="ftv_player_double"' : ''); ?> class="video<?php (!empty($trailer) && isset($message)) ? print " video-button" : ""; ?>">
        <?php if (!empty($microdata_video_object)): ?>
          <?php print $microdata_video_object; ?>
        <?php endif; ?>
        <?php if (!empty($player_link)): ?>
          <?php print $player_link; ?>
          <?php if (!empty($player_script)): ?>
            <?php print $player_script; ?>
          <?php endif; ?>
          <?php if (!empty($player_link_additions)): ?>
            <?php print $player_link_additions; ?>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($image)): ?>
          <div class="live-image">
            <?php print $image; ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($trailer)): ?>
          <div class="trailer" style="display: none">
            <?php print $trailer; ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
          <div class="delay-message overlay">
            <table>
              <tbody>
                <tr>
                  <td>
                    <div><?php print $message;?></div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
        <?php if (!empty($trailer) && isset($message)): ?>
          <div class="btn-a-wrapper">
            <a href="javascript:void(0)" class="btn-a">
              <span>Voir la vidéo</span>
            </a>
          </div>
        <?php endif; ?>
        <?php if (!in_array($cb_status_live, array(CULTUREBOX_LIVE_STATUS_LIVE_DIRECT, CULTUREBOX_LIVE_STATUS_LIVE_REPLAY, CULTUREBOX_LIVE_STATUS_LIVE_LAST_CHANCE))): ?>
          <?php if (!empty($live_status)): ?>
            <?php if (!empty($counter)): ?>
              <div class="counter" id="countdown"></div>
            <?php else: ?>
              <?php print $live_status; ?>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
    <div class="video-title clearfix">
      <?php // Dans le cas d'un player simple, on affiche le titre du live au-dessous du player. ?>
      <?php if (empty($player_link_additions)): ?>
        <div class="info">
          <?php if (!empty($main_thematic)): ?>
            <b itemprop="genre"><?php print $main_thematic; ?></b>
          <?php endif; ?>
          <?php if (!empty($place)): ?>
            <span><?php print $place; ?></span>
          <?php endif; ?>
          <?php if (!empty($start_date) && !$replay_en_attente): ?>
            <span class="date"><?php print $start_date; ?></span>
          <?php endif; ?>
          <?php if (!empty($replay_end_date) && !$replay_en_attente): ?>
            <em><?php print $replay_end_date; ?></em>
          <?php endif; ?>
        </div>
        <div class="ttl" <?php if (!empty($multilingue) && $multilingue == TRUE): ?>style="width: 75%" <?php endif; ?>>
          <?php if (!empty($inform_popup)): ?>
            <div class="inform share-link">
              <span class="inform-link">M'avertir</span>
              <?php print $inform_popup; ?>
            </div>
          <?php endif; ?>
          <?php if (!empty($live_status) && in_array($status, array(CULTUREBOX_LIVE_STATUS_LIVE_DIRECT, CULTUREBOX_LIVE_STATUS_LIVE_REPLAY))): ?>
            <?php print $live_status; ?>
          <?php endif; ?>
          <h1><?php print $title; ?></h1>
        </div>
      <?php endif; ?>
      <?php if (!empty($multilingue) && $multilingue == TRUE): ?>
        <?php if (!empty($url_related)) : ?>
          <?php print $url_related ?>
        <?php endif; ?>
      <?php endif; ?>
      <?php if (!empty($share_links)): ?>
        <?php print $share_links; ?>
      <?php endif; ?>
      <?php if (!empty($inform_popup_share)): ?>
        <div class="inform share-link">
          <span class="inform-link"></span>
          <?php print $inform_popup_share; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php if (!empty($node->field_live_bi_neural) && $node->field_live_bi_neural[LANGUAGE_NONE][0]['value'] == 1): ?>
      <div id="encart-binaural" class="expanded">
        <div id="expander"><div class="trait"></div><div class="trait"></div><div class="trait"></div></div>
        <div id="title-binaural">Découvrez le son binaural en écoutant ce live avec un casque audio !</div>
        <div class="expendable">
          <div class="left-part">
            <div class="title-part">Qu'est-ce que le son binaural ?</div>
            <p>Le binaural est une technique de restitution sonore spatialisée qui permet de percevoir les sons
              de manière naturelle. Elle nécessite impérativement un casque audio ou des écouteurs standards.
            </p>
          </div>
          <div class="right-part">
            <div class="title-part">Entrez dans l'expérience du son 3D !</div>
            <p>Mettez votre casque et testez notre vidéo de démo :</p>
            <?php if (!empty($extrait_binaural)): ?>
              <div id="extrait-binaural">
                <?php print $extrait_binaural; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php if (!empty($node->field_mise_en_avant_description[LANGUAGE_NONE][0]['value'])): ?>
      <div id="encart-binaural" class="expanded">
        <div id="expander"><div class="trait"></div><div class="trait"></div><div class="trait"></div></div>
        <div id="title-binaural">
          <?php if (!empty($node->field_fe_sub_title[LANGUAGE_NONE][0]['value'])): ?>
            <?php print $node->field_fe_sub_title[LANGUAGE_NONE][0]['value']; ?>
          <?php else: ?>
            <?php print $title; ?>
          <?php endif; ?>
        </div>
        <div class="expendable">
          <p><?php print $node->field_mise_en_avant_description[LANGUAGE_NONE][0]['value']; ?></p>
        </div>
      </div>
    <?php endif; ?>
    <?php if (!empty($bandeau_image)): ?>
      <div id="video-mea">
        <?php print $bandeau_image; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
