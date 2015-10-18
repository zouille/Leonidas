<?php if (!empty($articles) && count($articles) > 0): ?>
  <ul class="side-list slider-list">
    <li>
      <?php print (!empty($term_link)) ? $term_link : ''; ?>
      <?php if (!empty($articles[0])) : ?>
        <?php print $articles[0]; ?>
      <?php endif; ?>
    </li>
    <?php if (!empty($articles[1])) : ?>
      <li>
        <?php print $articles[1]; ?>
      </li>
    <?php endif; ?>
  </ul>
<?php endif; ?>
