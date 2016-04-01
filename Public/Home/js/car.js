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
car_obj = {
    index:function(){
        var _t = this;
        $("#carForm").validate({
            rules: {
                plate_num:{
                    required: true,
                    plateNum:true
                },
                car_series:{
                    required: true
                }
            },
            messages: {plate_num: {
                    required: "请填写车牌号"
                },car_series: {
                    required: "请选择车型"
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
    }
}