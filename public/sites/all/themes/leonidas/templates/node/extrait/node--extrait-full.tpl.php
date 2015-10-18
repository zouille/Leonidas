<div class="node-extrait-full">
  <h1>
    <?php if ($field_extrait_video_bonus && $field_extrait_video_bonus[0]['value'] == 1): ?>
      <span class="extrait-isbonus">Bonus</span>
    <?php endif; ?>
    <?php print $title; ?>
  </h1>
  <h2><?php if (!empty($diff_date)): ?><span><?php print render($diff_date); ?></span><?php endif; ?> <?php print !empty($diff_title) ? $diff_title : ''; ?></h2>
  <?php if (!empty($content['field_extrait_video'])): ?>
    <div id="article-full-main-media">
      <?php print render($content['field_extrait_video']); ?>
    </div>
  <?php elseif (!empty($player_link)): ?>
    <div id="article-full-main-media">
      <?php print $player_link; ?>
      <?php if (!empty($player_script)): ?>
        <?php print $player_script; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="content-holder">
    <div class="container-left">
      <div class="share-line">
        <?php if (!empty($share_links)): ?>
          <?php print $share_links; ?>
        <?php endif; ?>
      </div>
      <?php if (!empty($content['field_body'])): ?>
        <div class="content-text">
          <?php print render($content['field_body']); ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($bloc_oeuvres)): ?>
        <?php print render($bloc_oeuvres); ?>
      <?php endif; ?>
      <?php if (!empty($bloc_personnalites)): ?>
        <?php print render($bloc_personnalites); ?>
      <?php endif; ?>
    </div>
  </div>
</div>
