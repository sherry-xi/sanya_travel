jQuery(".slider .bd li").first().before( jQuery(".slider .bd li").last() );
jQuery(".slider").hover(function(){ jQuery(this).find(".arrow").stop(true,true).fadeIn(300) },function(){ jQuery(this).find(".arrow").fadeOut(300) });
jQuery(".slider").slide({ titCell:".hd ul", mainCell:".bd ul", effect:"leftLoop",  autoPlay:true, vis:3, autoPage:true,interTime:5500,delayTime:500, trigger:"click"});