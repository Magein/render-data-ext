### render-data-ext
    
    基于 render-data 渲染数据的扩展版本，适用于 ace-admin 框架使用。

### 交互文件

    基于render-data-static/public/js/admin-common.js
     
    cdn cdn.wxhand.com/ace-admin/common/admin-common.js

### 更新日志

    18.6.15 
        
        1. 更加upload类 <del>基于webuploader插件,样式、js文件 使用 http://cdn.wxhand.com/wei/webuploader/</del>(已与18.6.19优化)
     
    18.6.16 
     
        1. 增加Tools类中的方法注释，以便于更好的理解以及快速创建交互对话框
         
        2. 删除Tools类中的prompt() 方法第一个参数为整数型的时候默认创建几个文本框的功能，该功能有缺失，没有配置，prompt() 无法正常使用,是无法正常使用哦
     
    18.6.19 
     
        1. 优化SelectRender类的setOptions方法，增一个参数表示用于筛选，在页面用会增加一个不限的选项
     
        2. SelectRender类增加setCondition方法，该方法同上用处一样，只是可以自定义文案，或者追加多个选项
         
        3. UploadRender增加setContainer方法，用于设置承载上传类的容器，最后渲染的时候会直接渲染传递的容器，这样使得上传类更灵活
        
    18.7.5
     
        1. RenderStyle类的export() 方法，如果值是一个数组，则默认使用逗号分隔，暂时不考虑多维数组的情况
        
    18.7.6  
     
        1. 修复OperateRender类渲染的a标签 target 属性的值默认使用 _blank 修改为 _self ,使用 _blank 在360浏览器中会打开新的标签  
        
    18.7.18
     
        1. 新增 admin 文件夹，这个文件夹专用于掌上大学项目，封装了一个公共的 CommonAction 以便于统一管理，而不是通过复制粘贴到每个项目中
         
        2. 把公共的 html 静态文件也移到 admin文件夹下，放在 template中，其中public 存放公共静态资源文件，TemplateList重写了加载模板的方法，在原有的加载模板的方式放增加了加载公共模板文件的逻辑 
        
        3. composer.josn中 自动加载增加 admin 文件夹的映射关系
        
    18.7.24 
        
        1. 新增 WebQrcodeAction 类，将链接转化为二维码
        
        2. CommonAction类新增 qrcodeUrl() 方法，传递一个链接生成访问 WebQrcodeAction 的链接
        
        3. 优化引入不存在类，使用 class_exist() 先进行验证
        
    18.8.21
    
        1. CommonAction类新增 loadExportButton() 方法，用来是否加载导出按钮
        
        2. table.twig 增加 导出按钮控制逻辑
        
    18.8.30
     
        1. CommonAction类第545行增加isset判断
        
    18.9.4
     
        1. script.twig 引入的脚本中增加版本参数，开发中可以设置一个随机数来防止缓存
        
    18.9.6
     
        1. script.twig 修复 transUrl() 方法中的参数为undefind的时候，调用 match方法报错