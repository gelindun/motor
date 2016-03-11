var avatarUpload = {
    uid: 0,
    formData: {"do_action": "action.file_upload","fileType":1},
    rootPath:"/Uploadfiles/",
    publicPath:"/Public/",
    config: {},
    callBack:function(path){},
    _config: function () {
        var _t = this;
        _conf = {
            url: _t.config.url||"/File/avatarUpload",
            fileName: _t.config.fileName||"file",
            showQueueDiv: _t.config.showQueueDiv||"fileQueue",
            acceptFiles: _t.config.acceptFiles||"image/*",
            sequential:true,
            sequentialCount:1,
            maxFileCount: _t.config.maxFileCount||20,
            maxFileSize: _t.config.maxFileSize||1024 * 1024,
            previewWidth: _t.config.previewWidth||'50px',
            previewHeight: _t.config.previewHeight||'50px',
            statusBarWidth: '100%',
            showPreview: true,
            showDelete: false,
            showDone: false,
            autoSubmit: true,
            uploadStr: "修改头像",
            dragDropStr: "",
            cancelStr: "取消",
            showProgress: true,
            deletelStr: "删除",
            formData: $.extend(true, _t.formData, {uid: _t.uid})
            ,onSuccess:function(files,data,xhr,pd){
                data = eval('('+data+')');
                $(".area-header-img").hide();
                $(".area-op").show().find("img").attr({"src":_t.rootPath + data.small,"ref":_t.rootPath + data.savename});
                pd.statusbar.hide();
                $('#corpimg').Jcrop({
                    onChange: _t.showCoords,
                    onSelect: _t.showCoords,
                    aspectRatio:1,
                    bgOpacity: .4,
                    setSelect: [50, 50, 150, 150],
                    aspectRatio: 10 / 10
                });
                $(".savecorpimg").one("click",function(){
                    $.post("/File/crop",{
                        "x1":$('#x1').val(),
                        "y1":$('#y1').val(),
                        "x2":$('#x2').val(),
                        "y2":$('#y2').val(),
                        "w":$('#w').val(),
                        "h":$('#h').val(),
                        "src":$(".show-area").attr("src")
                    },function(data){
                        if(!data.obj){
                            data = eval('('+data+')')
                        }
                        $(".area-header-img").show().find("img").attr({"src":data.obj});
                        $(".area-op").hide();
                    },'json');
                })
            }
        };
        _t.config = _conf;
    },
    initUpload: function () {
        var _t = this;
        if (_t.config) {
            _t._config();
        }
        fp = $("#fileuploader");
        var uploadObj = fp.uploadFile(_t.config);
        return uploadObj;
    },showCoords:function(c){
        $('#x1').val(c.x);
        $('#y1').val(c.y);
        $('#x2').val(c.x2);
        $('#y2').val(c.y2);
        $('#w').val(c.w);
        $('#h').val(c.h);
    }
};