<?php
return array(
	//'配置项'=>'配置值'


    //模版替换
    'TMPL_PARSE_STRING'  =>array(
        '__PUBLIC__' => __ROOT__.'/Public', // 更改默认的/Public 替换规则
        '__JS__'     => __ROOT__.'/Public/js', // 增加新的JS类库路径替换规则
        '__CSS__'    => __ROOT__.'/Public/css',
        '__IMG__'    => __ROOT__.'/Public/image',
        '__LIB__'    => __ROOT__.'/Public/lib',
        '__UPLOAD_IMG__' => __ROOT__.'/WebData/upload/image', // 图片上传文件夹
        '__UPLOAD_FILE__' => __ROOT__.'/WebData/upload/file', // 文件上传文件夹
    ),

    'UPLOAD_IMAGE' => '/WebData/upload/image/', // 图片上传文件夹
    'UPLOAD_FILE' => '/WebData/upload/file/', // 文件上传文件夹


    //拓展配置
    'LOAD_EXT_CONFIG' => 'mysql,language,channel,web,theme',

    'TMPL_ACTION_ERROR'     =>  './Component/dispatch_jump', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  './Component/dispatch_jump', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   =>  './Public/Tpl/think_exception.tpl',// 异常页面的模板文件

    'MESSAGE_ERR' => '操作繁忙,请刷新再试试',

	//颜色配置
    /*
    'css' => array(  
        array('一级导航',       'cssNavi-color',         'cssNavi-bg'),
        array('二级级导航',      'cssNaviSon-color',      'cssNaviSon-bg'),
        array('首页中部',       'cssCenter-color',        'none'),
        array('首页新闻频道标签', 'cssChannelNews-color', 'cssChannelNews-bg'),
        array('首页频道标签',    'cssChannel-color',      'cssChannel-bg'),
        array('首页底部',       'cssFoot-color',          'cssFoot-bg'),
        array('友情链接',       'cssLink-color',          'cssLink-bg')
    ),*/
    //'TMPL_EXCEPTION_FILE'=>'./Public/error/index.html' //异常友好页面 上线记得打开

    'AUTOLOAD_NAMESPACE' => array( //自定义加载类库
        'Lib'     => './Lib',
    ),
    'SESSION_PREFIX' => 'science' //session 前缀，避免同一个服务器目录下多项目造成session错乱 science 理工学院的意思
);