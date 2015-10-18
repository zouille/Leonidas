<!doctype html>
<!--[if IE]>
<html xmlns:fb="http://ogp.me/ns/fb#" xmlns:og="http://opengraphprotocol.org/schema/" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" version="XHTML+RDFa 1.0" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>>
<![endif]-->
<!--[if !IE]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" version="XHTML+RDFa 1.0" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>>
<!--<![endif]-->
<head profile="<?php print $grddl_profile; ?>">
<script>var myHash='',queryString='';var xtref=document.referrer.replace(/[<>]/g,'').replace(/&/g,'$');myHash='xtref='+((''!=xtref)?xtref:'acc_dir');if(document.location.search&&(new RegExp("xtor=([^&]+)")).test(document.location.search)){var xtor=(new RegExp("xtor=([^&]+)")).exec(document.location.search);if(xtor){myHash=((myHash!='')?'&':'')+'xtor='+xtor[1]}var queryString=document.location.search.replace(xtor[0],'')}myHash=(document.location.hash!='')?document.location.hash+((myHash!='')?'&'+myHash:''):'#'+myHash;</script>
<?php if (variable_get('mobile_redirect', FALSE)): ?>
<script>function cbGetCookie(a){var b=" "+document.cookie,c=" "+a+"=",d=null,e=0,f=0;return b.length>0&&(e=b.indexOf(c),-1!=e&&(e+=c.length,f=b.indexOf(";",e),-1==f&&(f=b.length),d=unescape(b.substring(e,f)))),d}function cbSetCookie(a,b,c,d,e,f){document.cookie=a+"="+escape(b)+(c?"; expires="+c:"")+(d?"; path="+d:"")+(e?"; domain="+e:"")+(f?"; secure":"")}</script>
<?php if (!$player_widget): ?>
<script>var isMobile={Mobile:function(){return navigator.userAgent.match(/mobile/i)},iPad:function(){return navigator.userAgent.match(/iPad/i)},Android:function(){return navigator.userAgent.match(/Android/i)},BlackBerry:function(){return navigator.userAgent.match(/BlackBerry/i)},iOS:function(){return navigator.userAgent.match(/iPhone|iPad|iPod/i)},Opera:function(){return navigator.userAgent.match(/Opera Mini/i)},Windows:function(){return navigator.userAgent.match(/IEMobile/i)},AndroidTablets:function(){return navigator.userAgent.match(/Android/i)&&!navigator.userAgent.match(/mobile/i)?!0:!1},any:function(){return isMobile.Android()||isMobile.BlackBerry()||isMobile.iOS()||isMobile.Opera()||isMobile.Windows()},not_tablets:function(){return(isMobile.Android()||isMobile.BlackBerry()||isMobile.iOS()||isMobile.Opera()||isMobile.Windows())&&!isMobile.AndroidTablets()&&!isMobile.iPad()}},disableMobileRedirect=cbGetCookie("disableMobileRedirect");if((void 0==disableMobileRedirect||"true"!=disableMobileRedirect)&&isMobile.not_tablets()){if(-1!=window.location.host.search("www."))var destination=window.location.protocol+"//"+window.location.host.replace("www.","m.");else var destination=window.location.protocol+"//m."+window.location.host;window.location=destination+window.location.pathname+window.location.search+myHash}</script>
<?php endif; ?>
<?php endif; ?>
<?php print $head; ?>
<meta name="archimade_token" content="<?php print $archimade_token_content; ?>" />
<meta name="archimade_idsite" content="1951" />
<meta name="apple-itunes-app" content="app-id=648273701" />
<meta name="viewport" content="width=1000" />
<title><?php print $head_title; ?></title>
<?php if (!$player_widget): ?>
<link type="text/css" rel="stylesheet" href="http://newsletters.francetv.fr/cnil/css/cnil-min-v20140612.css" media="all" />
<link type="text/css" rel="stylesheet" href="http://newsletters.francetv.fr/footer-transverse/css/footer-min-v1.0.25.css" media="all" />
<link type="text/css" rel="stylesheet" href="http://newsletters.francetv.fr/footer-transverse/css/jquery-ui.css" media="all" />
<?php if (!empty($ftven_formabo['css'])): ?>
<?php print implode("\n", $ftven_formabo['css']); ?>
<?php endif; ?>
<?php endif; ?>
<?php print $styles; ?>
<?php print $scripts; ?>
<!--[if IE 7]>
<link type="text/css" rel="stylesheet" media="all" href="<?php print url(path_to_theme() . '/css/ie7.css', array('absolute' => TRUE)); ?>" />
<![endif]-->
<!--[if lt IE 9]>
<link type="text/css" rel="stylesheet" media="all" href="<?php print url(path_to_theme() . '/css/custom_ie7.css', array('absolute' => TRUE)); ?>" />
<![endif]-->
</head>
<body class="<?php print $classes; ?>" <?php print $attributes; ?>>
<?php if (!empty($metanav)): ?>
<?php print $metanav; ?>
<?php endif; ?>
<?php print $page_top; ?>
<?php print $page; ?>
<?php print $page_bottom; ?>
<?php if (!$player_widget): ?>
<script type="text/javascript" src="/sites/all/themes/culturebox/js/cnil.js"></script>
<script src="http://newsletters.francetv.fr/footer-transverse/js/jquery-ui-1.9.2.custom.min.js" defer></script>
<?php if (!empty($ftven_formabo['js'])): ?>
<?php print implode("\n", $ftven_formabo['js']); ?>
<?php endif; ?>
<script>
(function(w, d, s) {
  function cbAsyncLoad(){
    var js, fjs = d.getElementsByTagName(s)[0], load = function(url, id) {
	  if (d.getElementById(id)) {return;}
	    js = d.createElement(s); js.src = url; js.id = id;
	    fjs.parentNode.insertBefore(js, fjs);
	  };
    
    var services = new Object();
    services['cbAsyncTumblr'] = '//platform.tumblr.com/v1/share.js';
    services['cbAsyncGoogle'] = 'https://apis.google.com/js/plusone.js';
    services['cbAsyncTwitter'] = '//platform.twitter.com/widgets.js';
    services['cbAsyncFacebook'] = '//connect.facebook.net/fr_FR/all.js';
    
    for (var i in services) {
      if (window[i] === true) {
        load(services[i], i);
      }
    }
    
    load('//newsletters.francetv.fr/footer-transverse/js/jquery.xdomainrequest.min.js', 'jquery.xdomainrequest.min.js');
    load('//newsletters.francetv.fr/footer-transverse/js/jquery.footer-transverse-min-v1.0.18.js', 'jquery.footer-transverse-min-v1.0.18.js');
  }
  
  if (w.addEventListener) { w.addEventListener("load", cbAsyncLoad, false); }
  else if (w.attachEvent) { w.attachEvent("onload", cbAsyncLoad); }
}(window, document, 'script'));
</script>
<?php endif; ?>

<div id="eShowPubx01" style="display:none"><div class="adCtnt"></div></div>

<?php if (!$player_widget): ?>
  <div id="dialog-tabs" style="display: none;"></div>
  <div class="footer-transverse" id="footer-transverse">
    <div id="ft-top-box">
      <div id="ft-top">
        <div id="ft-top-logo">
          <a href="/" target="_blank"><img width="150" height="16" alt="Logo Culturebox" src="/sites/all/themes/culturebox/images/logo_culturebox_blanc_fond_noir.svg"></a>
        </div>
        <ul id="ft-top-link">
          <li style="margin-left: -15px;"><a href="/" title="Actu">Actu</a></li>
          <li class="small-separator"></li>
          <li><a href="/live/" title="Live">Live</a></li>
          <li class="small-separator"></li>
          <li><a href="/festivals/" title="Festivals">Festivals</a></li>
          <li class="small-separator"></li>
          <li><a href="/sitemap/index.html" title="Dernières actualités">Index</a></li>
          <li class="small-separator"></li>
          <li><a href="/sitemap/dernieres-actualites.html" title="Dernières actualités">Dernières actualités</a></li>
          <li class="flt-right large-separator"></li>
          <li class="flt-right"><a href="/mobile"><span class="icn icn-mobile"></span></a></li>
          <li class="flt-right large-separator"></li>
          <li class="flt-right"><a href="http://culturebox.francetvinfo.fr/abonnements/"><span class="icn icn-mail"></span></a></li>
          <li class="flt-right large-separator"></li>
          <li class="flt-right"><a href="/rss"><span class="icn icn-rss"></span></a></li>
          <li class="flt-right large-separator"></li>
          <li class="flt-right"><a target="_blank" href="https://plus.google.com/107368086649921030514/"><span class="icn icn-google-plus"></span></a></li>
          <li class="flt-right large-separator"></li>
          <li class="flt-right"><a target="_blank" href="https://twitter.com/culturebox"><span class="icn icn-twitter"></span></a></li>
          <li class="flt-right large-separator"></li>
          <li class="flt-right"><a target="_blank" href="https://www.facebook.com/Culturebox"><span class="icn icn-facebook"></span></a></li>
          <li style="margin-left: 15px;" class="flt-right large-separator"></li>    
        </ul>
      </div>
    </div>
    <div id="ft-body-box">
      <div id="ft-body">
        <div id="ft-body-container">
          <ul class="jeux" id="jeux">
            <h4>JEUX</h4>
            <li><a class="ft-first-item" title="Tout Le Monde Veut Prendre Sa Place" target="_blank" href="http://tlmvpsp.france2.fr/#xtatc=INT-457"><img src="http://newsletters.francetv.fr/footer-transverse/pictures/jeux.png" alt="Tout Le Monde Veut Prendre Sa Place"></a></li>
            <li><a title="Tout Le Monde Veut Prendre Sa Place" target="_blank" href="http://tlmvpsp.france2.fr/#xtatc=INT-457">Tout Le Monde Veut Prendre Sa Place</a></li>
            <li><a title="Des Chiffres et Des Lettres" target="_blank" href="http://dcdl.france3.fr/#xtatc=IN">Des Chiffres et Des Lettres</a></li>
            <li><a  title="Questions Pour Un Champion" target="_blank" href="http://www.qpuc.france3.fr/landing.php#xtatc=INT-494">Questions Pour Un Champion</a></li>
            <li><a class="bottom-style-end" title="Slam" target="_blank" href="http://slam.france3.fr/#xtatc=INT-493">Slam</a></li>
          </ul>
        </div>
        <div id="ft-body-offre-left">
          <h4>EVENEMENTS</h4>
          <ul class="link-left link-list">
            <li><a title="Festivals de l'été 2015" target="_blank" href="http://culturebox.francetvinfo.fr/festivals/">Festivals de l'été 2015</a></li>
            <li><a title="Jazz à la Villette" target="_blank" href="http://culturebox.francetvinfo.fr/festivals/jazz-a-la-villette/">Jazz à la Villette</a></li>
            <li><a title="la nouvelle plateforme web de films courts de FranceTV" target="_blank" href="http://irl.nouvelles-ecritures.francetv.fr/">IRL</a></li>
            <li><a title="Studio 4, webséries 100% originales et gratuites" target="_blank" href="http://www.france4.fr/studio-4/">Studio 4</a></li>
          </ul>
        </div>
        <div id="ft-body-offre-right">
          <h4>LES OFFRES DU GROUPE</h4>
          <ul class="link-left link-list">
            <li><a href="http://www.la1ere.fr/" target="_blank" title="La 1ère">La 1ère</a></li>
            <li><a href="http://www.france2.fr/" target="_blank" title="France 2">France 2</a></li>
            <li><a href="http://www.france3.fr/" target="_blank" title="France 3">France 3</a></li>
            <li><a href="http://www.france4.fr/" target="_blank" title="France 4">France 4</a></li>
            <li><a href="http://www.france5.fr/" target="_blank" title="France 5">France 5</a></li>
            <li><a href="http://www.franceo.fr/" target="_blank" title="France Ô">France Ô</a></li>
            <li><a href="http://pluzz.francetv.fr/" target="_blank" title="Pluzz">Pluzz</a></li>
            <li><a href="http://pluzzvad.francetv.fr/" target="_blank" title="Pluzz VàD">Pluzz VàD</a></li>
            <li><a href="http://www.leclubfrancetelevisions.fr/" target="_blank" title="leclub francetv">leclub francetv</a></li>
          </ul>
          <ul class="link-right link-list">
            <li><a href="http://www.francetvinfo.fr/" target="_blank" title="francetv info">francetv info</a></li>
            <li><a href="http://www.francetvsport.fr/" target="_blank" title="francetv sport">francetv sport</a></li>
            <li><a href="http://culturebox.francetvinfo.fr/" target="_blank" title="Culturebox">Culturebox</a></li>
            <li><a href="http://geopolis.francetvinfo.fr/" target="_blank" title="Geopolis">Geopolis</a></li>
            <li><a href="http://www.ludo.fr/" target="_blank" title="ludo.fr">Ludo</a></li>
            <li><a href="http://www.france5.fr/emissions/zouzous/" target="_blank" title="Zouzous">Zouzous</a></li>
            <li><a href="http://education.francetv.fr/" target="_blank" title="francetv education">francetv education</a></li>
            <li><a href="http://nouvelles-ecritures.francetv.fr/" target="_blank" title="Nouvelles Ecritures">Nouvelles Ecritures</a></li>
            <li><a href="http://zoom.francetv.fr/" target="_blank" title="francetv zoom">francetv zoom</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div id="ft-bottom-box" xtcz="Infos_Footer">
      <div id="ft-bottom">
        <div id="ft-bottom-logo"><a id="francetv" href="http://www.francetv.fr/" target="_blank"></a></div>
        <div id="ft-bottom-link">
          <ul id="ft-bottom-link-left">
            <li><a href="http://www.ftv-publicite.fr/" target="_blank" title="Devenir annonceur">Devenir annonceur</a></li>
            <li><a href="http://recrutement.francetelevisions.fr/" target="_blank" title="Recrutement">Recrutement</a></li>
            <li><a href="http://www.francetv.fr/confidentialite#mentions-legales" target="_blank" title="Mentions légales">Mentions légales</a></li>
            <li><a href="http://www.francetv.fr/confidentialite#cgu" target="_blank" title="CGU">CGU</a></li>
            <li><a href="http://www.francetv.fr/confidentialite#gestion-cookies" target="_blank" title="Gestion Cookies">Gestion Cookies</a></li>
            <li class="last-item"><a href="http://www.francetv.fr/confidentialite#politique-confidentialite" target="_blank" title="Politique de confidentialité">Politique de confidentialité</a></li>
          </ul>
          <div id="ft-bottom-copyright"><span>© 2015 France Télévisions</span></div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
</body>
</html>
