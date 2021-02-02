/*
* table 列表数操作
* */


/**
 * 配置基础模板
 * @private
 */
function _configTemplate(){
    var config = {
        render:{  //表格数据初时候配置
            url: "请求数据地址",
            cols:[
                {field: 'id', title: 'ID',width:100},
                {field: 'username', title: '账号 | 姓名'},
                {title: '操作', minWidth: 150, toolbar: '#currentTableBar', align: "center"}
            ],
            page:true,
            limit:10
        },
        toolbar:{  //工具栏监听配置
            addEvent:{ //添加数据事件
                title:'标题',
                content:"打开窗口内容url或者html快",
                urlParam:{id:"data.id",level:"data.level",sort:100}, //当content 使用url时候 自动将urlParam 的数据拼接到url
                param:"传入参数", //回调函数使用
                callbackFun:function(obj,param){}  //回调函数 param 是传入参数 传回给回调函数
            }
        }
    };
}


/**
 * 载入表格数据
 */


function layerTableHandle(config){
    if(!config.hasOwnProperty('module')){
        config.module = ['table']; //默认模块
    }
    layui.use(config.module, function () {

        var table = layui.table;
        /*
         * 载入表格数据
         */
        if(config.hasOwnProperty('render')){
            if(config.hasOwnProperty('table')){
                loadTableData(config.render,layui[config.table]);//指定table模块 table or treetable
            }else{
                loadTableData(config.render,table);
            }

        }

        /**
         * toolbar监听事件 顶部工具栏
         */
        if(config.hasOwnProperty('toolbar')){

            table.on('toolbar(currentTableFilter)', function (obj) {
                eventBind(config,obj,'toolbar');
            });
        }

        /**
         * 数据行操作监听
         */
        if(config.hasOwnProperty('recordToolbar')) {
            table.on('tool(currentTableFilter)', function (obj) {
                eventBind(config,obj,'recordToolbar');
            });
        }
    });
}

/**
 *  事件总绑定
 * @param config 配置信息
 * @param obj 数信息
 * @param toolbarType 工具类型 toolbar=顶部工具栏  recordToolbar数据行工具栏
 */
function eventBind(config,obj,toolbarType){

    var conf = config[toolbarType][obj.event];
    if(conf === undefined){
        return false;
    }
    switch (conf.action) {
        case 'childWindow':
            childWindowEvent(conf,obj); //打开一个子窗口并载入数据事件
            break;

        case 'confirm':
            confirmEvent(conf,obj); // 提示框事件
            break;

        case 'hyperlink':
            hyperlinkEvent(conf,obj); //  超链接事件 打开新网页
            break;
        case 'noEvent':
            noEvent(conf,obj); //无任何操作，返回执行回调函数
    }
}



/**
 * 超链接事件
 * @param config
 */
function hyperlinkEvent(config,obj){
    /**
     * _blank 打开新网页标签页
     * _parent  本浏览器标签页父级窗口打开
     * _top 本浏览器标签页顶级窗口打开
     * _self 默认
     */

    if(config.urlParam.target == '_blank'){
        var tempwindow=window.open('_blank');
        tempwindow.location=config.content+getUrlParam(config.urlParam,obj.data);

    }else{
        location.href = config.content+getUrlParam(config.urlParam,obj.data); //_self 默认
    }
}

/**
 * 提示框事件
 * @param config
 * @param obj
 */
function confirmEvent(config,obj){
    layer.confirm(config.message, {
        btn: ['确定','取消'],
        skin: 'layui-layer-molv2'
    }, function(index){
        if(config.hasOwnProperty('callback')){
            config.callback(obj,defaultProperty(config,'param',"")); //执行确定回调函数
        }
        layer.close(index);
    });
}



/**
 * 提示框事件
 * @param config
 * @param obj
 */
function noEvent(config,obj){
    if(config.hasOwnProperty('callback')){
        config.callback(obj,defaultProperty(config,'param',"")); //执行确定回调函数
    }
}

/**
 * 打开子窗口 事件
 * @param config
 * @param obj
 */
function childWindowEvent(config,obj) {

    var index = layer.open({
        title: config.title,
        type: 2,
        shade: 0.2,
        maxmin: true,
        shadeClose: true,
        area: ['100%', '100%'],
        content: config.content+getUrlParam(config.urlParam,obj.data)
    });
    $(window).on("resize", function () {
        layer.full(index);
    });
    if(config.hasOwnProperty('callback')){
        config.callback(obj,defaultProperty(config,'param',''));
    }

}

/**
 * 载入表格数据
 * @param config
 * @param table
 */
function loadTableData(config,table){
    table.render({
        elem: defaultProperty(config,"elem","#currentTableId"),
        url: config.url,
        toolbar: defaultProperty(config,"toolbar","#toolbarDemo"),
        defaultToolbar: defaultProperty(config,"defaultToolbar",[]),
        cols: [config.cols],
        page: defaultProperty(config,'page',false),
        skin: defaultProperty(config,'skin',''),
        limit:defaultProperty(config,'limit',10),

        //树形设置
        treeColIndex: defaultProperty(config,'treeColIndex',1),
        treeSpid: defaultProperty(config,'treeSpid',0),
        treeIdName: defaultProperty(config,'treeIdName','id'),
        treePidName: defaultProperty(config,'treePidName','parent_id'),
    });
}

/**
 * 获取配置的url参数
 * @param obj1  用户配置的对象
 * @param obj2 数据行的对象
 *   如果obj1 的属性为空而且obj2 有这个属性，url就拼接上这个值，否则用obj1的值，方便调用者随意添加参数 自定义参数+数据行的参数
 */

function getUrlParam(obj1,obj2){

    if((obj1 == undefined) || (obj2 == undefined)){
        return '';
    }
    var urlParam  =  "";
    for(var key in obj1){
        if( (obj1[key] == '') && obj2.hasOwnProperty(key)){
            urlParam += (urlParam?"&":"?")+key+"="+ obj2[key];
        }else{
            urlParam += (urlParam?"&":"?")+key+"="+ obj1[key];
        }
    }

    return urlParam;

}
