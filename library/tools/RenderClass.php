<?php

namespace Magein\renderDataZsdx\library\tools;

use Magein\renderDataZsdx\library\render\DateRender;
use Magein\renderDataZsdx\library\render\EditRender;
use Magein\renderDataZsdx\library\render\OperateRender;
use Magein\renderDataZsdx\library\render\SelectRender;
use Magein\renderDataZsdx\library\RenderFactory;

/**
 * Trait Tips
 * @package Magein\renderDataZsdx\library\traits
 */
class RenderClass extends \Magein\renderData\library\tools\RenderClass
{
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
}