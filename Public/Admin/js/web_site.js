var web_site = {
    s_Handler:function(form){
        
    },
    site_base: function () {
        $("#siteBase").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg);
                    }, dataType: 'json'
                }); 
            }
        });
    },
    note_edit:function(){
        $('.datetime').datetimepicker({format: "YYYY-MM-DD HH:mm"});
        $("#wikiForm").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg);
                        if(parseInt(data.status)){
                            $(form)[0].reset();
                        }
                    }, dataType: 'json'
                }); 
            }
        });
    },note_list:function(){
        $(".note-delete").css({"cursor":"pointer"}).on("click",function(){
            if(!confirm("确定删除?")){
                return false;
            }
            var _id = $(this).attr("_id");
            if(_id){
                btsalert.loading();
                $.post("?",{"action":"delete_note","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
    },case_list:function(){
        $(".list-delete").css({"cursor":"pointer"}).on("click",function(){
            if(!confirm("确定删除?")){
                return false;
            }
            var _id = $(this).attr("_id");
            if(_id){
                btsalert.loading();
                $.post("?",{"action":"delete_case","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
    },case_edit:function(){
        $('.datetime').datetimepicker({format: "YYYY-MM-DD HH:mm"});
        $("#wikiForm").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg);
                        if(parseInt(data.status)){
                            $(form)[0].reset();
                        }
                    }, dataType: 'json'
                }); 
            }
        });
        $(".qr_select").on("click",function(){
            wzsImgUpload.callBack = function(path){
                if($(".case_qr_code").find("img").length<1){
                    $("<img >").appendTo(".case_qr_code");
                }
                $(".case_qr_code").find("img").attr("src",path);
                $("input[name=qr_code]").val(path);
            }
            wzsImgUpload.rtnFileModal();
        });
        $(".logo_select").on("click",function(){
            wzsImgUpload.callBack = function(path){
                if($(".case_logo").find("img").length<1){
                    $("<img >").appendTo(".case_logo");
                }
                $(".case_logo").find("img").attr("src",path);
                $("input[name=logo]").val(path);
            }
            wzsImgUpload.rtnFileModal();
        });
        $(".pic_url_select").on("click",function(){
            wzsImgUpload.callBack = function(path){
                if($(".pic_url").find("img").length<1){
                    $("<img >").appendTo(".pic_url");
                }
                $(".pic_url").find("img").attr("src",path);
                $("input[name=pic_url]").val(path);
            }
            wzsImgUpload.rtnFileModal();
        });
    },article_list:function(){
        $(".list-delete").css({"cursor":"pointer"}).on("click",function(){
            if(!confirm("确定删除?")){
                return false;
            }
            var _id = $(this).attr("_id");
            if(_id){
                btsalert.loading();
                $.post("?",{"action":"delete_article","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
    },article_edit:function(){
        $('.datetime').datetimepicker({format: "YYYY-MM-DD HH:mm"});
        $("#wikiForm").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg);
                        if(parseInt(data.status)){
                            $(form)[0].reset();
                        }
                    }, dataType: 'json'
                }); 
            }
        });
        $(".logo_select").on("click",function(){
            wzsImgUpload.callBack = function(path){
                if($(".case_logo").find("img").length<1){
                    $("<img >").appendTo(".case_logo");
                }
                $(".case_logo").find("img").attr("src",path);
                $("input[name=logo]").val(path);
            }
            wzsImgUpload.rtnFileModal();
        });
    },article_type:function(){
        $(".logo_select").on("click",function(){
            wzsImgUpload.callBack = function(path){
                if($(".case_logo").find("img").length<1){
                    $("<img >").appendTo(".case_logo");
                }
                $(".case_logo").find("img").attr("src",path);
                $("input[name=logo]").val(path);
            }
            wzsImgUpload.rtnFileModal();
        });
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
                $.post("?",{"action":"del_article_type","id":_id},function(data){
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
                $.post("?",{"action":"order_article_type","sort_order":_sort},function(data){
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
                $.post("?",{"action":"order_article_type","sort_order":_sort},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
         });
    },about:function(){
        $('.datetime').datetimepicker({format: "YYYY-MM-DD HH:mm"});
        $("#wikiForm").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg);
                    }, dataType: 'json'
                }); 
            }
        });
    }
};

