var merchant = {
	index : function(){
		
		
    	//计算距离，显示最近的
		var showNearest=function(lat1, lng1){
			var nearest_index=-1, nearest_dis=-1;
            var _obj = new Array();
			$('#stores .store').each(function(index){
				var lat=$(this).attr('lat'), lng=$(this).attr('lng');
				if(lat && lng){
					var dis=getDistance(lat, lng, lat1, lng1);
					var dis_f=getFriendDistance(dis);
                     if(dis)
                    	_obj.push( {"dis":dis,"index": index} );
					$(this).attr('dis', store_dis);
					if(nearest_dis<0 || dis<nearest_dis){
						nearest_dis = dis;
						nearest_index = index;
					}
					$('.dis', $(this)).html(dis_f).show();
				}
			});	
            _obj.sort(function(a,b){
                return parseFloat(a.dis) - parseFloat(b.dis); 
            });
            $("body").append("<div id='temp_div'></div>");
            $.each(_obj,function(i,n){
                $('#stores .store').eq(n.index).clone().appendTo($('#temp_div'));
            })
            $('#stores .store').remove();
            $('#stores ul').append($('#temp_div .store'));
			$("#stores .store").eq(0).addClass('nearest');
            $("#temp_div").remove();
                                    
		};
		
		renderReverse=function(response){
			var addr=response.result.formatted_address;
			if(addr){
				//$('#your_address').show();
				//$('#your_address dd').html(addr);
			}
		}
		
		var geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(pos){
			showNearest(pos.point.lat, pos.point.lng);
		});
	}
}

