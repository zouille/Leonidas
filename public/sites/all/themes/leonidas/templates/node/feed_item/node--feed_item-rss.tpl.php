<?php
/**
 * @file
 * Template for feed_item content-type rss view mode.
 */
?>
<item>
  <title><?php print $title;?></title>
  <?php if (!empty($url)): ?>
    <link><?php print $url; ?></link>
  <?php endif; ?>
  <?php if (isset($enclosure)): ?>
    <enclosure url="<?php print $enclosure['external_url']?>" length="<?php print $enclosure['filesize'];?>" type="<?php print $enclosure['filemime'];?>" />
  <?php endif; ?>
  <pubDate><?php print $pubdate; ?></pubDate>
</item>
