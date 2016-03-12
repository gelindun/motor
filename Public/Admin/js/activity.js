var activity = {
    init:function(){
        $(".list-recom").css({"cursor":"pointer"}).on("click",function(){
            if(!confirm("确定操作?")){
                return false;
            }
            var _id = $(this).attr("_id");
            var status = $(this).attr("_isrecom");
            if(_id){
                btsalert.loading();
                $.post("?",{"action":"recom_activity","id":_id,"is_recom":status},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
    }
};

