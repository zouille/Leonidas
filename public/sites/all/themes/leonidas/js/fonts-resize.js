/* Text changer - light version.
Let your text's font size customizable.
by Marco Rosella - http://www.centralscrutinizer.it/en/design/js-php/text-changer
v0.2 - May 18, 2006
*/
function initFontResize() {
	textChanger.init();
}
var textChanger = {
	defaultFS : 1.5,
	init: function() {
		var el = document.getElementsByTagName("body")[0];
		var sz = textChanger.getCookie();
		el.style.fontSize = sz ? sz + 'em' : textChanger.defaultFS + 'em';
		var incr = document.getElementById('increase');
		if(incr)
			incr.onclick = function(){textChanger.changeSize(1); return false;};
		var decr = document.getElementById('decrease');
		if(decr)
			decr.onclick = function(){textChanger.changeSize(-1); return false;};
		var reset = document.getElementById('reset');
		if(reset)
			reset.onclick = function(){textChanger.changeSize(0); return false;};
	},
	changeSize: function(val) {
		var el = document.getElementsByTagName("body")[0];
		var size = el.style.fontSize.substring(0, 4);
		var fSize = parseFloat(size, 10);
		if (val == 1)	{
			fSize += 0.1;
			if (fSize > textChanger.defaultFS*1.5) fSize = textChanger.defaultFS*1.5;
		} 
		if (val == -1) {
			fSize -= 0.1;
			if (fSize < textChanger.defaultFS/1.5) fSize = textChanger.defaultFS/1.5;
		}       
		if (val == 0) fSize = textChanger.defaultFS;
		el.style.fontSize = fSize.toFixed(2) + 'em';
		textChanger.updateCookie(fSize);
	},
	updateCookie: function(vl)	{
		var today = new Date();
		var exp = new Date(today.getTime() + (365*24*60*60*1000));
		document.cookie = 'textChangerL=size=' + vl + ';' +'expires=' + exp.toGMTString() + ';' +'path=/';
	},
	getCookie: function()	{ 
		var cname = 'textChangerL=size=';   
		var start = document.cookie.indexOf(cname);
		var len = start + cname.length;
		if ((!start) && (cname != document.cookie.substring(0,cname.length))) {return null;}
		if (start == -1) return null;
		var end = document.cookie.indexOf(";",len);
		if (end == -1) end = document.cookie.length;
		return unescape(document.cookie.substring(len, end));
	}
}
if (window.addEventListener)
	window.addEventListener("load", initFontResize, false);
else if (window.attachEvent && !window.opera)
	window.attachEvent("onload", initFontResize);