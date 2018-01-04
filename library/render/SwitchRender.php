<?php

namespace Magein\renderDataExt\library\render;

use Magein\renderData\library\constant\FormFieldConstant;
use Magein\renderData\library\FieldRenderAbstract;

/**
 * 开关类
 * Class SwitchRender
 * @package Magein\renderDataExt\library\render
 */
class SwitchRender extends FieldRenderAbstract
{
    protected $type = FormFieldConstant::TYPE_CHECKBOX;

    private $clickEvent = null;

    /**
     * @param callable $callback
     * @return $this
     */
    public function setClickEvent(callable $callback)
    {
        $this->clickEvent = $callback;

        return $this;
    }

    /**
     * 使用ace框架
     * @return string
     */
    protected function render()
    {
        $checked = false;

        if ($this->value) {
            $checked = true;
        }

        if (is_callable($this->clickEvent)) {
            $this->clickEvent = call_user_func($this->clickEvent, $this->data);
        }

        $jsonData = $this->clickEvent;

        if ($jsonData && is_array($jsonData)) {
            $jsonData = json_encode($jsonData, JSON_UNESCAPED_UNICODE);
        }

        $input = '<label>'
            . '<input name="' . $this->name . '[]" class="ace ace-switch ace-switch-4" type="checkbox" ' . ($checked ? ' checked' : '') . ($jsonData ? ' data-json=' . $jsonData . '' : '') . '>'
            . '<span class="lbl switch-button"></span>'
            . '</label>';

        return $input;
    }
}