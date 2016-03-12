var login = {
    
    index: function () {
        $("#LoginForm").validate({
            submitHandler: function (form) {
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg,function(){
                            if(data.result && data.result.redirect){
                                window.location.href = data.result.redirect
                            }
                        });
                        
                    }, dataType: 'json'
                });
            }
        });
    }
}