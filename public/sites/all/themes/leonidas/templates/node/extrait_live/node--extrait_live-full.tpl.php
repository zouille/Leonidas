<div class="node-extrait-live-full">
  <div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> no-video-icons clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
    <div class="top-video clearfix">
      <div class="video">
        <?php if (!empty($content['field_extrait_video'])): ?>
          <div id="article-full-main-media">
            <?php print render($content['field_extrait_video']); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sidebar">
        <?php if (!empty($node_extrait_live)): ?>
          <div class="side-block">
            <div class="h3">Le live intégral</div>
            <ul class="derniers-lives-list">
              <li>
                <?php print $node_extrait_live; ?>
              </li>
            </ul>
          </div>
        <?php endif; ?>
        <?php if (!empty($node_extrait_sommaire)): ?>
          <?php print $node_extrait_sommaire; ?>
        <?php endif; ?>
      </div>
      <div class="video-title clearfix">
        <div class="ttl">
          <?php if (!empty($is_bonus)): ?>
            <div class="mask revoir">
              <a href="javascript:void(0)">Bonus</a>
            </div>
          <?php endif; ?>
          <h1><?php print $title; ?></h1>
        </div>
        <?php print $share_links; ?>
      </div>
    </div>
  </div>
</div>
