<!doctype html>
<!--[if IE]>
<html xmlns:fb="http://ogp.me/ns/fb#" xmlns:og="http://opengraphprotocol.org/schema/"
      xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" version="XHTML+RDFa 1.0"
      dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>>
<![endif]-->
<!--[if !IE]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" version="XHTML+RDFa 1.0"
      dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>>
<!--<![endif]-->
<head profile="<?php print $grddl_profile; ?>">
<?php print $head; ?>
<meta name="archimade_token" content="<?php print $archimade_token_content; ?>"/>
<meta name="archimade_idsite" content="1951"/>
<meta name="apple-itunes-app" content="app-id=648273701"/>
<title><?php print $head_title; ?></title>
<?php print $styles; ?>
<!--[if IE 7]>
<link type="text/css" rel="stylesheet" media="all" href="<?php print url(path_to_theme() . '/css/ie7.css', array('absolute' => TRUE)); ?>"/>
<![endif]-->
<!--[if lt IE 9]>
<link type="text/css" rel="stylesheet" media="all" href="<?php print url(path_to_theme() . '/css/custom_ie7.css', array('absolute' => TRUE)); ?>"/>
<![endif]-->
</head>
<body>
<?php print render($page); ?>
</body>
</html>