<?php
/* 主题风格配置 */

return [
    'theme'=>[ //样式相关

        'form' => [ //表单按钮风格
            'submit' => ['text'=>'保 存','style'=>'layui-btn'],
            'reset' => ['text'=>'重 置','style'=>'layui-btn layui-btn-warm'],
            'cancel' => ['text'=>'取 消','style'=>'layui-btn layui-btn-primary'],
            'upload' => ['text'=>'选 择','style'=>'layui-btn layui-btn-sm layui-btn-normal'],
        ],
        'toolbar' => [ //工具栏 操作
            'add' => ['text'=>'添 加','style'=>'layui-btn layui-btn-sm'],
            'back' => ['text'=>'返 回','style'=>'layui-btn layui-btn-sm','icon'=>'<i class="layui-icon"></i>']
        ],
        'databar' => [ //数据栏操作
            'edit' => ['text'=>'编 辑', 'style' => 'layui-btn layui-btn-primary layui-btn-xs','icon' => '<i class="layui-icon">&#xe642;</i>'],
            'delete' => ['text'=>'删 除','style' => 'layui-btn layui-btn-primary layui-btn-xs','icon' => '<i class="layui-icon">&#xe640;</i>'],
            'child' => ['text'=>'子 级','style' => 'layui-btn layui-btn-primary layui-btn-xs','icon' => '<i class="layui-icon">&#xe630;</i>'],
            'show' => ['text'=>'查 看', 'style' => 'layui-btn layui-btn-primary layui-btn-xs','icon' => '<i class="layui-icon">&#xe615;</i>'],
            'add' => ['text'=>'添加', 'style' => 'layui-btn layui-btn-primary layui-btn-xs','icon' => '<i class="layui-icon">&#xe624;</i>'],
        ],

        'more-svg' => '<svg   xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
                                <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
                            </svg>'
    ],

    'image' =>[
        'thumb_latestnews' => '/Public/image/thumb_latestnews.png', //最新文章列表缩略图
        'thumb_channelnews' => '/Public/image/thumb_latestnews.png' //导航文章列表缩略图
    ]
];