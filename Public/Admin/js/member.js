var member = {
    
    member_edit: function(){
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
        $(".logo_select").on("click",function(){
            wzsImgUpload.callBack = function(path){
                if($(".case_logo").find("img").length<1){
                    $("<img >").appendTo(".case_logo");
                }
                $(".case_logo").find("img").attr("src",path);
                $("input[name=head_img]").val(path);
            }
            wzsImgUpload.rtnFileModal();
        });
    },index:function(){
        $(".list-delete").css({"cursor":"pointer"}).on("click",function(){
            if(!confirm("确定删除?")){
                return false;
            }
            var _id = $(this).attr("_id");
            if(_id){
                btsalert.loading();
                $.post("?",{"action":"delete_user","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
    }
};

