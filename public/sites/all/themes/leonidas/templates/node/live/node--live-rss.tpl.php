<?php
/**
 * @file
 * Template for live content-type rss view mode.
 */
?>
<item>
  <title><?php print $title;?></title>
  <link><?php print url('node/' . $node->nid, array('absolute' => TRUE));?></link>
  <description><?php print check_plain(render($content)); ?></description>
  <?php if (isset($enclosure)): ?>
    <enclosure url="<?php print $enclosure['external_url']?>" length="<?php print $enclosure['filesize'];?>" type="<?php print $enclosure['filemime'];?>" />
  <?php endif; ?>
  <pubDate><?php print $pubdate; ?></pubDate>
</item>
