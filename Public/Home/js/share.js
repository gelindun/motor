if (share_img = "", $("img").length && (imgs = $("img")) && imgs[0]) {
    share_img = $(imgs[0]).attr("src");
}
if(typeof(dataForWeixin)=="undefined"){
    dataForWeixin = {
        img: $("body").eq(0).attr('itokit-img') || share_img,
        url: $("body").eq(0).attr('itokit-url') || window.location.href,
        title: $("body").eq(0).attr('itokit-title') || $("title").html(),
        desc: $("body").eq(0).attr('itokit-desc') || $("meta[name=description]").attr("content")
    };
    if(dataForWeixin.img.indexOf('http') == '-1'){
        dataForWeixin.img = (domain_name +  dataForWeixin.img);
    } 
}

//var wx_sdk = "/Public/static/open/jweixin.js";
function cancel_callback(res) {
    
}
function success_callback(res, shareType) {
    shareType = shareType || 0;
    data_post = 'url=' + window.location.href + '&share_type=' + shareType;
    $(document).trigger('wx_sendmessage_confirm');
    $.ajax({
        url: '',
        type: 'post',
        dataType: 'json',
        data: data_post,
        success: function (result) {
            if (result.ret == 0 && result.data > 0) {
                
            }
        }
    });
}
var _config = {
    debug: false,
    appId: typeof(app_id) != "undefined" ? app_id : "",
    timestamp: typeof(wx_timestamp) != "undefined" ? wx_timestamp : "",
    nonceStr: typeof(nonceStr) != "undefined" ? nonceStr : "",
    signature: typeof(signature) != "undefined" ? signature : "",
    jsApiList: ["checkJsApi","onMenuShareTimeline","onMenuShareAppMessage","onMenuShareQQ","onMenuShareWeibo","hideMenuItems","showMenuItems","hideAllNonBaseMenuItem","showAllNonBaseMenuItem","translateVoice","startRecord","stopRecord","onRecordEnd","playVoice","pauseVoice","stopVoice","uploadVoice","downloadVoice","chooseImage","previewImage","uploadImage","downloadImage","getNetworkType","openLocation","getLocation","hideOptionMenu","showOptionMenu","closeWindow","scanQRCode","chooseWXPay","openProductSpecificView","addCard","chooseCard","openCard"]
};
$(function () {
    dataForWeixin.url = getUrlParameterAdv(dataForWeixin.url);
    var ua = navigator.userAgent.toLowerCase();
    define = null;
    require = null;
    //$.getScript(wx_sdk, function () {
        wx.config(_config);
        wx.ready(function () {
            //wx.scanQRCode();
            document.querySelector('.act-scan').onclick = function () {
                wx.scanQRCode({
                    needResult: 1,
                    desc: '扫描二维码',
                    success: function (res) {
                        var result = res.resultStr;
                        tipAlert("result:"+result)
                    }
                });
             };
            
            wx.onMenuShareAppMessage({
                "imgUrl": dataForWeixin.img || "",
                "link": dataForWeixin.url || "",
                "desc": dataForWeixin.desc || dataForWeixin.title || document.title || "",
                "title": dataForWeixin.title || "",
                trigger: function (res) {
                },
                success: function (res) {
                    if (success_ext)
                        success_ext(res);
                    else
                        success_callback(res);
                }, cancel: function (res) {
                }, fail: function (res) {
                    cancel_callback(res);
                }
            });
            wx.onMenuShareTimeline({
                "imgUrl": dataForWeixin.img || "",
                "link": dataForWeixin.url || "",
                "title": dataForWeixin.title || "",
                trigger: function (res) {
                },
                success: function (res) {
                    if (success_ext)
                        success_ext(res, 1);
                    else
                        success_callback(res, 1);
                }, cancel: function (res) {
                    cancel_callback(res);
                }, fail: function (res) {
                    cancel_callback(res);
                }
            });
            wx.onMenuShareQQ({
                "imgUrl": dataForWeixin.img || "",
                "link": dataForWeixin.url || "",
                "desc": dataForWeixin.desc || document.title || "",
                "title": dataForWeixin.title || "",
                trigger: function (res) {
                },
                success: function (res) {
                    success_callback(res, 5);
                }, cancel: function (res) {
                    cancel_callback(res);
                }, fail: function (res) {
                    cancel_callback(res);
                }
            });
            wx.onMenuShareWeibo({
                "imgUrl": dataForWeixin.img || "",
                "link": dataForWeixin.url || "",
                "desc": dataForWeixin.desc || document.title || "",
                "title": dataForWeixin.title || "",
                trigger: function (res) {
                },
                success: function (res) {
                    success_callback(res, 2);
                }, cancel: function (res) {
                    cancel_callback(res);
                }, fail: function (res) {
                    cancel_callback(res);
                }
            });
        })
        wx.error(function (res) {
            tipAlert("result:"+JSON.stringify(res))
        });
    //})
})

function getUrlParameterAdv(lsURL) {
    var _newurl;
    if (lsURL.indexOf('openid') == -1) {
        return lsURL;
    }
    var _reg = new RegExp("\/openid/.[^\/]*", "gm");
    lsURL = lsURL.replace(_reg, '');
    var loU = lsURL.split("?");
    _newurl = loU[0];
    if (loU.length > 1) {
        var loallPm = loU[1].split("&");
        for (var i = 0; i < loallPm.length; i++) {
            _newurl += '?';
            var loPm = loallPm[i].split("=");
            if (loPm[0] != 'openid') {
                if (i > 0) {
                    _newurl += '&';
                }
                _newurl += loPm[0] + '=' + loPm[1];
            }
        }
    }

    return _newurl;
}