$(function(){
    if($(".menu-footer li").length){
        $(".menu-footer").on("click","li",function(){
            var _t = $(this);
            var _url = _t.attr("data-href");
            if(_url){
                window.location.href = _url;
            }
        })
    }
    $("article.widget-body").find("img").css({"max-width":"100%","height":"auto"});
    if($(".rtn-back").length){
        $(".rtn-back").on("click",function(){
            if(document.referrer){
                window.history.back();
            }else{
                window.location.href = "/"
            }
        })
    }
    if($(".rtn-home").length){
        $(".rtn-home").on("click",function(){
            window.location.href = "/"
        })
    }
    $("img").css({"max-width":"100%","height":"auto"});
    
})

function tipAlert(msg,callBack){
    var callBack = callBack||function(){};
    var tip = $.tips({
                        content:msg,
                        stayTime:2000,
                        type:"info"
                    })
    tip.on("tips:hide",function(){
        callBack();
    })
}

function isWeixin(){
    var ua = navigator.userAgent.toLowerCase();  
    if(ua.match(/MicroMessenger/i)=="micromessenger") {  
        return true;  
    } else {  
        return false;  
    }  
}