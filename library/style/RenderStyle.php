<?php

namespace Magein\renderDataZsdx\library\style;

use Magein\renderData\library\FieldRenderAbstract;
use Magein\renderDataZsdx\library\RenderFactory;

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

            $title = isset($this->fieldTitle[$name]) ? $this->fieldTitle[$name] : '';

            if ($title) {

                $desc = '<label class="col-sm-3 control-label no-padding-right">' . $title . '</label>';

                // 先匹配css
                preg_match('/class="([\S\s]*)"/', $item, $matches);

                if (preg_match('/type="text"/', $item)) {

                    $class = isset($matches[1]) ? $matches[1] : '';

                    $item = preg_replace('/(<input)/', '$1 class="col-xs-10 col-sm-5 ' . $class . '"', $item);
                }

                $item = '<div class="col-sm-9">' . $item . '</div>';

                $form .= '<div class="form-group">' . $desc . $item . '</div>';

            } else {
                $form .= $item;
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
            foreach ($result as $item) {
                $value = [];
                foreach ($item as $key => $val) {
                    $value[] = '"' . preg_replace('/(["])/', '"$1', $val) . '"';
                }
                $content .= implode(',', $value) . "\n";
            }
        }

        // 存在编码问题请在下载输出前根据生产环境转化，不要在这里直接转化

        return $content;
    }
}