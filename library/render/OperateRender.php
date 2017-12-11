<?php

namespace Magein\renderDataExt\library\render;

use Magein\renderData\library\FieldRenderAbstract;

class OperateRender extends FieldRenderAbstract
{

    private $operate = [];

    /**
     * @param string|callable $operate
     * @param string $url
     * @param string $jsonData
     * @return $this
     */
    public function setOperate($operate, $url = '', $jsonData = '')
    {
        if (is_callable($operate)) {

            $this->operate[] = $operate;

        } else {

            $this->operate[] = [
                'operate' => $operate,
                'url' => $url,
                'data' => $jsonData,
            ];

        }

        return $this;
    }

    /**
     * @return array
     */
    private function transOperate()
    {
        $operate = [];

        if ($this->operate) {

            foreach ($this->operate as $item) {

                if (is_callable($item)) {
                    $item = call_user_func($item, $this->data);
                }

                if (isset($item['operate'])) {
                    $item = [$item];
                }

                $operate = array_merge($operate, $item);

            }
        }

        return $operate;
    }

    /**
     * @return string
     */
    protected function render()
    {
        $render = '';

        $operate = $this->transOperate();

        if ($operate) {

            foreach ($operate as $item) {

                $operate = isset($item['operate']) && $item['operate'] ? $item['operate'] : '操作';
                $url = isset($item['url']) && $item['url'] ? $item['url'] : 'javascript:;';
                $jsonData = isset($item['data']) ? $item['data'] : [];
                $other = isset($item['other']) ? $item['other'] : [];

                if ($jsonData && is_array($jsonData)) {
                    $jsonData = json_encode($jsonData, JSON_UNESCAPED_UNICODE);
                }

                $attr = '';
                if ($other) {
                    foreach ($other as $key => $vo) {

                        if (is_array($vo)) {
                            $vo = json_encode($jsonData, JSON_UNESCAPED_UNICODE);
                        }

                        $attr .= $key . '="' . $vo . '" ';
                    }
                }

                $render .= '<a href="' . $url . '" ' . ($attr ? $attr : '') . ' name="operate" target="_blank" ' . ($jsonData ? ' data-json=' . $jsonData . '' : '') . '>' . $operate . '</a>';
            }
        }

        return $render;
    }
}