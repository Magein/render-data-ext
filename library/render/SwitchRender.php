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
     * 这是开关的事件，这里固定成一个ajax请求，需要搭配
     * http://cdn.wxhand.com/ace-admin/common/admin-common.js
     *
     * 最总将参数已字符串的形式渲染到html标签的data-json属性中
     *
     * format:
     * $callback=function($param){
     *
     *      $data=[
     *          'id'=>$param['id']
     *          'status'=>1
     *      ];
     *
     *      // 可以使用 Tools工具 构建任意的交互时间
     *
     *      // return Tools::ajax($data);
     *
     *      // return Tools::operate();
     * }
     *
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