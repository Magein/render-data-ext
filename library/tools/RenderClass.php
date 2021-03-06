<?php

namespace Magein\renderDataExt\library\tools;

use Magein\renderDataExt\library\render\DateRender;
use Magein\renderDataExt\library\render\EditRender;
use Magein\renderDataExt\library\render\OperateRender;
use Magein\renderDataExt\library\render\SelectRender;
use Magein\renderDataExt\library\render\SwitchRender;
use Magein\renderDataExt\library\render\UploadRender;
use Magein\renderDataExt\library\RenderFactory;

/**
 * Trait Tips
 * @package Magein\renderDataExt\library\traits
 */
class RenderClass extends \Magein\renderData\library\tools\RenderClass
{
    public function upload()
    {
        $renderClass = new UploadRender();
        RenderFactory::setFieldRenderClass($renderClass);

        return $renderClass;
    }

    public function select()
    {
        $renderClass = new SelectRender();
        RenderFactory::setFieldRenderClass($renderClass);

        return $renderClass;
    }

    /**
     * @return DateRender
     */
    public function date()
    {
        $renderClass = new DateRender();
        RenderFactory::setFieldRenderClass($renderClass);

        return $renderClass;
    }

    /**
     * @return OperateRender
     */
    public function operate()
    {
        $renderClass = new OperateRender();
        RenderFactory::setFieldRenderClass($renderClass);

        return $renderClass;
    }

    /**
     * @return EditRender
     */
    public function edit()
    {
        $renderClass = new EditRender();
        RenderFactory::setFieldRenderClass($renderClass);

        return $renderClass;
    }

    /**
     * 兼容php5.6
     * @return SwitchRender
     */
    public function switches()
    {
        $renderClass = new SwitchRender();
        RenderFactory::setFieldRenderClass($renderClass);

        return $renderClass;
    }
}