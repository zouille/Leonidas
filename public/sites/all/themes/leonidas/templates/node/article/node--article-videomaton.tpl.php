<h1><?php print $title; ?></h1>
<span class="published">
  <?php print $published; ?>
</span>
<?php if (!empty($description)): ?>
  <div class="content-img-text">
    <?php print $description; ?>
  </div>
<?php endif; ?>
<div class="content-holder">
  <?php if (!empty($content['field_body'])): ?>
    <div class="content-text">
      <?php print render($content['field_body']); ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($content['field_videomaton_videos'])): ?>
    <section class="clearfix combo-filters" id="options">
      <div class="option-combo"  style="margin-bottom:2px;">
        <ul class="filter option-set clearfix " data-filter-group="genre">
          <li class="ctous"><a class="selected" data-filter-value="" href="#filter-genre-tous"></a></li>
          <li class="ctheatre"><a data-filter-value=".theatre" href="#filter-genre-theatre"></a></li>
          <li class="cconcert"><a data-filter-value=".concert" href="#filter-genre-concert"></a></li>
          <li class="cdanse"><a data-filter-value=".danse" href="#filter-genre-danse"></a></li>
          <li class="ccirque"><a data-filter-value=".cirque" href="#filter-genre-cirque"></a></li>
        </ul>
      </div>
      <br>
      <br>
      <div class="option-combo">
        <ul class="filter option-set clearfix " data-filter-group="public">
          <li class="ctous"><a class="selected" data-filter-value="" href="#filter-public-tous"></a></li>
          <li class="cjeune"><a data-filter-value=".jeune" href="#filter-public-jeune"></a></li>
          <li class="ctout"><a data-filter-value=".tout" href="#filter-public-tout"></a></li>
          <li class="cadulte"><a data-filter-value=".adulte" href="#filter-public-adulte"></a></li>
        </ul>
      </div>
    </section>
    <!-- #options -->
    <div id="container" class="clearfix">
      <?php print render($content['field_videomaton_videos']); ?>
    </div>
  <?php endif; ?>
</div>
<div class="share-line">
  <?php if (!empty($share_links_bottom)): ?>
    <?php print $share_links_bottom; ?>
  <?php endif; ?>
</div>
