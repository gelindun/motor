var _click = "click";
if($.validator){
    $.validator.setDefaults({
        errorPlacement: function (error, element) {
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
                },store_id: {
                    required: "请选择门店"
                },cylinder_id: {
                    required: "请选择发动机缸数"
                },amount:{
                	required: "请选择门店和发动机缸数"
                }
            },
            submitHandler:function(form){
                var el=$.loading({content:'loading...',})
                $(form).ajaxSubmit({
                    success: function (data) {
                        el.hide();
                        if(typeof(data.msg)!="undefined"){
                        	$.tips({
	                            content:data.msg,
	                            stayTime:2000,
	                            type:"success"
	                        })
                        }
                        
                    }, dataType: 'json'
                }); 
            }
        });
        this.fetchSeries();
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
	},fetchAmount(){
		var store_id = parseInt($("select[name=store_id]").val());
		var cylinder_id = parseInt($("input[name=cylinder_id]").val());
        console.log(store_id);
        console.log("cl"+cylinder_id)
		if(!isNaN(store_id)&&!isNaN(cylinder_id)){
			var _obj = {store_id:store_id,cylinder_id:cylinder_id}
			$.post("/Cylinder/fetchAmount",_obj,function(data){
				if(typeof(data.result.amount)!="undefined"){
					$("input[name=amount]").val(parseFloat(data.result.amount));
				}
			},'json');
		}

	},fetchSeries(){
        var car_series = $("select[name=car_series]");
        var car_brand = $("select[name=car_brand]");
        if(car_brand.val()){
            var bid = parseInt(car_brand.val());
            $.get('/CarSeries/fetchSeries',{bid:bid},function(data){
                if(typeof(data)!="undefined"&&typeof(data.result.list)!="undefined"){
                    var _opt = "";
                    $.each(data.result.list,function(i,n){
                        _opt += '<option value="'+n.id+'">'+n.title+'</option>';
                    })
                    car_series.html(_opt);
                }
            },'json')
        }
    }
}