//颜色选择器
$(function(){

    //手机端特殊处理一些样式
    if(IsMobile()){
       // $("#main").css("marginTop","20px");

        $("#footer-quikLink").hide();
        $("#footer-idea").hide();
        $("#footer-contract").css("text-align","center");
        $("#footer-weixin").css("text-align","center");

        $(".index-news").css("margin-top","15px");
        $(".slider-main").css("padding-top",$("#header").height()-20);

    }else{
        //$("#main").css("marginTop","65px");
        //$("#header").css("min-width","1300px");
        log($("#header").height());
        $(".slider-main").css("padding-top",$("#header").height()-15);
    }

    $(document).scroll(function(e){
        if(IsMobile()){
            return ;
        }

        var top = $(document).scrollTop();
        var number = 700;
        if(top > number){
            $("#searchForm").hide();
            $(".logo-pc").hide(1000);
        }else{
            $("#searchForm").fadeIn(500);
            $(".logo-pc").fadeIn(1000);
        }
    });

    if(!IsMobile()){
        /*
        var top = $(document).scrollTop();
        var number = 400;
        if(top > number){
            $("#header.header-scrolled").css("padding","0px");
            $("#searchForm").hide();
            $(".logo-pc").hide();
        }else{
            $("#header.header-scrolled").css("padding","10px 0px");
            $("#searchForm").show();
            $(".logo-pc").show();
        }*/
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
    if(IsMobile()){
        $(".channel-banner").css("margin-top","40px");
        $("#main").css("margin-top","55px");
    }else{
        $(".channel-banner").css("margin-top",$("#header").height()-15);
    }

});

