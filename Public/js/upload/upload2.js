
var uploadHandle2 = {

    //默认配置
    config:{
        formId:'uploadForm2',
        fileId:'uploadFile2',
        iframeId:'uploadIframe2',

        fileExt:{ //文件类型校验信息
            image:{ //图片类型
                rule:/(jpg|png|bmp|jpeg|gif)$/i,
                message:"上传失败,格式错误. 仅支持jpg`png、bmp、jpeg、gif图片格式"
            },
            document:{ //文档类型
                rule:'',
                message:'',
            },
            all:{ //所有文件类型
                rule:'',
                message:''
            }
        }
    },

    //上传方法
    upload:function(config){

        this.setConfig(config); //1.合并配置信息
        this.setForm();
        this.setEvent();

    },

    //合并配置信息
    setConfig:function(config){
        this.config =  $.extend(this.config, config);
    },

    //设置隐藏表单和iframe
    setForm:function (){

        var form = '<form action="'+this.config.url+'" method="post" enctype="multipart/form-data" target="'+this.config.iframeId+'" id="'+this.config.formId+'">';
        form += '<input type="file" value="" name="fileName" id="'+this.config.fileId+'"/>';
        form += '<input type="text" value="uploadHandle2" name="objName"/>';
        form += '</form>';
        var iframe = '<iframe  name="'+this.config.iframeId+'"></iframe>';
        var css = {display:'none'};
        $(form).css(css).appendTo("body");
        $(iframe).css(css).appendTo("body");
    },

    //设置上传事件
    setEvent:function(){
        //上传按钮事件
        $("#"+this.config.id).click(function(){
            $("#"+uploadHandle2.config.fileId).attr('name',uploadHandle2.config.name).click();
        });

        //监控文件控件变化
        $("body").on('change','#'+this.config.fileId,function(){

            uploadHandle2.config.file = $(this).val();
            if(!uploadHandle2.setVerify()){
                $(this).val('');
                return false;
            }
            layerLoadIndex = layer.load();
            $("#"+uploadHandle2.config.formId).submit();
            $('#'+uploadHandle2.config.fileId).val('').removeAttr('name');
        });


    },

    //文件类型校验
    setVerify:function verifyFileType(){
        var regulation = this.config.fileExt[this.config.fileType];
        if(!regulation.rule){
            return true;
        }
        if(!regulation.rule.test(this.config.file)){
            layerConfirm(regulation.message);
            return false;
        }
        return true;
    },

    //上传成功后 回调函数
    finish:function(data){
        layer.close(layerLoadIndex);
        data = eval('(' + data + ')');
        if(data == null && data=='' && data==undefined){
            layerErr("上传系统异常请联系网络中心");
            return false;
        }
        if(data.errcode !=0){
            layerErr("上传系统异常请联系网络中心");
            log("Error:"+data.msg);
            return false;
        }
        this.config.callback(data);
    }

};

