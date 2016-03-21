/* 
*   author yeping
*   依赖bootstrap
*   loading,alert
*/
var btsalert = {};
btsalert.loadingStr = "<div id='BtsLoadingMdl' class='modal'><div class='bts-warp'><img src='/Public/static/common/img/loading.gif?r=9'></div></div>";
btsalert.alertStr = "<div id='BtsAlertMdl' class=' modal'><div class='bts-warp alert alert-info'></div></div>";
btsalert.loading = function(hide){
    var _t = this;
    hide = hide||'';
    if(!$("body").find('#BtsLoadingMdl').length){
        $("body").append(_t.loadingStr);
    }
    if(hide){
        $('#BtsLoadingMdl').modal('hide');
    }else{
        $('#BtsLoadingMdl').modal('show');
    }
}
btsalert.alert = function(msg,callback){
    callback = callback||function(){};
    var _t = this;
    var msg = msg||"...";
    if(!$("body").find('#BtsAlertMdl').length){
        $("body").append(_t.alertStr);
    }
    $('#BtsAlertMdl').find('.alert').html(msg);
    $('#BtsAlertMdl').modal('show');
    $('#BtsAlertMdl').on('hidden.bs.modal', function (e) {
        callback();
    })
    setTimeout(function(){
        $('#BtsAlertMdl').modal('hide');
    },2000);
}
