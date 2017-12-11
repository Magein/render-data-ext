<?php

namespace Magein\renderDataZsdx\library\render;

use Magein\renderData\library\constant\FormFieldConstant;

class SelectRender extends \Magein\renderData\library\render\form\SelectRender
{
    protected $type = FormFieldConstant::TYPE_SELECT;

    private $change = [];

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $childName 子级的名称
     * @param array $childList 数据 格式为
     * [
     *      '父级的option的value值'=>[
     *              [
     *                  'value'=>'子级的option的value值',
     *                  'title'=>'子级的option的text值'
     *              ]
     *       ]
     * ]
     * @return $this
     */
    public function setChange(string $childName, array $childList)
    {
        $this->change['child_name'] = $childName;
        $this->change['child_list'] = $childList;

        return $this;
    }

    protected function render()
    {
        $value = $this->value;

        $this->value = null;

        $attr = $this->attr();

        $input = '';

        if ($this->options) {

            foreach ($this->options as $key => $option) {

                $selected = false;
                if ($value !== null && $value == $key) {
                    $selected = true;
                }

                $input .= '<option value="' . $key . '" ' . ($selected ? 'selected' : '') . '>' . $option . '</option>';
            }
        }

        $change = '';
        if ($this->change) {
            $change = 'data-change=' . json_encode($this->change);
        }

        $input = '<select ' . $attr . ' ' . ($change) . ' value="' . $value . '">' . $input . '</select>';

        return $input;
    }

}