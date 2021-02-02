/**
 * 公共js
 * Created by Aous on 2017/8/19.
 */



/**
 * 数据打印到控制台
 * @param data
 */
function log(data){
    console.log(data);
}

/**
 * 移除操作提示信息
 */
function removeMessage(){
    var obj = $(".message-info");
    if(obj.length >0){
        obj.delay(2500).fadeOut(500);
    }
}

/**
 * 获取字符串 出现的字母和数字数量
 * @param string
 */
function getStringNumberCount(string){

    var charCount = 0; //英文字符数量
    for(var i=0;i<string.length;i++) {
        //对每一位字符串进行判断，如果Unicode编码在0-127，计数器+1；否则+2
        if (string.charCodeAt(i) < 128 && string.charCodeAt(i) >= 0) {
            charCount++; //一个英文在Unicode表中站一个字符位
        }
    }
    return charCount;
}

/**
 * 字符串截断输出 超过maxLength长度用...表示
 * @param str 字符串
 * @param maxLength 最大长度
 */
function mySubstring(str,maxLength,isReturn){

    if(maxLength == undefined || maxLength==null ||maxLength==''){
        maxLength = 20;
    }
    if(IsMobile()){
        maxLength -= 10;
    }
    if(str==''){
        return false;
    }

    var newStr  = str;
    if(!isIE()){
        newStr = str.substring(0,maxLength);
        var num = getStringNumberCount(newStr);

        if(num>=2){ //英文和数字 字符占用字节比中文短
            var addition =  Math.floor(num/2);
            newStr = str.substring(0,maxLength+addition);
        }
    }else{
        newStr = str.substring(0,maxLength);
    }

    if(isReturn==undefined){
        document.write(newStr);
    }else{
        return  newStr;
    }
}

/**
 *  判断是否IE浏览器
 * @returns {boolean}
 */
function isIE() {
    var userAgent = window.navigator.userAgent;
    if (userAgent.indexOf("MSIE") >= 1 ){
        return true; //仅10极其以下有效 IE11 无效
    } else  if((userAgent.indexOf("WOW64") >=1) && (userAgent.indexOf("Chrome") <1) ){
        return true;// IE 11
    }
    return false;

}

    /**
 * 根据id对比两个控件值是否一致
 * @param id1
 * @param id2
 */
function compareById(id1,id2){
    return $("#"+id1).val() == $("#"+id2).val();
}

/**
 * 根据id判断控件值是否为空
 * @param id
 * @param boolean false 为空，不为空返回jquey对象
 */
function isEmpty(id){
    var obj = $("#"+id);
    if(obj.val() == ''){
        return false;
    }
    return obj;
}

/**
 * 正则校验 从id的控件中获取值
 * @param reg
 * @param id
 */
function regById(reg,id){
    return reg.test($("#"+id).val());
}

/**
 * 输出 错误提示
 * @param msg
 */
function layerErr(msg){
    layer.msg(msg,{icon:5});
}

/**
 * 输出成功提示
 * @param msg
 */
function layerSuc(msg){
    layer.msg(msg,{icon:6});
}

/**
 * 提示层 仅仅提示不做额外回调处理
 * @param msg
 * @param func 回调函数
 * @param funcParam 回调函数参数
 */
function layerHintAlert(msg,func,funcParam){
    if ((func == undefined) || (typeof func !== "function")) {
        layer.alert(msg, {
            skin: 'layui-layer-molv' //样式类名
            ,closeBtn: 0
        });
        return ;
    }
    layer.alert(msg, {
        skin: 'layui-layer-molv' //样式类名
        ,closeBtn: 0
    },function(index){
        layer.close(index);
        //执行回调函数
        func(funcParam);
    });
}


/**
 *  提示框
 * @param msg 提示信息
 * @param config 配置信息 回调函数等信息
 */
function layerConfirm(msg,config){
    if((config != undefined) && (config.btn != undefined)){
        var btn = config.btn;
    }else{
        var btn = ['确定','取消'];
    }
    layer.confirm(msg, {
        btn: btn,
        skin: 'layui-layer-molv2',
    }, function(index){

        if((config != undefined) && (config.confirm != undefined)){
            config.confirm.callback(config.confirm.Param); //执行确定回调函数
        }
        layer.close(index);
    }, function(){

        if((config != undefined) && (config.cancel != undefined)){
            config.cancel.callback(config.cancel.param); //执行取消回调函数
        }
    });
}

/**
 * 刷新验证码
 * @param obj
 */
function refreshCaptcha(id){
    $("#"+id).click(function(){
        var url = $(this).attr("data-url")+"?uid="+Math.random();
        $(this).attr('src',url);
    });
}

/**
 * 判断对象属性默认值
 * @param obj 对象
 * @param Property 属性
 * @param defaultValue 默认值
 */
function defaultProperty(obj,property,defaultValue){
    return obj.hasOwnProperty(property)?obj[property]:defaultValue;

}


/**
 * 民族下拉框设置
 * @param selectId
 * @param selectedValue
 */
function setNation(selectId,selectedValue){
    //民族下拉框
    var nations = ["汉族", "蒙古族", "回族", "藏族", "维吾尔族", "苗族", "彝族", "壮族", "布依族", "朝鲜族", "满族", "侗族", "瑶族", "白族",
        "土家族", "哈尼族", "哈萨克族", "傣族", "黎族", "傈僳族", "佤族", "畲族", "高山族", "拉祜族", "水族", "东乡族", "纳西族", "景颇族", "柯尔克孜族", "土族",
        "达斡尔族", "仫佬族", "羌族", "布朗族", "撒拉族", "毛南族", "仡佬族", "锡伯族", "阿昌族", "普米族", "塔吉克族", "怒族", "乌孜别克族", "俄罗斯族", "鄂温克族",
        "德昂族", "保安族", "裕固族", "京族", "塔塔尔族", "独龙族", "鄂伦春族", "赫哲族", "门巴族", "珞巴族", "基诺族"];

    var option = "";
    for(var i = 0; i < nations.length; i++) {
        selected = nations[i] == selectedValue?"selected":'';
        option += '<option '+selected +' value="' + nations[i] + '">' + nations[i] + '</option>';
    }
    $(option).appendTo("#"+selectId);

}


/**
 * 判断是否手机端 平板不算 返回false
 * @returns {*}
 * @constructor
 */
function IsMobile() {

    if(navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/BlackBerry/i)){
        return 1;
    }

    return false;
    /*
    var isMobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i) ? true : false;
        },
        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i) ? true : false;
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i) ? true : false;
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i) ? true : false;
        },
        any: function () {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Windows());
        }
    };

    return isMobile.any(); //是移动设备

     */
}