<?php
/**
 * @file
 * Template for layout page
 */
?>
<div id="wrapper">
  <header>
    <div class="holder">
      <?php if (!empty($content['header_top_left'])): ?>
        <?php print $content['header_top_left']; ?>
      <?php endif; ?>
      <?php if (!empty($content['header_top_right']) || !empty($content['header_top_right_frame'])): ?>
        <div class="box">
          <?php print $content['header_top_right']; ?>
          <?php if (!empty($content['header_top_right_frame'])): ?>
            <div class='frame'>
              <?php print $content['header_top_right_frame']; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($content['header_top_right_logo'])): ?>
        <?php print $content['header_top_right_logo']; ?>
      <?php endif; ?>
    </div>
    <?php if (!empty($content['header_bottom'])): ?>
      <nav>
        <div class="holder">
          <?php print $content['header_bottom']; ?>
        </div>
      </nav>
    <?php endif; ?>
    <?php if (!empty($content['header_submenu'])): ?>
      <?php print $content['header_submenu']; ?>
    <?php endif; ?>
  </header>
  <!-- header end -->
  <?php if (!empty($content['content'])): ?>
    <?php print $content['content']; ?>
  <?php endif; ?>
</div>
<!-- wrapper end -->

<?php if (!empty($content['left_selection']) || !empty($content['right_selection'])): ?>
  <div class="selection-holder">
    <div class="selection">
      <?php if (drupal_is_front_page()): ?>
        <div class="three-column">
          <?php if (!empty($content['left_selection'])): ?>
            <div class="content-block">
              <div class="social-title">
                <div class="ttl">
                  <strong>
                    <?php print 'culturebox sur facebook'; ?>
                  </strong>
                </div>
              </div>
              <?php print $content['left_selection']; ?>
            </div>
          <?php endif; ?>
          <?php if (!empty($content['right_selection'])): ?>
            <?php print $content['right_selection']; ?>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <?php if (!empty($content['left_selection'])): ?>
          <?php print $content['left_selection']; ?>
        <?php endif; ?>
        <?php if (!empty($content['right_selection'])): ?>
          <?php print $content['right_selection']; ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
<!-- selection-holder end -->
<?php if (!empty($content['footer_before'])): ?>
  <?php print $content['footer_before']; ?>
<?php endif; ?>
<?php if (!empty($content['footer_top']) || !empty($content['footer_bottom'])): ?>
  <footer>
    <?php if (!empty($content['footer_top'])): ?>
      <div class="footer-holder">
        <?php print $content['footer_top']; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($content['footer_bottom'])): ?>
      <div class="bottom">
        <?php print $content['footer_bottom']; ?>
      </div>
    <?php endif; ?>
  </footer>
<?php endif; ?>
