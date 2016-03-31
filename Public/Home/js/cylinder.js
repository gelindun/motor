var _click;
if('ontouchstart' in window){
   _click = "touchstart"
}else{
   _click = "click"
}
if($.validator){
    $.validator.setDefaults({
        errorPlacement: function (error, element) {
            if($(".ui-poptips-error").length < 1)
                el_1 = $.tips({
                    content:error.html(),
                    stayTime:1500,
                    type:"error"
                })
        },
        errorElement: "i",
    });
}
cy_obj = {
    index:function(){
        var _t = this;
        $("#cylinderForm").validate({
            rules: {
                nickName: {
                    required: true
                },mobile: {
                    required: true,
                    mobile:true,
                },plate_num:{
                    required: true,
                    plateNum:true
                },
                car_series:{
                    required: true
                },
                store_id:{
                    required: true
                },
                cylinder_id:{
                    required: true
                },amount:{
                    required: true
                }
            },
            messages: {
                nickName: {
                    required: "请填写称呼，方便售后联系您"
                },mobile: {
                    required: "请填写手机号，方便售后联系您"
                },plate_num: {
                    required: "请填写车牌号，方便售后联系您"
                },car_series: {
                    required: "请选择车型"
                },store_id: {
                    required: "请选择门店"
                },cylinder_id: {
                    required: "请选择发动机缸数"
                },amount:{
                    required: "请选择门店和发动机缸数"
                }
            },
            onkeyup:false,
            submitHandler:function(form){
                var el=$.loading({content:'loading...',})
                $(form).ajaxSubmit({
                    success: function (data) {
                        el.hide();
                        if(typeof(data.msg)!="undefined"){
                            var tip = $.tips({
                                content:data.msg,
                                stayTime:1000,
                                type:"error"
                            })
                            tip.on("tips:hide",function(){
                                if (data.result && data.result.url) {
                                    window.location.href = data.result.url
                                }
                            })
                        }

                        
                    }, dataType: 'json'
                }); 
            }
        });
        $("select[name=car_brand]").on("change",function(){
            _t.fetchSeries()
        })

        _t.fetchSeries();
        $("select[name=store_id]").on("change",function(){
            _t.fetchAmount();
        });
        $(".cylinder_tag").on(_click,'.ui-label',function(){
            $(".cylinder_tag").find(".ui-label").removeClass("ui-tag-selected");
            $(this).addClass("ui-tag-selected");
            var _tag = $(this).attr("data-id");
            $("input[name=cylinder_id]").val(_tag);
            _t.fetchAmount();
        });
        _t.fetchAmount();
        _t.storeInit();
    },fetchAmount:function(){
        var store_id = parseInt($("select[name=store_id]").val());
        var cylinder_id = parseInt($("input[name=cylinder_id]").val());
        if(!isNaN(store_id)&&!isNaN(cylinder_id)){
            var _obj = {store_id:store_id,cylinder_id:cylinder_id}
            $.post("/Cylinder/fetchAmount",_obj,function(data){
                if(typeof(data.result.amount)!="undefined"){
                    $("input[name=amount]").val(parseFloat(data.result.amount));
                }
            },'json');
        }

    },fetchSeries:function(){
        var car_series = $("select[name=car_series]");
        var car_brand = $("select[name=car_brand]");
        if(car_brand.val()){
            var bid = parseInt(car_brand.val());
            $.get('/CarSeries/fetchSeries',{bid:bid},function(data){
                if(typeof(data)!="undefined" && typeof(data.result.list)!="undefined"){
                    var _opt = "";
                    $.each(data.result.list,function(i,n){
                        _opt += '<option value="'+n.id+'">'+n.title+'</option>';
                    })
                    car_series.html(_opt);
                }
            },'json')
        }
    },storeInit:function(){
        //计算距离，显示最近的
        var _t = this;
        var showNearest=function(lat1, lng1){
            var nearest_index=-1, nearest_dis=-1;
            var _obj = new Array();
            $('#stores option').each(function(index){
                var lat = $(this).attr('lat'), lng=$(this).attr('lng');
                if(lat && lng){
                    var dis=getDistance(lat, lng, lat1, lng1);
                    var dis_f=getFriendDistance(dis);
                     if(dis)
                        _obj.push( {"dis":dis,"index": index} );
                    $(this).attr({'dis': store_dis,'disf':dis_f});
                    if(nearest_dis<0 || dis<nearest_dis){
                        nearest_dis = dis;
                        nearest_index = index;
                    }
                }
            }); 
            _obj.sort(function(a,b){
                return parseFloat(a.dis) - parseFloat(b.dis); 
            });
            $("body").append("<div id='temp_div'></div>");
            $.each(_obj,function(i,n){
                $('#stores option').eq(n.index).clone().appendTo($('#temp_div'));
            })
            $('#stores option').remove();
            $('#stores').append($('#temp_div option'));
            
            $("#temp_div").remove();
            var _msg = "最近的门店距离您"+$("#stores option").eq(0).attr("disf")+"请确认后手动选择门店";
            
            if(store_dis > 300){
                $("<option>请选择门店名称</option>").prependTo($('#stores'));
                var dia = $.dialog({
                            title:'温馨提示',
                            content:_msg,
                            button:["确定"]
                        });
            }else{
                $("#stores option").eq(0).addClass('nearest').attr("selected","selected");
            }
            _t.fetchAmount();
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