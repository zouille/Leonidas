<div class="slideshow-info">
  <div class="aside">
    <?php if (!empty($signature_free)): ?>
      <div class="box person">
        <div class="holder">
          <div class="text"><?php print sprintf('Par %s', $signature_free); ?></div>
        </div>
      </div>
    <?php elseif (!empty($signature)): ?>
      <?php print drupal_render($signature); ?>
    <?php endif; ?>
  </div>
  <!-- aside end -->
  <div class="slideshow-content">
    <?php if (!empty($content['field_title_long'])): ?>
      <h1><?php print render($content['field_title_long']); ?></h1>
    <?php else: ?>
      <h1><?php print $title; ?></h1>
    <?php endif; ?>
    <span class="published">
      <?php print $published; ?>
    </span>
    <?php if (!empty($description)): ?>
      <div class="content-img-text">
        <?php print $description; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="toolbar-wrapp">
  <div id="toolbar-nav">
    <div class="share-line">
      <?php if (!empty($share_links_bottom)): ?>
        <?php print $share_links_bottom; ?>
      <?php endif; ?>
<!--      <div class="nav-page">-->
<!--        <span>--><?php //print 'Naviguer avec votre clavier';?><!--</span>-->
<!--        <span class="next article-diaporama-next">--><?php //print 'Retour vers le haut'; ?><!--</span>-->
<!--        <span class="prev article-diaporama-prev">--><?php //print 'Retour vers le haut'; ?><!--</span>-->
<!--      </div>-->
    </div>
  </div>
</div>
<?php if (!empty($content['field_article_main_media'])): ?>
  <?php print render($content['field_article_main_media']); ?>
<?php endif; ?>
