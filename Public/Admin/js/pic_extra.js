var pic_extra = {
	index:function(){
		$(".list-delete").css({"cursor":"pointer"}).on("click",function(){
            if(!confirm("确定删除?")){
                return false;
            }
            var _id = $(this).attr("_id");
            if(_id){
                btsalert.loading();
                $.post("?",{"action":"delete_pic","id":_id},function(data){
                    btsalert.loading(1);
                    btsalert.alert(data.msg,function(){
                        window.location.reload();
                    });
                },"json");
            }
        });
	},extra:function(){
		var _this = this;
        wzsImgUpload.initUpload = function () {
            var _t = this;
            if (_t.config) {
                _t._config();
            }
            fp = $("#fileuploader");
            im = $(".image-modal");
            var uploadObj = fp.uploadFile(_t.config);
            $(".upload-btn").on("click", ".start", function () {
                uploadObj.startUpload();
                return false;
            });
            $(".upload-btn").on("click", ".cancel", function () {
                uploadObj.cancelAll();
                return false;
            });
            im.find(".img_box li").unbind("click");
            im.on("click",".img_box li",function(){
                if($(this).hasClass('active'))
                    $(this).removeClass('active');
                $(this).addClass('active');
            });
            im.find(".img_box li").unbind("click");
            im.on("click",".modal-footer .btn",function(){
                _li = im.find(".img_box li.active");
                if(_li.length){
                    _t.callBack(_li);
                    var options = 'hide';
                    $('.image-modal').modal(options);
                }
            });
            return uploadObj;
        }
        $(".image_select").on("click",function(){
            wzsImgUpload.callBack = function(obj){
               $.each(obj,function(i,n){
                   _path = ($(n).find("img").attr("ref"))
                   var _warp = $("#image-warp");
                   var _clone = $(_warp).find('.clone').clone();
                   //_clone.find("a").attr("href",_path);
                   _clone.find("img").attr("src",_path);
                   _clone.removeClass("clone");
                   _clone.prependTo(_warp);
               })
               _this.buildPicStr();
            }
            
            wzsImgUpload.rtnFileModal();
        });
        $("#image-warp").on("click",".act-remove",function(){
            var _t = $(this)
            _t.parent().parent().remove();
            _this.buildPicStr();
        });

        $("#image-warp").on("click",".btn-move-next",function(){
            var _t = $(this).parent()
            if(_t.next(".multi-section").length > 0){
                _t.insertAfter(_t.next(".multi-section"));
                _this.buildPicStr();
            }
        });

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

        
    },buildPicStr:function(){
    	var _str = "";
    	$.each($("#image-warp .multi-section"),function(i,n){
    		if(!$(n).hasClass("clone")){
    			if(_str){
    				_str += ",";
    			}
    			_str += $(n).find("img").attr("src");
    		}
    	})
    	$("input[name=pic_str]").val(_str);
    }
}