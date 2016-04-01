var _click;
if('touchstart' in window){
   _click = "touchstart"
}else{
   _click = "click"
}
var car_mine = {
	car_pop : "#car-pop",
	init : function(callBack){
		var _t = this;
		$(_t.car_pop).on(_click,".car-mine-li",function(){
			var _obj = {
				"plateNum":$(this).attr("data-platenum"),
				"seriesTitle":$(this).attr("data-title"),
				"brand":$(this).attr("data-bid"),
				"series":$(this).attr("data-sid"),
			}
			callBack(_obj);
			return false;
		})
		$(_t.car_pop).on(_click,".hide-car-pop",function(){
			_t.hide_car();
			return false;
		})
		

	},pop_car:function(){
		var _t = this;
		var el=$.loading({content:'loading...',})
		$.get('/CarSeries/rtnMine',{},function(data){
			$(_t.car_pop).html(data);
			$(_t.car_pop).show();
			el.hide();
		})
		
	},hide_car:function(){
		var _t = this;
		$(_t.car_pop).hide();
	}
}