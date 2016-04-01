
var order_obj = {
    confirm : function(){
        var subsum = $(".sub-sum");
        var _sum = 0;
        $.each(subsum,function(i,n){
            _sum += parseFloat($(n).attr('sub-sum'));
            $("#TotalMoney").html(_sum);
            $("#PayMoney").html(_sum);
        });
        $(".delete-ticket").on("click",function(){
            var _t = $(this);
            var _id = _t.attr("_id");
            if(confirm('确认删除门票?')){
                var el=$.loading({content:'loading...'})
                $.post('?',{tid:_id,action:'delete_ticket'},function(data){
                    el.hide();
                    tipAlert(data.msg,function(){
                        window.location.reload();
                    });
                },'json');
            }
        });
        $("#sub_order").on("click",function(){
            window.location.href = "/Order/checkout"
        });
    },checkout:function(){
        
    },payment:function(){
        $(".ul_order_type").on("click","li",function(){
            $(".ul_order_type").find("li").removeClass("curr");
            var _t = $(this);
            _ext = _t.attr("_ext");
            _t.addClass("curr");
            $("input[name=pay_type]").val(_t.html());
            $(".ul_order_ext").find("li").hide();
            $("li[ext="+_ext+"]").show();
        });
        $("#sub_to_pay").on("click",function(){
            var _pay_type = $("input[name=pay_type]").val();
            var _ext = $(".ul_order_type").find("li.curr").attr("_ext");
            if(!_pay_type){
                tipAlert('请选择支付方式');
                return false;
            }
            post_obj = {
                do_action:"sub_pay",
                pay_type:_pay_type
            };
            if(_ext === "offline"){
                post_obj.pay_remark = $("textearea").val();
            }else if(_ext === "wechat"){
                if($("li[ext=wechat]").find("img").length){
                    tipAlert('请选择支付方式');
                    return true;
                }
            }
            var el=$.loading({content:'loading...'});
            $.post("?",post_obj,function(data){
                el.hide();
                if(data.msg)
                    tipAlert(data.msg);
                if(typeof(data.result.url)!="undefined"){
                    if(_ext === 'wechat' && !isWeixin()){
                        tipAlert('扫描二维码进行支付');
                        $("li[ext=wechat]").empty().append(qrCode(data.result.url)).show();
                    }else{
                        tipAlert('支付跳转中,请稍候');
                        window.location.href = data.result.url;
                    }
                }
            },"json")
        });
        if(order_status < 1){
            chkOrderstatus(order_id);
        }
    }
}

function chkOrderstatus(order_id){
    $.post('/Order/chkOrderstatus',{order_id:order_id},function(data){
        if(data.result && data.result.order_status > 0){
            window.location.reload();
        }
    },'json')
}

function qrCode(url){
    var qr = qrcode(10, 'M');
    qr.addData(url);
    qr.make();
    var img = qr.createImgTag();
    return img;
}
