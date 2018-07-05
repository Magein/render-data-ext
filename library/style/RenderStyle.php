<?php

namespace Magein\renderDataExt\library\style;

use Magein\renderData\library\FieldRenderAbstract;
use Magein\renderData\library\render\FormRender;
use Magein\renderDataExt\library\RenderFactory;

class RenderStyle extends \Magein\renderData\library\style\RenderStyle
{
    /**
     * @return string
     */
    public function form()
    {
        $form = '';

        $result = RenderFactory::renderData($this->field, $this->data);

        foreach ($result as $name => $item) {

            // 隐藏域隐藏不显示标题
            if (preg_match('/type="hidden"/', $item)) {
                $form .= $item;
            } else {

                $title = isset($this->fieldTitle[$name]) ? $this->fieldTitle[$name] : '';

                $desc = '<label class="col-sm-3 control-label no-padding-right">' . $title . '</label>';

                // 先匹配css
                preg_match('/class="([\S\s]*)"/', $item, $matches);

                if (preg_match('/type="text"/', $item)) {

                    $class = isset($matches[1]) ? $matches[1] : '';

                    $item = preg_replace('/(<input)/', '$1 class="col-xs-10 col-sm-5 ' . $class . '"', $item);
                }

                if (isset($this->field[$name]['class'])) {
                    /**
                     * @var $class FormRender
                     */
                    $class = $this->field[$name]['class'];

                    $description = $class->__get('description');

                    if ($description) {
                        $item = $item . '<span class="help-inline col-xs-12 col-sm-7"><span class="middle">' . $description . '</span></span>';
                    }
                }

                $item = '<div class="col-sm-9">' . $item . '</div>';

                $form .= '<div class="form-group">' . $desc . $item . '</div>';
            }
        }


        return $form;
    }

    /**
     * @return string
     */
    public function field()
    {
        $form = '';

        $result = RenderFactory::renderData($this->field, $this->data);

        foreach ($result as $name => $item) {

            $title = isset($this->fieldTitle[$name]) ? $this->fieldTitle[$name] : '';

            if ($title) {

                $item = '<span>' . $title . '</span>' . $item;

            }

            $form .= '<div class="select-field">' . $item . '</div>';
        }

        return $form;
    }

    /**
     * @return string
     */
    public function export()
    {
        $result[] = $this->fieldTitle;

        foreach ($this->data as $data) {

            $temp = [];

            foreach ($this->field as $name => $item) {
                /**
                 * @var FieldRenderAbstract $class
                 */
                $class = clone $item['class'];

                if (is_object($class)) {

                    if ($class->__get('callback')) {
                        $class = call_user_func($class->__get('callback'), $data);
                    }

                    $value = $class->__get('value');

                    if ($value === null) {
                        $value = isset($data[$name]) ? $data[$name] : null;
                    }

                    $temp[$name] = $value;
                }
            }

            $result[] = $temp;
        }

        $content = '';

        if ($result) {
            $content = array_reduce($result, function ($content, $item) {

                $value = array_reduce($item, function ($result, $item) {

                    if (is_array($item)) {
                        $item = implode(',', $item);
                    }

                    $result[] = '"' . preg_replace('/(["])/', '"$1', $item) . '"';

                    return $result;
                });

                return $content . implode(',', $value) . "\n";
            });
        }

        // 存在编码问题请在下载输出前根据生产环境转化，不要在这里直接转化

        return $content;
    }
}