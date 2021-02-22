//颜色选择器
$(function(){

    //手机端特殊处理一些样式
    if(IsMobile()){
       // $("#main").css("marginTop","20px");

        $("#footer-quikLink").hide();
        $("#footer-idea").hide();
        $("#footer-contract").css("text-align","center");
        $("#footer-weixin").css("text-align","center");

    }else{

    }

});

