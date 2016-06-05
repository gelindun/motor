var Wechat = {
    news:function(){
        $(".list-delete").css({"cursor":"pointer"}).on("click",function(){
            if(!confirm("确定删除?")){
                return false;
            }
            var _id = $(this).attr("_id");
            if(_id){
                btsalert.loading();
                $.post("?",{"action":"delete_news","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
        window.onload = window.onresize = function(){
            var _w = parseFloat($(".news-item").eq(0).width()) + 2*parseFloat($(".news-item").css("padding-left"));
    
            $('#reply-blk').masonry({itemSelector:'.news-item', columnWidth:(_w+6)});
        }

        
    },menu:function(){
        
        $("#wikiForm").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg,function(){
                            window.location.reload();
                        });
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
                $.post("?",{"action":"delete","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
        
        
        $( ".sortable" ).sortable({
            cursor: "move",
            items :".sort-li",                    
            opacity: 0.6,                      
            revert: true,                      
            update : function(event, ui){       
                var _sort = $(this).sortable("toArray");
                console.log(_sort);
                btsalert.loading();
                $.post("?",{"action":"order","sort_order":_sort},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
         });
         $( ".sub-sortable" ).sortable({
            cursor: "move",
            items :".sub-sort-li",             
            opacity: 0.6,                     
            revert: true,                    
            update : function(event, ui){    
                var _sort = $(this).sortable("toArray");
                btsalert.loading();
                $.post("?",{"action":"order","sort_order":_sort},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
         });
    },link:function(){
        $("#wikiForm").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg,function(){
                            window.location.reload();
                        });
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
                $.post("?",{"action":"delete","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
        
        
        $( ".sortable" ).sortable({
            cursor: "move",
            items :".sort-li",                    
            opacity: 0.6,                      
            revert: true,                      
            update : function(event, ui){       
                var _sort = $(this).sortable("toArray");
                btsalert.loading();
                $.post("?",{"action":"order","sort_order":_sort},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
         });

    },text:function(){
        $("#wikiForm").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg,function(){
                            window.location.reload();
                        });
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
                $.post("?",{"action":"delete","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
        
        
        $( ".sortable" ).sortable({
            cursor: "move",
            items :".sort-li",                    
            opacity: 0.6,                      
            revert: true,                      
            update : function(event, ui){       
                var _sort = $(this).sortable("toArray");
                btsalert.loading();
                $.post("?",{"action":"order","sort_order":_sort},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
         });

    }
}