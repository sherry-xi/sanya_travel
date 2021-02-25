if ($('.theme_carousel').length) {
    $(".theme_carousel").each(function (index) {
        var $owlAttr = {},
            $extraAttr = $(this).data("options");
        $.extend($owlAttr, $extraAttr);
        $(this).owlCarousel($owlAttr);
    });
}
$(function () {


    //专业介绍
    $(".major-thumb").hover(function(){
        $(this).find(".title-bg").fadeIn(500);
        $(this).find(".title-name").fadeIn(500);
        $(this).find(".title-name-show").hide();
    },function(){
        $(this).find(".title-bg").hide();
        $(this).find(".title-name").hide();
        $(this).find(".title-name-show").fadeIn(500);

    });

    $(".major-thumb").click(function(){
        location.href = $(this).find("a").attr("href");
    });
    $(".index-travel-title").each(function(){
        $(this).text(mySubstring($(this).attr('data-text'),20,true));
    });
    if(IsMobile()){
        $(".major-thumb img").css("height",'auto');
    }else{
        $(".major-thumb img").css("height",'200px');


        //三个新闻列表高度统一
        var maxHeight = $(".news-list-bg-3").height();
        if(maxHeight < $(".news-list-bg-4").height()){
            maxHeight = $(".news-list-bg-4").height();
        }
        if(maxHeight < $(".news-list-bg-5").height()){
            maxHeight = $(".news-list-bg-5").height();
        }
        $(".news-list-bg-3").height(maxHeight);
        $(".news-list-bg-4").height(maxHeight);
        $(".news-list-bg-4").height(maxHeight);
    }

    $(".major-thumb").css("width",'100%');

});
