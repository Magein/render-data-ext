<?php

namespace Magein\renderDataExt\admin\action;

use AadminCore\Core\RequestParam;

class WebUploaderAction extends CommonAction
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

        $file = $param->getFileDataByName('file');

        $result = $this->upload($file);

        if ($result) {
            return $this->buildAjaxResponse(self::CODE_SUCCESS, '上传成功', $this->uploadRes);
        }

        return $this->buildAjaxResponse(self::CODE_ERROR, $this->uploadRes);
    }
}