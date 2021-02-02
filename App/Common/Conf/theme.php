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
    ]
];