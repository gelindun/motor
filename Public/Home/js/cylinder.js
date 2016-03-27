if($.validator){
    $.validator.setDefaults({
        errorPlacement: function (error, element) {
            //$(element).prev("label").append(error);
            el_1 = $.tips({
                content:error.html(),
                stayTime:1000,
                type:"error"
            })
        },
        errorElement: "i",
    });
}
cy_obj = {
	index:function(){
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
	}
}