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

function getRad(d){
    return d*(Math.PI)/180.0;
}

function getDistance(lat1, lng1, lat2, lng2){
    lat1=lat1*1;
    lng1=lng1*1;
    lat2=lat2*1;
    lng2=lng2*1;
    var f=getRad((lat1+lat2)/2);
    var g=getRad((lat1-lat2)/2);
    var l=getRad((lng1-lng2)/2);
    
    var sf=Math.sin(f);
    var sg=Math.sin(g);
    var sl=Math.sin(l);
    
    var s, c, w, r, d, h1, h2;
    var fl=1/298.257;
    
    sg=sg*sg;
    sl=sl*sl;
    sf=sf*sf;
    
    s=sg*(1-sl)+(1-sf)*sl;
    c=(1-sg)*(1-sl)+sf*sl;
    
    w=Math.atan(Math.sqrt(s/c));
    r=Math.sqrt(s*c)/w;
    d=2*w*6378137.0;
    h1=(3*r-1)/2/c;
    h2=(3*r+1)/2/s;
    
    return d*(1+fl*(h1*sf*(1-sg)-h2*(1-sf)*sg));
};
var store_dis = 0;
function getFriendDistance(lat1, lng1, lat2, lng2){
    var dis=0,_lang_tip = "约";;
    if(arguments.length==1){
        dis=lat1;
    }else{
        dis=getDistance(lat1, lng1, lat2, lng2);
    }
    store_dis = dis;
    if(dis<10000){
        return _lang_tip + (dis>>0)+'m';
    }else{
        return _lang_tip + ((dis/1000)>>0)+'km';
    }
};

