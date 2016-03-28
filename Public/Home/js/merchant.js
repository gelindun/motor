var store_dis = 0, _lang_tip = "约";
var merchant = {
	index : function(){
		var getRad=function(d){
			return d*(Math.PI)/180.0;
		}
		
		var getDistance=function(lat1, lng1, lat2, lng2){
			lat1=lat1*1;
			lng1=lng1*1;
			lat2=lat2*1;
			lng2=lng2*1;
			var f=getRad((lat1+lat2)/2);
			var g=getRad((lat1-lat2)/2);
			var l=getRad((lng1-lng2)/2);
			
			var sf=Math.sin(f);
			var sg=Math.sin(g);
			var sl=Math.sin(l);
			
			var s, c, w, r, d, h1, h2;
			var fl=1/298.257;
			
			sg=sg*sg;
			sl=sl*sl;
			sf=sf*sf;
			
			s=sg*(1-sl)+(1-sf)*sl;
			c=(1-sg)*(1-sl)+sf*sl;
			
			w=Math.atan(Math.sqrt(s/c));
			r=Math.sqrt(s*c)/w;
			d=2*w*6378137.0;
			h1=(3*r-1)/2/c;
			h2=(3*r+1)/2/s;
			
			return d*(1+fl*(h1*sf*(1-sg)-h2*(1-sf)*sg));
		};
		
		var getFriendDistance=function(lat1, lng1, lat2, lng2){
			var dis=0;
			if(arguments.length==1){
				dis=lat1;
			}else{
				dis=getDistance(lat1, lng1, lat2, lng2);
			}
			store_dis = dis;
			console.log(store_dis)
			if(dis<10000){
				return _lang_tip + (dis>>0)+'m';
			}else{
				return _lang_tip + ((dis/1000)>>0)+'km';
			}
		};
		
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
		
		var geolocation=new BMap.Geolocation();
		geolocation.getCurrentPosition(function(pos){
			showNearest(pos.point.lat, pos.point.lng);
		});
	}
}

