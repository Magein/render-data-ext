<?php

namespace Magein\renderDataExt\library\tools;

class Tools
{
    /**
     * @param $url
     * @param $param
     * @return string
     */
    public static function redirect($url, array $param = [])
    {
        $url = '?action_name=' . $url;

        if ($param) {

            foreach ($param as $key => $item) {
                $url .= '&' . $key . '=' . $item;
            }

        }

        return $url;
    }

    /**
     * @param string $operate 操作的名称
     * @param string $url 异步请求链接
     * @param array $data 请求参数 异步请求携带的参数
     * @param array|bool $script 绑定脚本  true表示需要弹出一个确认框， 数组表示弹出对话框，数组形式为['reason'=>'理由|请输入理由|没有理由']表示 对话框中的输入框name值为reason，描述为理由
     * @return array
     */
    public static function fastOperate($operate = '', $url = '', array $data = [], $script = true)
    {
        $url = '?action_name=' . $url;

        if ($data) {

            $data = self::ajax($url, $data);

            if ($script) {

                if (is_bool($script) && $script) {
                    $data = self::confirm('请再次确认是否继续？', $data);
                } elseif (is_array($script)) {
                    $input = [];
                    foreach ($script as $key => $item) {
                        $item = explode('|', $item);
                        $title = isset($item[0]) ? $item[0] : '';
                        $message = isset($item[1]) ? $item[1] : '';
                        $value = isset($item[2]) ? $item[2] : '';
                        $input[] = self::input($key, $title, $message, $value);
                    }
                    $data = self::prompt($input, $data);
                }
            }
        }

        $param = self::operate($operate, '', $data);

        return $param;
    }

    /**
     * @param string $operate 操作名称
     * @param string $url 跳转链接  如果连接中出现Action则自动翻译成运营后台的链接
     * @param array $data 渲染到html标签中的json数据，渲染结果为 data-json=xxx
     * @param array $other 其他属性，数据形式  键值为html中的属性和值  key=value
     * @return array
     */
    public static function operate($operate, $url = '', $data = [], $other = [])
    {
        if (preg_match('/Action$/', $url)) {
            $url = self::redirect($url, $data);
            $data = [];
        }

        $param['operate'] = $operate ? $operate : '操作';
        $param['url'] = $url ? $url : 'javascript:;';

        if ($data) {
            $param['data'] = $data;
        }

        if ($other) {
            $param['other'] = $other;
        }

        return $param;
    }

    /**
     * @param string $url
     * @param array $data
     * @param string $redirect
     * @return array
     */
    public static function ajax($url, $data, $redirect = '')
    {
        if (preg_match('/Action$/', $url)) {
            $url = self::redirect($url);
        }

        $param['url'] = $url;

        if ($data) {
            $param['data'] = $data;
        }

        if ($redirect) {
            $param['redirect'] = $redirect;
        }

        return $param;
    }


    /**
     * @param string $message
     * @param array $ajax
     * @param array $config
     * @return array
     */
    public static function confirm($message, $ajax = [], $config = [])
    {
        return [
            'confirm' => [
                'message' => $message,
                'config' => $config,
            ],
            'ajax' => $ajax
        ];
    }

    /**
     * @param array|int $param
     * @param array $ajax
     * @param array $config
     * @return array
     */
    public static function prompt($param, $ajax = [], $config = [])
    {
        $input = [];
        if (is_int($param) && $param > 0) {
            for ($i = 1; $i <= $param; $i++) {
                $input[] = self::input('param');
            }
        } else {
            $input = $param;
        }

        return [
            'prompt' => [
                'input' => $input,
                'config' => $config,
            ],
            'ajax' => $ajax
        ];
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $message
     * @param string $value
     * @return array
     */
    public static function input($name, $title = '', $message = '', $value = '')
    {
        return [
            'name' => $name,
            'title' => $title,
            'message' => $message,
            'value' => $value
        ];
    }
}