<div class="extrait-live">
  <div class="top-video">
    <div class="holder">
      <?php if (!empty($rendered_article_media)): ?>
        <?php print $rendered_article_media; ?>
      <?php endif; ?>
    </div>
    <span class="trends">Extrait live</span>
    <h2><?php print $title_link; ?></h2>
    <?php if (!empty($accroche)): ?>
      <p class="article"><?php print $accroche; ?></p>
    <?php endif; ?>
  </div>
</div>
