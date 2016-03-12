var resource = {
    
    index: function(){
       $(".content").on("click",".act-remove",function(){
            var _id = $(this).attr("_id");

            if(!confirm("确定删除?")){
                 return false;
            }
            if(_id){
                btsalert.loading();
                $.post("?",{"action":"delete_res","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        if(parseInt(data.status) > 0)window.location.reload();
                    });
                },"json");
            };
            e = e || window.event;  
            if(e.stopPropagation) {  
                e.stopPropagation();  
            } else {  
                e.cancelBubble = true;  
            } 
       })
    }
};

