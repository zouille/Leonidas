var state_list;
function initMap() {
	var _map1 = document.getElementById("map1");

	if (_map1) {
		createMap(_map1);
	}
}

function createMap(_map) {
	state_list = document.getElementById("map-hover-side").getElementsByTagName("li");
	var _areas = _map.getElementsByTagName("area");
		for (i = 0; i < _areas.length; i ++) {
			if (_areas[i].alt) {
				var _node = document.getElementById(_areas[i].alt);
				if (_node) {
					_areas[i]._node = _node;
					_areas[i].onmouseover = function() {
						if (this._node.className.indexOf("activestate") == -1)
						{
							this._node.className += " activestate";

						}
					}
					_areas[i].onmouseout = function() {
						this._node.className = this._node.className.replace("activestate", "");
					}
				}
			}
		}
}
if (window.addEventListener){
	window.addEventListener("load", initMap, false);
}
else if (window.attachEvent){
	window.attachEvent("onload", initMap);
}