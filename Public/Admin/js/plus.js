var plus = {
    index:function(){
        
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
                $.post("?",{"action":"del_plus","id":_id},function(data){
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
                $.post("?",{"action":"order_plus","sort_order":_sort},function(data){
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
                $.post("?",{"action":"order_plus","sort_order":_sort},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
         });
         
         $("#icon-select-btn").css({"cursor":"pointer"}).on("click",function(){
             var _t = $(this);
             _fa = _t.attr('fa');
            wzsIconSelect.callBack = function(path){
               _t.removeClass(_fa).addClass(path).attr("fa",path);
                $("input[name=plus_icon]").val(path);
            }
            wzsIconSelect.rtnIconModal();
        });
    }
};

