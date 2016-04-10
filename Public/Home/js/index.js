$(function(){
	if($('.ui-slider').length){
		if($('.ui-slider .ui-slider-content').find("li").length > 0){
			var slider = new fz.Scroll('.ui-slider', {
		        role: 'slider',
		        indicator: true,
		        autoplay: true,
		        interval: 3000
		    });
		}
	}
	
})