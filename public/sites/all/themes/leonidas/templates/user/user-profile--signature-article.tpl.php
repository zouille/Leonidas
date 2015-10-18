<?php if (!empty($user_name) || !empty($user_image)): ?>
  <div class="person">
    <?php if (!empty($user_image)): ?>
      <div class="img">
        <?php print $user_image; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($user_name)): ?>
      <div class="text">
        <?php print sprintf('Par <span>%s</span> %s', $user_name, (!empty($user_profile['field_twitter']) ? render($user_profile['field_twitter']) : l('@Culturebox', 'https://twitter.com/Culturebox', array('attributes' => array('target' => '_blank', 'rel' => 'nofollow', 'class' => array('signature-twitter'))))) . (!empty($user_profile['field_user_description']) ? render($user_profile['field_user_description']) : '')); ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
