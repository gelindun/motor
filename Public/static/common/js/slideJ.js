(function ($) {
 
    $.fn.slideJ = function (options) {
        var defaults = {//默认属性
            width: $(this).width(),
            height: $(this).height(),
            nav: ".slideNav",
            leftBtn: ".slideLeft",
            rightBtn: ".slideRight",
            speed: 200,
            time: 6000,
            easeFunc: "easeInOutCubic",
            type: "opacity", // opacity || slide
            hoverPause: true
        }
        var options = $.extend(defaults, options);//参数合并

        var sildeElem = $(this), //滑动模块
                slideCl = sildeElem.find("li"),
                slideNavCl = $(options.nav).find("a"),
                total = slideCl.size(), //图片数量
                nowNum = 1,
                active = false;
        if (total <= 1) {
            return;
        }//数量小于等于1不做操作
        //整体CSS设置
        $(this).css({
            //"position":"relative",
            "height": options.height,
            "width": options.width,
            overflow: "hidden"
        });


        //取消A标签虚线框
        var aHideFocus = options.nav + " a" + "," + options.leftBtn + " a," + options.rightBtn + " a," + options.leftBtn + "," + options.rightBtn;
        $(aHideFocus).attr("hideFocus", "hideFocus");


        this.each(function () {//分发轮换效果
            switch (options.type) {
                case "opacity":
                    opacityAnimateJ(options);
                    break;
                case "slide":
                    slideAnimateJ(options);
                    break;
                case "top":
                    topAnimateJ(options);
                    break;
                default:
                    break;
            }
            ;
        });

        //------------淡入淡出----------------------
        function opacityAnimateJ() {
            $(sildeElem).find("ul").css({
                //position:"relative",
                height: options.height,
                width: options.width,
                overflow: "hidden"
            });
            slideCl.css({
                position: "absolute"
            });

            slideNavCl.eq(0).addClass("selected");
            slideCl.css({opacity: 0, "z-index": "0"});
            slideCl.eq(0).css({opacity: 1, "z-index": "1"});
            var interval = setInterval(checkNum, options.time);
            slideNavCl.each(function (index) {
                $(this).click(function () {
                    if (active == true) {
                        return;
                    }
                    nowNum = index;
                    checkNum();
                    //clearInterval(interval);
                    //interval = setInterval(checkNum,options.time);
                });
            });
            $(options.rightBtn).click(function () {
                if (active == true) {
                    return;
                }
                //clearInterval(interval);
                checkNum();
                //interval = setInterval(checkNum,options.time);
            });
            $(options.leftBtn).click(function () {
                if (active == true) {
                    return;
                }
                //clearInterval(interval);
                
                var nx = nowNum - 2;
                var cx = 0;
                if (nx == -1) {
                    nx = total - 1;
                    cx = 0;
                } else if (nx == -2) {
                    nx = total - 2;
                    cx = total - 1;
                } else {
                    cx = nx + 1;
                }
                toggle_scroll(nx);
                nowNum = cx;

                //interval = setInterval(checkNum,options.time);
            });

            function checkNum() {
                if (nowNum < total - 1) {
                    toggle_scroll();
                    nowNum++;
                } else {
                    toggle_scroll();
                    nowNum = 0;
                }
            }
            function toggle_scroll(n) {
                active = true;
                if (n != null) {
                    nowNum = n;
                }
                slideCl.css({"z-index": "0"});

                sildeElem.find("li.selected").css({"z-index": 1});

                slideCl.eq(nowNum).css({"z-index": "2", opacity: 0});
                //slideCl.animate({opacity:0},options.speed);
                slideCl.eq(nowNum).animate({opacity: 1}, {easing: options.easeFunc, duration: options.speed});

                slideNavCl.removeClass("selected");
                slideNavCl.eq(nowNum).addClass("selected");

                slideCl.removeClass("selected");
                slideCl.eq(nowNum).addClass("selected");
                active = false;

            }
            if (options.hoverPause) {
                sildeElem.hover(function () {
                    clearInterval(interval);
                    interval = false;
                }, function () {
                    if (interval == false) {
                        interval = setInterval(checkNum, options.time);
                    }
                });
                slideNavCl.hover(function () {
                    clearInterval(interval);
                    interval = false;
                }, function () {
                    if (interval == false) {
                        interval = setInterval(checkNum, options.time);
                    }
                });
                slideCl.hover(function () {
                    clearInterval(interval);
                    interval = false;
                }, function () {
                    if (interval == false) {
                        interval = setInterval(checkNum, options.time);
                    }
                });
            }

        }
        //------------左右滑动--------------------
        function slideAnimateJ() {

            $(sildeElem).find("ul").css({
                position: "relative",
                height: options.height,
                width: options.width * total,
                overflow: "hidden"
            });
            slideCl.css({
                //position:"absolute"
                width: options.width,
                float: "left"
            });

            slideNavCl.eq(0).addClass("selected");
            //	slideCl.css({opacity:0,"z-index":"0","display":"none"});
            //	slideCl.eq(0).css({opacity:1,"z-index":"1","display":"block"});
            var interval = setInterval(checkNum, options.time);
            slideNavCl.each(function (index) {
                $(this).click(function () {
                    if (active == true) {
                        return;
                    }
                    nowNum = index;
                    checkNum();
                    clearInterval(interval);
                    interval = setInterval(checkNum, options.time);
                });
            });
            $(options.rightBtn).click(function () {
                if (active == true) {
                    return;
                }
                clearInterval(interval);
                checkNum();
                interval = setInterval(checkNum, options.time);
            });
            $(options.leftBtn).click(function () {
                if (active == true) {
                    return;
                }
                clearInterval(interval);

                var nx = nowNum - 2;
                var cx = 0;
                if (nx == -1) {
                    nx = total - 1;
                    cx = 0;
                } else if (nx == -2) {
                    nx = total - 2;
                    cx = total - 1;
                } else {
                    cx = nx + 1;
                }
                toggle_scroll(nx);
                nowNum = cx;

                interval = setInterval(checkNum, options.time);
            });

            function checkNum() {
                if (nowNum < total - 1) {
                    toggle_scroll();
                    nowNum++;
                } else {
                    toggle_scroll();
                    nowNum = 0;
                }
            }
            function toggle_scroll(n) {
                active = true;
                if (n != null) {
                    nowNum = n;
                }
                slideCl.parent().animate({
                    left: -options.width * nowNum
                }, {easing: options.easeFunc, duration: options.speed});
            }
        }
        //------------上下滑动--------------------
        function topAnimateJ() {

            $(sildeElem).find("ul").css({
                position: "relative",
                height: options.height * total,
                width: options.width,
                overflow: "hidden"
            });
            slideCl.css({
                //position:"absolute"
                width: options.width,
                height: options.height,
                display: "block"
            });

            slideNavCl.eq(0).addClass("selected");
            //	slideCl.css({opacity:0,"z-index":"0","display":"none"});
            //	slideCl.eq(0).css({opacity:1,"z-index":"1","display":"block"});
            var interval = setInterval(checkNum, options.time);
            slideNavCl.each(function (index) {
                $(this).click(function () {
                    if (active == true) {
                        return;
                    }
                    nowNum = index;
                    checkNum();
                    clearInterval(interval);
                    interval = setInterval(checkNum, options.time);
                });
            });
            $(options.rightBtn).click(function () {
                if (active == true) {
                    return;
                }
                clearInterval(interval);
                checkNum();
                interval = setInterval(checkNum, options.time);
            });
            $(options.leftBtn).click(function () {
                if (active == true) {
                    return;
                }
                clearInterval(interval);

                var nx = nowNum - 2;
                var cx = 0;
                if (nx == -1) {
                    nx = total - 1;
                    cx = 0;
                } else if (nx == -2) {
                    nx = total - 2;
                    cx = total - 1;
                } else {
                    cx = nx + 1;
                }
                toggle_scroll(nx);
                nowNum = cx;

                interval = setInterval(checkNum, options.time);
            });

            function checkNum() {
                if (nowNum < total - 1) {
                    toggle_scroll();
                    nowNum++;
                } else {
                    toggle_scroll();
                    nowNum = 0;
                }
            }
            function toggle_scroll(n) {
                active = true;
                if (n != null) {
                    nowNum = n;
                }
                slideCl.parent().animate({
                    top: -options.height * nowNum
                }, {easing: options.easeFunc, duration: options.speed});
                active = false;
            }
            if (options.hoverPause) {
                sildeElem.hover(function () {
                    clearInterval(interval);
                    interval = false;
                }, function () {
                    if (interval == false) {
                        interval = setInterval(checkNum, options.time);
                    }
                });
                slideNavCl.hover(function () {
                    clearInterval(interval);
                    interval = false;
                }, function () {
                    if (interval == false) {
                        interval = setInterval(checkNum, options.time);
                    }
                });
                slideCl.hover(function () {
                    clearInterval(interval);
                    interval = false;
                }, function () {
                    if (interval == false) {
                        interval = setInterval(checkNum, options.time);
                    }
                });
            }
            
        }
    }
})(jQuery);
