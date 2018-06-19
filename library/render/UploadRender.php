<?php

namespace Magein\renderDataExt\library\render;

use Magein\renderData\library\render\FormRender;

class UploadRender extends FormRender
{
    /**
     * 上传插件的容器
     * @var string
     */
    protected $container = '';

    /**
     * 设置上传插件的容器
     * @param $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    public function render()
    {
        return $this->container;
    }
}