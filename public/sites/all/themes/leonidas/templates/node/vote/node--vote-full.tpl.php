<div id="ar-node-vote-full">
  <h1 class="first-sub nd"><?php print $node->title; ?></h1>
  <div class="content-holder">
    <div class="container-left">
      <?php if (!empty($description) && $display_comment): ?>
        <div class="content-img-text">
          <?php print check_plain($description); ?>
        </div>
      <?php elseif (!empty($description) && !$display_comment): ?>
        <div class="content-img-text"><?php print $description; ?></div>
      <?php endif; ?>
      <div class="content-text">
        <?php if (!empty($content['field_body']) && $display_comment): ?>
          <?php print nl2br(render($content['field_body'])); ?>
        <?php endif; ?>
      </div>
      <?php if (!empty($bloc_oeuvres)): ?>
        <?php print render($bloc_oeuvres); ?>
      <?php endif; ?>
      <div class="share-line">
        <?php if (!empty($share_links)): ?>
          <div class="content-img-text vote-lp">Partagez le livre qui a changé votre vie et invitez vos amis à donner leur avis.</div>
          <?php print $share_links; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
