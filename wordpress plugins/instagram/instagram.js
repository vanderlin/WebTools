
jQuery(document).ready(function($) {

	$(".instagram-data").slideUp(0);
	
	function overCallBack() {
		$(this).find(".instagram-data").slideDown(200);
	}
	function outCallBack() {
		$(this).find(".instagram-data").slideUp(200);
	}
	
	var settings = {
	    sensitivity: 4,
	    interval: 75,
	    over: overCallBack,
	    out: outCallBack
	}; 
	$(".instagram-item").hoverIntent( settings );

	
});