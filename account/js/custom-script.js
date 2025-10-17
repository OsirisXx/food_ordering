/*================================================================================
	Item Name: Somaiya Vidyavihar - Captive Portal
	Version: 3.1
	Author: Arctech Ventures
	Author URL: http://www.arctechventures.com
================================================================================ */

(function($){
	"use strict";
	// Ensure active menu item still navigates to its href (some setups block default)
	$(document).on('click', '#left-sidebar-nav .side-nav li > a', function(e){
		var $li = $(this).closest('li');
		var href = $(this).attr('href');
		if(href && $li.hasClass('active')){
			// Force navigation explicitly so it redirects instead of silently doing nothing
			window.location.href = href;
			return false;
		}
	});
})(jQuery);
