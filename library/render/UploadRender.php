<?php

namespace Magein\renderDataExt\library\render;

use Magein\renderData\library\render\FormRender;

class UploadRender extends FormRender
{
    public function render()
    {
        /**
         * 需要引入http://cdn.wxhand.com/wei/webuploader 下的css js文件
         *
         * upload.js文件的214行被我隐藏，不然图片会出现一条杠
         *
         */

        $input = <<<eof
 <div id="wrapper" style="width: 800px">
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
        return $input;
    }
}