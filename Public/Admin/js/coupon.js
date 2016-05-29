var coupon = {
    
    coupon_edit: function(){
        $('.datetime').datetimepicker({format: "YYYY-MM-DD HH:mm"});
        $("#wikiForm").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg);
                        if(parseInt(data.status)){
                            window.location.reload();
                        }
                    }, dataType: 'json'
                }); 
            }
        });
    },index:function(){
        $(".list-delete").css({"cursor":"pointer"}).on("click",function(){
            if(!confirm("确定删除?")){
                return false;
            }
            var _id = $(this).attr("_id");
            if(_id){
                btsalert.loading();
                $.post("?",{"action":"delete_coupon","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
    }
};

