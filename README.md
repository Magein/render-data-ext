### render-data-ext
    基于render-data渲染数据的扩展版本，仅限于ace-admin使用。

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