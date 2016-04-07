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

var my = {
    index:function(){
        
    },profile:function(){
        $("#profileForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                email: {
                    required: "请输入邮箱地址",
                    email: "邮箱地址格式错误,请检查"
                }
            },
            submitHandler:function(form){
                var el=$.loading({content:'loading...',})
                $(form).ajaxSubmit({
                    success: function (data) {
                        el.hide();
                        $.tips({
                            content:data.msg,
                            stayTime:2000,
                            type:"success"
                        })
                    }, dataType: 'json'
                }); 
            }
        });
    },passwd:function(){
        $("#passwdForm").validate({
            rules: {
                passwd: {
                    required: true
                },
                newpasswd: {
                    required: true,
                    rangelength: [5, 15]
                },
                repasswd: {
                    required: true,
                    rangelength: [5, 15],
                    equalTo: "#newpasswd"
                }
            },
            messages: {
                passwd: {
                    required: "请输入原始密码"
                },
                newpasswd: {
                    required: "请输入新密码",
                    rangelength: "密码为5-15个长度之间的数字或字母"
                },
                repasswd: {
                    required: "请输入确认密码",
                    rangelength: "密码为5-15个长度之间的数字或字母",
                    equalTo: "两次密码不一样"
                }
            },
            submitHandler: function (form) {
                var el = $.loading({content:'loading...',})
                $(form).ajaxSubmit({
                    success: function (data) {
                        el.hide();
                        var tip = $.tips({
                            content:data.msg,
                            stayTime:1000,
                            type:"error"
                        })
                        tip.on("tips:hide",function(){
                            if (data.result && data.result.redirect) {
                                window.location.href = data.result.redirect
                            }
                        })
                        

                    }, dataType: 'json'
                });
            }
        });
    },bind:function(){
        
    },ticket:function(){
        $(".tb-ticket tbody").infinitescroll({
                navSelector:'.pagination',
                nextSelector:'.pagination a.next',
                itemSelector:'.tb-ticket tbody tr'
            });
    },follow:function(){
        $(".tb-ticket tbody").infinitescroll({
                navSelector:'.pagination',
                nextSelector:'.pagination a.next',
                itemSelector:'.tb-ticket tbody tr'
            });
    },order:function(){
       if($(".profile-warp").length){
            $(".profile-warp").infinitescroll({
                navSelector:'.pagination',
                nextSelector:'.pagination a.next',
                itemSelector:'.ticket-order-list'
            });
        }
    },login: function () {
        $("#LoginForm").validate({
            rules: {
                uname: {
                    required: true
                },
                upwd: {
                    required: true
                }
            },
            messages: {
                uname: {
                    required: "请输入用户名"
                },
                upwd: {
                    required: "请输入密码"
                }
            },
            submitHandler: function (form) {
                $(form).ajaxSubmit({
                    success: function (data) {
                        var tip = $.tips({
                            content:data.msg,
                            stayTime:1000,
                            type:"error"
                        })
                        tip.on("tips:hide",function(){
                            if (data.result && data.result.redirect) {
                                window.location.href = decodeURIComponent(data.result.redirect)
                            }
                        })
                    }, dataType: 'json'
                });
            }
        });
    },
    register: function () {
        $("#RegisterForm").validate({
            rules: {
                uname: {
                    required: true,
                    minlength: 2,
                    maxlength: 15
                },
                upwd: {
                    required: true,
                    rangelength: [5, 15]
                },
                repassword: {
                    required: true,
                    rangelength: [5, 15],
                    equalTo: "input[type='password']"
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                uname: {
                    required: "请输入用户名",
                    minlength: "最小长度2",
                    maxlength: "最大长度15"
                },
                upwd: {
                    required: "请输入密码",
                    rangelength: "密码为5-15个长度之间的数字或字母"
                },
                repassword: {
                    required: "请输入确认密码",
                    rangelength: "密码为5-15个长度之间的数字或字母",
                    equalTo: "两次密码不一样"
                },
                email: {
                    required: "请输入邮箱",
                    email: "邮箱格式不正确"
                }
            },
            submitHandler: function (form) {
                $(form).ajaxSubmit({
                    success: function (data) {
                        var tip = $.tips({
                            content:data.msg,
                            stayTime:1000,
                            type:"error"
                        })
                        tip.on("tips:hide",function(){
                            if (data.result && data.result.redirect) {
                                window.location.href = data.result.redirect
                            }
                        })
                    }, dataType: 'json'
                });
            }
        });
    }
}