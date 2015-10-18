<div id="photo0">
  <a id="navigation_prev0" href="javascript:void(0);" class="navigation_prev"></a>
  <a id="navigation_next0" href="javascript:void(0);" class="navigation_next"></a>
</div>
<article class="slideshow-info slideshow-content">
  <div class="aside">
    <?php if (!empty($diaporama)): ?>
      <div class="photo">
        <div class="ico">
          <?php print $diaporama['image']; ?>
        </div>
        <div class="desc">
          <strong>Diaporama</strong>
          <span><?php print $diaporama['count']; ?></span>
        </div>
      </div>
    <?php endif; ?>
    <?php if (!empty($signature_free)): ?>
      <div class="person">
        <div class="text">
          <?php print sprintf('Par <span>%s</span> %s', $signature_free, l('@Culturebox', 'https://twitter.com/Culturebox', array('attributes' => array('target' => '_blank', 'rel' => 'nofollow', 'class' => array('signature-twitter')))) . (!empty($signature_free_sub_title) ? " $signature_free_sub_title" : '')); ?>
        </div>
      </div>
    <?php elseif (!empty($signature)): ?>
      <?php if (is_array($signature)): ?>
        <?php print render($signature); ?>
      <?php else: ?>
        <div class="person">
          <div class="text"><?php print $signature . ' ' . l('@Culturebox', 'https://twitter.com/Culturebox', array('attributes' => array('target' => '_blank', 'rel' => 'nofollow', 'class' => array('signature-twitter')))); ?></div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
<!-- aside end -->
  <h1><?php print $title; ?></h1>
  <span class="published">
    <?php print $published; ?>
  </span>
  <?php if (!empty($description)): ?>
    <p class="content-img-text">
      <?php print $description; ?>
    </p>
  <?php endif; ?>
  <div class="toolbar-wrapp">
    <div id="toolbar-nav">
      <div class="share-line">
        <?php if (!empty($share_links)): ?>
          <?php print $share_links; ?>
        <?php endif; ?>
        <div class="nav-page">
          <span>Naviguer avec votre clavier</span>
          <span class="next article-diaporama-next">Retour vers le haut</span>
          <span class="prev article-diaporama-prev">Retour vers le haut</span>
        </div>
      </div>
    </div>
  </div>
  <?php if(!empty($photo)):?>
    <?php print $photo;?>
  <?php endif;?>
  <?php if (!empty($content['field_body'])): ?>
    <div class="content-holder">
      <?php print render($content['field_body']); ?>
   </div>
  <?php endif; ?>
</article>