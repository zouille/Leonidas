<div id="node-playlist-full">
  <div class="<?php print $classes; ?> node-extrait-live-full no-video-icons clearfix "<?php print $attributes; ?>>
    <div class="top-video clearfix">
      <h1><span>Playlist</span> <?php print $title; ?></h1>
      <?php print $share_links; ?>
      <div class="video">
        <?php if (!empty($first_video)): ?>
          <div id="article-full-main-media">
            <?php print render($first_video); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sidebar">
        <?php if (!empty($content['field_body'])): ?>
          <div class="side-block text">
            <?php print render($content['field_body']); ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($content['field_playlist_videos'])): ?>
          <div class="side-block nav">
            <span class="prev"></span>
            <span class="next"></span>
          </div>
          <div class="side-block sommaire scroll-pane" style="height: 250px;">
            <?php print render($content['field_playlist_videos']); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
