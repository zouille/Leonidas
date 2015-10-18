var isMobile = {
  Mobile: function() {
    return navigator.userAgent.match(/mobile/i);
  },
  iPad: function() {
    return navigator.userAgent.match(/iPad/i);
  },
  Android: function() {
    return navigator.userAgent.match(/Android/i);
  },
  BlackBerry: function() {
    return navigator.userAgent.match(/BlackBerry/i);
  },
  iOS: function() {
    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
  },
  Opera: function() {
    return navigator.userAgent.match(/Opera Mini/i);
  },
  Windows: function() {
    return navigator.userAgent.match(/IEMobile/i);
  },
  AndroidTablets: function() {
    if (navigator.userAgent.match(/Android/i)) {
      if (!navigator.userAgent.match(/mobile/i)) {
        return true;
      }
    }
    return false;
  },
  any: function() {
    return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
  },
  not_tablets: function() {
    return ((isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows()) && !isMobile.AndroidTablets() && !isMobile.iPad());
  }
};
var disableMobileRedirect = cbGetCookie('disableMobileRedirect');
if (!(disableMobileRedirect != undefined && disableMobileRedirect == 'true')
  && isMobile.not_tablets()) {
  if (window.location.host.search("www.") != -1) {
    var destination = window.location.protocol + "//" + window.location.host.replace('www.', 'm.');
  }
  else {
    var destination = window.location.protocol + "//m." + window.location.host;
  }
  window.location = destination + window.location.pathname + window.location.search;
}