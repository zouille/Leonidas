<?php if (!empty($article_live) && $article_live) : ?>
  <div class="node-article-live">
    <div class="node-article-live-image">
      <img src='<?php echo url(drupal_get_path('theme', 'culturebox').'/images/bloc_selection_live.jpg', array('absolute' => TRUE)); ?>' alt='Concerts & Spectacles en live' title='Concerts & Spectacles en live' />
    </div>
    <h1 itemprop="headline"><?php print $title; ?></h1>
  </div>
<?php else: ?>
  <h1 itemprop="headline"><?php print $title; ?></h1>
<?php endif; ?>
<span class="published clearfix">
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
  <?php if (!empty($published_schema_org)): ?>
    <meta itemprop="datePublished" content="<?php print $published_schema_org; ?>" />
  <?php endif; ?>
  <?php if (!empty($updated_schema_org)): ?>
    <meta itemprop="dateUpdated" content="<?php print $updated_schema_org; ?>" />
  <?php endif; ?>
  <?php print $published; ?>
</span>
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
<?php if (!empty($share_links)): ?>
  <div class="box">
    <?php print $share_links; ?>
  </div>
<?php endif; ?>
<?php if (!empty($description)): ?>
  <p class="content-img-text">
    <?php if (variable_get('cb_display_seo_brand', TRUE)): ?><noscript>CULTUREBOX</noscript><?php endif; ?><?php print $description; ?>
  </p>
<?php endif; ?>
<?php if (!empty($min_article) && $min_article == TRUE): ?>
  <div class="block-refresh min-refresh">
    <p>Cet article minute par minute est mis-à-jour régulièrement par notre rédaction.</p>
    <div>
      <span class="min-refresh reload"></span>
      <input type="button" value="Actualiser la page" class="min-refresh">
    </div>
  </div>
<?php endif; ?>
<?php if (!empty($content['field_rate']) && ($content['field_rate']['#items'][0]['average'] > 0)): ?>
  <div id="node-article-field_rate">
    <div id="node-article-field_rate-text">
      <p>
        La note Culturebox
      </p>
    </div>
    <div id="node-article-field_rate-fivestar">
      <?php print render($content['field_rate']); ?>
    </div>
    <div id="node-article-field_rate-note">
      <?php
      $note = ceil($content['field_rate']['#items'][0]['average'] / 20);
      print $note . '/5';
      ?>
    </div>
  </div>
<?php endif; ?>
<div class="content-holder">
  <?php if (!empty($content['field_body'])): ?>
    <div class="content-text" itemprop="articleBody">
      <?php if (empty($description) && variable_get('cb_display_seo_brand', TRUE)): ?>
        <noscript>CULTUREBOX</noscript><?php endif; ?><?php print render($content['field_body']); ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($newsletter_bloc)): ?>
    <?php print $newsletter_bloc; ?>
  <?php endif; ?>
  <?php if (!empty($aside)): ?>
    <?php print $aside; ?>
  <?php endif; ?>
</div>
