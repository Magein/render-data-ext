<?php

namespace Magein\renderDataExt\library;

use Magein\renderDataExt\library\style\RenderStyle;
use Magein\renderDataExt\library\tools\RenderClass;

class RenderFactory extends \Magein\renderData\library\RenderFactory
{
    public function init()
    {
        // 注册渲染样式类
        $this->setRenderStyle(new RenderStyle());

        // 注册渲染类
        $this->setRenderClass(RenderClass::class);
    }

    /**
     * @param $name
     * @param null|string $title
     * @param null $class
     * @return RenderClass| \Magein\renderData\library\tools\RenderClass
     */
    public function append($name, $title = null, $class = null)
    {
        return parent::append($name, $title, $class);
    }
}