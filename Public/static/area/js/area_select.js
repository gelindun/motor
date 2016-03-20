var area_obj = {
	tabs : ".area-content .tabs .tab",
	cont : ".area-content .mc",
	layer : 3,
	loadArea : function(pid,index){
		var _t = this,_cont = $(_t.cont)
		var pid = pid||0,index=index||0;
		_cont.eq(index).empty().html('<div class="iloading">正在加载中，请稍候...</div>');
		$.get('/Area/loadArea',{"pid":pid},function(data){
			_t.buildArea(data,index);
		},'json')
	},initArea : function(){
		var _t = this;
		_t.loadArea();
		$(_t.cont).on("click",".area-list li",function(){
			pid = $(this).find("a").attr("data-value");
			area = $(this).find("a").html();
			$(this).siblings("li").removeClass("curr");
			$(this).addClass("curr");
			index = $(this).parents(".mc").attr("data-area");
			$(_t.tabs).find("li").eq(index).find("em").html(area);
			index++;

			if(index < _t.layer){
				$(_t.tabs).find("li").removeClass("curr");
				$(_t.tabs).find("li").eq(index).addClass("curr");
				_t.loadArea(pid,index);
			}
		});
		$(_t.tabs).on("click","li",function(){
			$(this).siblings("li").removeClass("curr");
			$(this).addClass("curr");
			index = $(this).attr("data-index");
			$(_t.cont).css({"display":"none"});
			_cont.eq(index).css({"display":"block"})
		})
	},buildArea(data,index){
		var _t = this;
		_cont = $(_t.cont)
		var _area_list = '<ul class="area-list">';
			$.each(data.result.lists,function(i,n){
				if(i == 0){
					$(_t.tabs).find("li").eq(index).find("em").html(n.area);
				}
				_area_list += '<li><a href="javascript:void(0)" data-value="'+n.id+'">'+n.area+'</a></li>'
			})
		_area_list += '</ul>'
		_cont.css({"display":"none"});
		_cont.eq(index).css({"display":"block"}).empty().html(_area_list);
	}
}







