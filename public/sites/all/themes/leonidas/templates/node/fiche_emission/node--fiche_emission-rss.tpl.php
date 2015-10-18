<item>
  <title><?php print $title; ?></title>
  <link><?php print url('node/' . $node->nid, array('absolute' => TRUE)); ?></link>
  <description><?php print ltrim(check_plain(render($content['field_fe_chapo']))); ?></description>
  <?php if (isset($enclosure)): ?>
    <enclosure url="<?php print $enclosure['external_url'] ?>" length="<?php print $enclosure['filesize']; ?>" type="<?php print $enclosure['filemime']; ?>" />
  <?php endif; ?>
  <pubDate><?php print $pubdate; ?></pubDate>
</item>
