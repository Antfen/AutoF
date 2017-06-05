;(function (window, document, $, undefined) {
	"use strict";

	function getScript(src) {
		document.write('<' + 'script src="' + src + '"' + ' type="text/javascript"><' + '/script>');
	}

	if(typeof window.google === 'undefined' || typeof google.maps === 'undefined'){
		getScript(document.location.protocol + '//maps.google.com/maps/api/js');
	}
}(window, document, jQuery));