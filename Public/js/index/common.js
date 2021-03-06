//颜色选择器
$(function(){

    //手机端特殊处理一些样式
    if(IsMobile()){


        $("#footer-quikLink").hide();
        $("#footer-idea").hide();
        $("#footer-contract").css("text-align","center");
        $("#footer-weixin").css("text-align","center");


        $(".pc-slider").css("padding-top",$("#header").height()-24);//首页banner设置
        $(".mobile-slider").css("padding-top",$("#header").height()-25);//首页banner设置
        $(".channel-banner").css("margin-top","40px");//导航栏 频道banner设置

        //首页专业列表去掉背景
        $(".major ").css("background","#fff");
        $(".major .icon-box").css("margin-bottom","10px");
    }else{

        if(isIE()){
            $(".pc-slider").css("padding-top",$("#header").height()-54);
        }else{
            $(".pc-slider").css("padding-top",$("#header").height()-24);
        }

        $(".mobile-slider").css("padding-top",$("#header").height()-24);
        $(".channel-banner").css("margin-top",$("#header").height()-24);
        if(window.screen.width > 1440){
            $(".index-banner .carousel-item").css("min-height",window.screen.width/3.415);//大分辨率电脑banner不能定400 否则页面看不完整图片
        }
    }



    $(document).scroll(function(e){
        if(IsMobile()){
            return ;
        }

        var top = $(document).scrollTop();
        var number = 300;
        if(top > number){
            $("#searchForm").hide();
            $(".logo-pc").hide(400);
        }else{
            $("#searchForm").fadeIn(200);
            $(".logo-pc").fadeIn(400);
        }
    });



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




});

