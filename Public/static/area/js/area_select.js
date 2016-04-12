var area_obj = {
	tabs : ".area-content .tabs .tab",
	cont : ".area-content .mc",
	layer : 3,
	city_str :"input[name=city_str]",
	area_str :"input[name=area_str]",
	area_arr : [],
	loadArea : function(pid,index){
		var _t = this,_cont = $(_t.cont)
		var pid = pid||0,index=index||0;
		_cont.eq(index).empty().html('<div class="iloading">正在加载中，请稍候...</div>');
		$(_t.tabs).find("li").removeClass("curr");
		$(_t.tabs).find("li").eq(index).addClass("curr");
		$.get('/Area/loadArea',{"pid":pid},function(data){
			_t.buildArea(data,index);
		},'json')
	},initArea : function(){
		var _t = this;
		_t.area_arr = $(_t.area_str).val().split(',');
		
		if(_t.area_arr.length){
			_t.loadArea(0,0);
			
		}else{
			_t.loadArea();
		}
		
		
		$(_t.cont).on("click",".area-list li",function(){
			pid = $(this).find("a").attr("data-value");
			area = $(this).find("a").html();
			$(this).siblings("li").removeClass("curr");
			$(this).addClass("curr");
			index = $(this).parents(".mc").attr("data-area");
			$(_t.tabs).find("li").eq(index).find("em").html(area);
			index++;
			_str = '',city_str = '';
			$.each($(_t.cont).find("li.curr a"),function(i,n){
				if(i > 0){
					_str += ',';
					city_str += ',';
				};
				_str += $(n).attr("data-value");
				city_str += $(n).attr("data-city");
			})
			if($(_t.city_str).length > 0){
				$(_t.city_str).val(city_str);
				if($('#locBtn').length > 0){
					$('#locBtn').trigger("click");
				}
			}
			$(_t.area_str).val(_str);
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
	},buildArea:function(data,index){
		var _t = this;
		_cont = $(_t.cont);
		curr = 0;
		if(typeof(_t.area_arr[0])!="undefined"){
			curr = _t.area_arr[0];
			_t.area_arr.shift();
		}
		
		var _area_list = '<ul class="area-list">';
			$.each(data.result.lists,function(i,n){
				if(i == 0){
					$(_t.tabs).find("li").eq(index).find("em").html(n.area);
				}
				_area_list += '<li><a href="javascript:void(0)" data-city="'+n.area+'" data-value="'+n.id+'">'+n.area+'</a></li>'
			})
		_area_list += '</ul>'
		_cont.css({"display":"none"});
		_cont.eq(index).css({"display":"block"}).empty().html(_area_list);
		if(curr){
			_cont.eq(index).find("li a[data-value="+curr+"]").parent().addClass('curr');
			$(_t.tabs).find("li").eq(index).find("em").html(_cont.eq(index).find("li a[data-value="+curr+"]").html());
		}
		if(typeof(_t.area_arr[0])!="undefined"){
			_t.loadArea(curr,++index);
		}
	}
}







