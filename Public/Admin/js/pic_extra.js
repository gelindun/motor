var pic_extra = {
	index:function(){

	},extra:function(){
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
            im.on("click",".img_box li",function(){
                if($(this).hasClass('active'))
                    $(this).removeClass('active');
                $(this).addClass('active');
            });
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
                   var url = "?"
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {'img_path':_path,'do_action':'add_image'},
                        success: function(data){
                            clone.find(".act-remove").attr("_id",data.result._id);
                        },
                        dataType: "json"
                    });
                   _clone.appendTo(_warp);
               })
            }
            wzsImgUpload.rtnFileModal();
        });
        $("#image-warp").on("click",".act-remove",function(){
            var _t = $(this)
            var _id = _t.attr("_id");
            var url = "?"
			$.ajax({
				type: 'POST',
				url: url,
				data: {'do_action':'delete_image','id':_id},
				success: function(data){
                    if(data.status)
                        _t.parent().parent().remove();
				},
				dataType: "json"
			});
        });
    }
}