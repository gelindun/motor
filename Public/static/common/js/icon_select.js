var wzsIconSelect = {
    callBack: function (path) {
    },
    rtnIconModal: function () {
        var _t = this;
        var options = 'show';
        var im = $('.icon-modal');
        im.modal(options);
        im.on("click", ".icon-select i", function () {
            $(".icon-select i").removeClass("active");
            $(this).addClass("active");
        });
        im.on("click", ".modal-footer .btn", function () {
            _li = im.find(".icon-select i.active");
            if (_li.length) {
                _t.callBack(_li.attr("fa"));
                var options = 'hide';
                im.modal(options);
            }
        });
    }
};
