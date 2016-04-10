var wzsImgUpload = {
    uid: 0,
    init:false,
    formData: {"do_action": "action.file_upload","fileType":1},
    rootPath:"/Uploadfiles/",
    publicPath:"/Public/",
    config: {},
    callBack:function(path){},
    _config: function () {
        var _t = this;
        _conf = {
            url: _t.config.url||"/File/fileUpload",
            fileName: _t.config.fileName||"file",
            showQueueDiv: _t.config.showQueueDiv||"fileQueue",
            acceptFiles: _t.config.acceptFiles||"image/*",
            maxFileCount: _t.config.maxFileCount||20,
            maxFileSize: _t.config.maxFileSize||500 * 1024,
            previewWidth: _t.config.previewWidth||'50px',
            previewHeight: _t.config.previewHeight||'50px',
            statusBarWidth: '100%',
            showPreview: true,
            showDelete: true,
            showDone: true,
            autoSubmit: false,
            uploadStr: "添加文件",
            dragDropStr: "<div><b>拖放文件</b></div>",
            cancelStr: "取消",
            showProgress: true,
            deletelStr: "删除",
            formData: $.extend(true, _t.formData, {uid: _t.uid}),
            customProgressBar: function (obj, s) {
                this.statusbar = $("<dl class='ajax-file-upload-statusbar row'></dl>").width(s.statusBarWidth);
                previewDiv = $("<dd class='col-md-2'></dd>").appendTo(this.statusbar);
                this.preview = $("<img class='ajax-file-upload-preview ' />").width(s.previewWidth).height(s.previewHeight).appendTo(previewDiv).hide();
                this.filename = $("<dd class='ajax-file-upload-filename col-md-3'></dd>").appendTo(this.statusbar);
                this.progressDiv = $("<dd class='ajax-file-upload-progress col-md-3' />").appendTo(this.statusbar).hide();
                this.progressbar = $("<div class='ajax-file-upload-bar'></div>").appendTo(this.progressDiv);
                buttonDiv = $("<dd class='col-md-3' />").appendTo(this.statusbar);
                this.cancel = $("<span>" + s.cancelStr + "</span>").appendTo(buttonDiv).hide();
                this.del = $("<span >" + s.deletelStr + "</span>").appendTo(buttonDiv).hide();
                this.cancel.addClass("ajax-file-upload-red");
                this.del.addClass("ajax-file-upload-red");
                this.abort = $("<span>" + s.abortStr + "</span>").appendTo(buttonDiv).hide();
                this.done = $("<span>" + s.doneStr + "</span>").appendTo(buttonDiv).hide();
                this.download = $("<span>" + s.downloadStr + "</span>").appendTo(buttonDiv).hide();
                this.done.addClass("ajax-file-upload-green");
                return this;
            },onSuccess:function(files,data,xhr,pd){
                data = eval('('+data+')');
                _li = $(".img_box>.img_box_simple").clone().removeClass('img_box_simple').addClass('img_list');
                _li.find("img").attr({"src":_t.rootPath + data.small,"ref":_t.rootPath + data.savename});
                _li.find("i").html(data.size);
                _li.appendTo(".img_box").show();
                pd.statusbar.hide();
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
            im.find(".img_box li").removeClass('active');
            $(this).addClass('active');
        });
        im.on("click",".modal-footer .btn",function(){
            _li = im.find(".img_box li.active");
            if(_li.length){
                _t.callBack(_li.find("img").attr("ref"));
                var options = 'hide';
                $('.image-modal').modal(options);
            }
        });
        return uploadObj;
    }, rtnFileModal: function () {
        var _t = this;
        if(!_t.init){
            _t.init = _t.initUpload();
        }
        var options = 'show';
        $('.image-modal').modal(options);
        _t.rtnList();
    },rtnList:function(p){
        var _t = this;
        var p = p||1;
        $.get("/File/imgList",{p:p},function(data){
            data = typeof(data) != "object"?eval("("+data+")"):data;
            $(".img_box").find(".img_list").remove();
            $.each(data.result.lists,function(i,n){
                _src = n.filesrc.indexOf('scene_sys/') >= 0?_t.publicPath + n.filesrc :_t.rootPath + n.filesrc;
                _thumbsrc = n.filethumbsrc.indexOf('scene_sys/') >= 0?_t.publicPath + n.filethumbsrc :_t.rootPath + n.filethumbsrc;
                _li = $(".img_box>.img_box_simple").clone().removeClass('img_box_simple').addClass('img_list');
                _li.find("img").attr({"src":_thumbsrc,"ref":_src});
                _li.find("i").html(n.sizekb + 'kb');
                _li.appendTo(".img_box").show();
            })
            if(p < 2){
                $('.ajax-page').twbsPagination({
                totalPages: parseInt(data.result.pgNum)||1,
                visiblePages: 7,
                    onPageClick: function (event, page) {
                        $(".img_box").find(".img_list").remove();
                        _t.rtnList(page);
                    }
                });
            }
        },"json");
        
    }
};
