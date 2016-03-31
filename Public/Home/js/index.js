$(function(){
	if($('.ui-slider').length){
		var slider = new fz.Scroll('.ui-slider', {
	        role: 'slider',
	        indicator: true,
	        autoplay: true,
	        interval: 3000
	    });
	}
	
})