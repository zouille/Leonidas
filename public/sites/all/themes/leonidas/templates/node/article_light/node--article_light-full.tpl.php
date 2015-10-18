<div id="ar-node-fiche-emission-full">
  <h1 class="first-sub"><?php if (!empty($published)): ?><span class="date-display-single"><?php print $published; ?></span> <?php endif; ?><?php print $title; ?></h1>
  <?php if (!empty($media)): ?>
    <div id="article-full-main-media">
      <?php if (!empty($microdata_image)): ?>
        <?php print $microdata_image; ?>
      <?php endif; ?>
      <?php if (!empty($img_full_seo)): ?>
        <?php print l($media, $img_full_seo, array('html' => TRUE, 'attributes' => array('class' => 'seo-optim-img'))); ?>
      <?php else: ?>
        <?php print $media; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="content-holder">
    <?php if (!empty($content['field_article_catchline'])): ?>
      <div class="content-img-text">
        <?php print render($content['field_article_catchline']); ?>
      </div>
    <?php endif; ?>
    <div class="container-left">
      <div class="share-line">
        <?php if (!empty($signature_free)): ?>
          <div class="box person">
            <div class="holder">
              <div class="text"><?php print sprintf('Par %s', $signature_free); ?></div>
            </div>
          </div>
        <?php elseif (!empty($signature)): ?>
          <?php if (is_array($signature)): ?>
            <?php print render($signature); ?>
          <?php else: ?>
            <div class="box person">
              <div class="holder">
                <div class="text"><?php print $signature; ?></div>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($share_links)): ?>
          <div class="box-diff">
            <?php print $share_links; ?>
          </div>
        <?php endif; ?>
      </div>
      <?php if (!empty($content['field_body'])): ?>
        <div class="content-text">
          <?php print render($content['field_body']); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>