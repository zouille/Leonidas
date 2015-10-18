<?php
/**
 * @file
 * Template for live content-type full view mode.
 */
?>
<div class="<?php print $classes; ?> clearfix node-<?php print $node->nid; ?>"<?php print $attributes; ?>>
  <?php print culturebox_site_l($node->title, "node/$node->nid"); ?>
</div>
