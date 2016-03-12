var system = {
    
    edit: function(){
       
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
        
    },add:function(){
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
        $(".list-delete").css({"cursor":"pointer"}).on("click",function(){
            if(!confirm("确定删除?")){
                return false;
            }
            var _id = $(this).attr("_id");
            if(_id){
                btsalert.loading();
                $.post("?",{"action":"delete_admin","aid":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
    }
}