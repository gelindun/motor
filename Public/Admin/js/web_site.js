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
        //$('.datetime').datetimepicker({format: "YYYY-MM-DD HH:mm"});
        $("#wikiForm").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg);
                        if(parseInt(data.status)){
                            if(typeof(data.result.url)!="undefined")
                                window.location.href = data.result.url;
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
                            if(typeof(data.result.url)!="undefined")
                                window.location.href = data.result.url;
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
        $(".logo_select").on("click",function(){
            wzsImgUpload.callBack = function(path){
                if($(".case_logo").find("img").length<1){
                    $("<img >").appendTo(".case_logo");
                }
                $(".case_logo").find("img").attr("src",path);
                $("input[name=pic_url]").val(path);
            }
            wzsImgUpload.rtnFileModal();
        });
        
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
    },merchant_edit:function(){
        //$('.datetime').datetimepicker({format: "YYYY-MM-DD HH:mm"});
        $("#wikiForm").validate({
            submitHandler:function(form){
                btsalert.loading();
                $(form).ajaxSubmit({
                    success: function (data) {
                        btsalert.loading(1);
                        btsalert.alert(data.msg);
                        if(parseInt(data.status)){
                            if(typeof(data.result.url)!="undefined")
                                window.location.href = data.result.url;
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

    },map_init:function(){
        var myAddress=$('input[name=address]').val();
        var lng = $('input[name=lng]').val()||'116.331398';
        var lat = $('input[name=lat]').val()||'39.897445';
        var destPoint = new BMap.Point(lng,lat);
        var map = new BMap.Map('map');
        map.centerAndZoom(new BMap.Point(destPoint.lng, destPoint.lat), 12);
        map.enableScrollWheelZoom();
        map.addControl(new BMap.NavigationControl());
        var marker = new BMap.Marker(destPoint);
        map.addOverlay(marker);
        
        map.addEventListener('click', function(e){
            destPoint = e.point;
            console.log(e)
            set_primary_input();
            map.clearOverlays();
            map.addOverlay(new BMap.Marker(destPoint)); 
        });
        
        var ac = new BMap.Autocomplete({'input':'address','location':map});
        ac.addEventListener('onhighlight', function(e) {
            ac.setInputValue(e.toitem.value.business);
        });
        
        ac.setInputValue(myAddress);
        ac.addEventListener('onconfirm', function(e) {//鼠标点击下拉列表后的事件
            var _value = e.item.value;
            myAddress = _value.business;
            ac.setInputValue(myAddress);
            
            map.clearOverlays();    //清除地图上所有覆盖物
            local = new BMap.LocalSearch(map, {renderOptions:{map: map}}); //智能搜索
            local.setMarkersSetCallback(markersCallback);
            local.search(myAddress);
        });
        
        var markersCallback = function(posi){
            $('#locBtn').attr('disabled', false);
            if(posi.length==0){
                alert('定位失败，请重新输入详细地址或直接点击地图选择地点！');
                return false;
            }
            for(var i=0; i<posi.length; i++){
                if(i==0){
                    destPoint = posi[0].point;
                    set_primary_input();
                }
                posi[i].marker.addEventListener('click', function(data){
                    destPoint = data.target.getPosition(0);
                });  
            }
        }
        
        var set_primary_input=function(){
            $('input').filter('[name=lng]').val(destPoint.lng).end()
            .filter('[name=lat]').val(destPoint.lat);
        }
        
        $('input[name=address]').keyup(function(event){
            if(event.which == 13){
                $('#locBtn').click();
            }
        });
        
        $('#locBtn').click(function(){
            if(!$('input[name=address]').val()){return false};
            $(this).attr('disabled', true);
            local = new BMap.LocalSearch(map, {renderOptions:{map: map}}); //智能搜索
            local.setMarkersSetCallback(markersCallback);
            local.search($('input[name=address]').val());
            return false;
        });
    }
};

