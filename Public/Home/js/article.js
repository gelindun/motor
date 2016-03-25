var article = {
    index:function(){
        if($(".ui-list-article").length){
            $(".ui-list-article").infinitescroll({
                navSelector:'.pagination',
                nextSelector:'.pagination a.next',
                itemSelector:'.index-article-li'
            });
        }
    }
}