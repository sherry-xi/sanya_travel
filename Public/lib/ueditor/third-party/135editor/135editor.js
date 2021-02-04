UE.registerUI('135editor', function (editor, uiName) {
    var btn = new UE.ui.Button({
        name   : '135editor',
        title  : '135编辑器',
        onclick: function () {
            var dialog = new UE.ui.Dialog({
                iframeUrl: '/Public/lib/ueditor/third-party/135editor/135EditorDialogPage.html?cache='+Math.random(),
                editor   : editor,
                name     : '135editor',
                title    : "135editor助手",
                cssRules : "width: " + (window.innerWidth - 60) + "px;" + "height: " + (window.innerHeight - 60) + "px;",
            });
            dialog.render();
            dialog.open();
        }
    });

    return btn;
});
/*
UE.registerUI('135editor',function(editor,uiName){
    var dialog = new UE.ui.Dialog({
        iframeUrl: editor.options.UEDITOR_HOME_URL+'dialogs/135editor/135EditorDialogPage.html', // 135EditorDialogPage
        cssRules:"width:"+ parseInt(document.body.clientWidth*0.9) +"px;height:"+(window.innerHeight -50)+"px;",
        editor:editor,
        name:uiName,
        title:"135编辑器"
    });
    dialog.fullscreen = false;
    dialog.draggable = false;
    var btn = new UE.ui.Button({
        name:'btn-dialog-' + uiName,
        className:'edui-for-135editor',
        title:'135编辑器',
        onclick:function () {
            dialog.render();
            dialog.open();
        }
    });
    return btn;
},undefined);
// 修改最后的undefine
*/
