<?php

namespace Magein\renderDataExt\library\render;

use Magein\renderData\library\constant\FormFieldConstant;

class SelectRender extends \Magein\renderData\library\render\form\SelectRender
{
    protected $type = FormFieldConstant::TYPE_SELECT;

    /**
     * 交互事件
     * @var array
     */
    private $change = [];

    /**
     *
     * @param array $options 下拉选项
     * @param bool $condition 是否作为筛选使用，true 则追加一个 不限的 option，如果使用自定义文案，需使用 setCondition
     * @return $this
     */
    public function setOptions($options, $condition = false)
    {
        $this->options = $options;

        if ($condition) {
            $this->setCondition();
        }

        return $this;
    }

    /**
     * @param array $condition
     * @return array
     */
    public function setCondition($condition = [])
    {
        if (empty($this->options)) {
            return $this->options;
        }

        $condition = $condition ?: ['' => '不限'];

        foreach ($this->options as $key => $item) {
            $condition[$key] = $item;
        }

        $this->options = $condition;

        return $this->options;
    }

    /**
     * @param string $childName 子级的名称 这里的名称是<select name=''>中 name 的属性值
     * @param array $childList 数据 格式为
     * [
     *      '父级的option的value值'=>[
     *              [
     *                  'value'=>'子级的option的value值',
     *                  'title'=>'子级的option的text值'
     *              ]
     *       ]
     * ]
     *
     *
     * example:
     *
     *  父类option:
     *      [
     *          "fashion"=>"时装",
     *          "food"=>"食品"
     *      ]
     *
     * 则$childList参数为:
     *      [
     *          "fashion"=>[
     *              [
     *                  "value"=>"man",
     *                  "title"=>"男装"
     *              ],
     *              [
     *                  "value"=>"woman",
     *                  "title"=>"女装"
     *              ],
     *              [
     *                  "value"=>"child",
     *                  "title"=>"童装"
     *              ]
     *          ],
     *          "food"=>[
     *              [
     *                  "value"=>"fruits",
     *                  "title"=>"水果"
     *              ],
     *              [
     *                  "value"=>"vegetables",
     *                  "title"=>"蔬菜"
     *              ]
     *          ]
     *
     *      ]
     *
     *
     *
     * @return $this
     */
    public function setChange($childName, array $childList)
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