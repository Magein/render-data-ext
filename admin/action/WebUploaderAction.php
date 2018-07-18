<?php

namespace Magein\renderDataExt\admin\action;

use AadminCore\Admin\BaseAction;
use AadminCore\Core\RequestParam;

class WebUploaderAction extends BaseAction
{
    /**
     * @return string
     */
    public static function getIntro()
    {
        return 'webuploader上传插件';
    }

    /**
     *
     *  html页面中需要引入http://cdn.wxhand.com/wei/webuploader 下的css js文件
     *  upload.js文件的214行被我隐藏，不然图片会出现一条杠
     * 上传插件容器
     * @return string
     */
    public static function getContainer()
    {
        return <<<eof
 <div id="wrapper" style="width: 500px">
        <div id="container">
            <!--头部，相册选择和格式选择-->
            <div id="uploader">
                <div class="queueList">
                    <div id="dndArea" class="placeholder">
                        <div id="filePicker"></div>
                        <p>或将照片拖到这里，单次最多可选300张</p>
                    </div>
                </div>
                <div class="statusBar" style="display:none;">
                    <div class="progress">
                        <span class="text">0%</span>
                        <span class="percentage"></span>
                    </div><div class="info"></div>
                    <div class="btns">
                        <div id="filePicker2"></div><div class="uploadBtn">开始上传</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
eof;
    }

    /**
     * @param RequestParam $param
     * @return array
     */
    public function doAction(RequestParam $param)
    {

        /**
         *
         * 请注意：批量上传也是一个一个请求
         *
         * $param->getFileDataByName('file');
         * 获取的数组形式：
         *
         *  array (size=5)
         *       'name' => string 'QQ截图20161116191522.png' (length=26)
         *       'type' => string 'image/png' (length=9)
         *       'size' => int 300656
         *       'tmp_name' => string 'E:/wamp64/tmp\aadmin_upload_file_7c272b7c6352a51abe44de57c836da485053' (length=69)
         *       'error' => int 0
         *
         *
         *  $param->getPost();
         *  获取的数据形式如下：
         *  array (size=6)
         *   'uid' => string '123' (length=3)
         *   'id' => string 'WU_FILE_0' (length=9)
         *   'name' => string 'QQ截图20161116191522.png' (length=26)
         *   'type' => string 'image/png' (length=9)
         *   'lastModifiedDate' => string 'Wed Nov 16 2016 19:15:24 GMT+0800 (中国标准时间)' (length=54)
         *   'size' => string '300656' (length=6)
         *
         *  $param->getFile();
         *  获取的数据形式如下:
         *  array (size=1)
         *   'file' =>
         *   array (size=5)
         *   'name' => string 'QQ截图20161116191522.png' (length=26)
         *   'type' => string 'image/png' (length=9)
         *   'size' => int 300656
         *   'tmp_name' => string 'E:/wamp64/tmp\aadmin_upload_file_7c272b7c6352a51abe44de57c836da485053' (length=69)
         *   'error' => int 0
         *
         */

        $file = $param->getFileDataByName('file');

        $result = $this->uploadPhoto($file);

        if ($result['code']) {
            return $this->buildAjaxResponse(self::CODE_SUCCESS, '上传成功', $result['data']);
        }

        return $this->buildAjaxResponse(self::CODE_ERROR, $result['msg']);
    }
}