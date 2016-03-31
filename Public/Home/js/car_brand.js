var _click;
if('touchstart' in window){
   _click = "touchstart"
}else{
   _click = "click"
}
var car_brand = {
	brand_pop : "#brand-pop",
	brand_mask : ".brand-mask",
	ui_series : ".car-ui-series",
	init : function(callBack){
		var _t = this;
		$("#brand-pop").on(_click,_t.brand_mask,function(){
			_t.hide_series();
			return false;
		})
		$("#brand-pop").on(_click,".car-series-li",function(){
			_t.hide_series();
			var _obj = {
				"brand" : $(_t.ui_series).attr("data-brand"),
				"series":$(this).attr("data-series"),
				"seriesTitle": $(this).attr("data-series-title"),
				"brandTitle": $(_t.ui_series).attr("data-brand-title")
			}
			callBack(_obj)
			setTimeout(function(){
				$(_t.brand_pop).hide();
			},500)
			return false;
		})
		$("#brand-pop").on(_click,".car-brand-li",function(){
			var data_brand = $(this).attr("data-brand");
			var title_brand = $(this).attr("data-brand-title");
			_t.pop_series(data_brand,title_brand);
			return false;
		})

	},pop_brand:function(){
		var _t = this;
		$.get('/CarSeries/rtnBrand',{},function(data){
			$(_t.brand_pop).html(data);
			$(_t.brand_pop).show();
		})
		
	},hide_brand:function(){
		var _t = this;
		_t.hide_series();
		$(_t.brand_pop).hide();
	},pop_series:function(data_brand,title_brand){
		var _t = this,data_brand = data_brand||0;
		$.get('/CarSeries/rtnSeries',{brand:data_brand},function(data){
			$(_t.brand_pop).append(data);
			$(_t.brand_pop).find(_t.brand_mask).show();
			setTimeout(function(){
				$(_t.brand_pop).find(_t.ui_series).addClass('active')
				.attr({"data-brand":data_brand,"data-brand-title":title_brand});
			},100)
		})
		
	},hide_series:function(){
		var _t = this;
		$(_t.brand_pop).find(_t.ui_series).removeClass('active')
		setTimeout(function(){
			$(_t.brand_pop).find(_t.brand_mask).hide();
		},500)
	}
}