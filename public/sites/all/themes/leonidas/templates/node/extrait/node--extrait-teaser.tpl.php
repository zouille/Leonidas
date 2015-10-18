<div class="content">
  <div class="img-extrait-sommaire">
    <?php print render($image); ?>
    <?php if (isset($have_video) && $have_video): ?>
      <div class="fleche-play play-medium"></div>
    <?php endif; ?>
    <?php if (isset($is_bonus) && $is_bonus): ?>
      <div class="extrait-isbonus">Bonus</div>
    <?php endif; ?>
  </div>
  <div class="sommaire">
    <div class="field-sub"><?php if (isset($has_link) && $has_link): ?><?php print culturebox_site_l($node->title, 'node/' . $node->nid); ?><?php else: ?><?php print $node->title; ?><?php endif; ?></div>
    <div class="field-desc"><?php print assets_cut_filter_process(render($content['field_body'])); ?></div>
  </div>
</div>