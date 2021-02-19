//颜色选择器
$(function(){

    //手机端特殊处理一些样式
    if(IsMobile()){
        $(".logo-mobile").fadeIn(100);
       // $("#main").css("marginTop","20px");

        $("#footer-quikLink").hide();
        $("#footer-idea").hide();
        $("#footer-contract").css("text-align","center");
        $("#footer-weixin").css("text-align","center");

        $(".index-news").css("margin-top","15px");
        $(".slider-main").css("padding-top","80px");

    }else{
        $(".logo-pc").fadeIn(100);
        //$("#main").css("marginTop","65px");
        //$("#header").css("min-width","1300px");
    }

    //横幅朱家 鼠标小手指
    $(".slider-main .carousel-item").each(function(){
        if($(this).attr('data-url')){
            $(this).css("cursor","pointer");
        }
    });
    //点击图片打开新页面
    $(".carousel-inner .carousel-item").click(function(){
        if($(this).attr('data-url')){
            var tempwindow=window.open('_blank');
            tempwindow.location= $(this).attr('data-url');
        }

    });


    //导航栏 banner设置
    $(".channel-banner").css("margin-top",$("#header").height()+10);
});

