<?php if (!empty($user_name) || !empty($user_image)): ?>
  <div class="box person">
    <div class="holder">
      <?php if (!empty($user_image)): ?>
        <div class="img">
          <?php print $user_image; ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($user_name)): ?>
        <div class="text">
          <span><?php print sprintf('Par %s', $user_name); ?></span>
        </div>
      <?php endif; ?>
    </div>
    <?php if (!empty($user_profile['field_user_description'])): ?>
      <div class="p"><?php print render($user_profile['field_user_description']); ?></div>
    <?php endif; ?>
    <?php if (!empty($user_profile['field_twitter'])): ?>
      <div class="p"><?php print render($user_profile['field_twitter']); ?></div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <?php print '&nbsp;'; ?>
<?php endif; ?>
