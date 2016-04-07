var article = {
    index:function(){
        if($(".ui-article-index").length){
            $(".ui-article-index").infinitescroll({
                navSelector:'.pagination',
                nextSelector:'.pagination a.next',
                itemSelector:'.ui-article-item'
            });
        }
    }
}