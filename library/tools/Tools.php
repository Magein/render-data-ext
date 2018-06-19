<?php

namespace Magein\renderDataExt\library\tools;

class Tools
{
    /**
     * 用于内部 action 之间的相互跳转，第一个参数传递 action::getName的值即可
     *
     * 用于 RedirectRender类 setHref 方法的参数使用
     *
     * example:
     *
     *  $redirect=function($param){
     *
     *      // 普通方式
     *      // return "http://www.zsdx.cn?id=".$param['id'];
     *
     *
     *      // 如果是多个 请使用 OperateRender 类调用 setOperate()
     *      return  Tools::redirect(action::getName(),['id'=>$param['id']])
     * }
     *
     *  $renderFactory->append('view')->redirect()->setHref($redirect);
     *
     * @param string $url action::getName()得到的字符串
     * @param array $param 链接中的参数
     * @return string
     */
    public static function redirect($url, array $param = [])
    {
        $url = '?action_name=' . $url;

        if ($param) {

            $url .= '&' . http_build_query($param);

        }

        return $url;
    }

    /**
     *
     * operate方法的升级版，用户快速渲染交互事件(非跳转类)，包含： 确认框，对话框
     *
     *   确认框      渲染一个自定义的对话框应该使用 confirm 方法  可以自定义描述文案以及图标 这里使用默认的文案
     *
     *   对话框    渲染一个对话框，自定义对话框应该使用 prompt方法 ，对话框的中显示几个输入框根据配置来的，行使如下：
     *                  ['key'=>'title|message|value']
     *
     *                  key: 是input的name值 用于后端接受数据
     *                  title : 是input框的描述信息  如 姓名: <input type='text' name='name'> 姓名就是由title指定
     *                  message: 没有输入值的时候的提示信息  这值有值则表示这个输入框是必填的  不必填则为空即可 不能提示字段类型信息，即 字段不是必填的，但是填写了就验证格式  这种需求暂时没有
     *                  value: 输入框的默认值
     *
     * example:
     *
     *  渲染一个确认框
     *      $data['id']=$param['id'];
     *      $operate=Tools::fastOperate('删除',action::getName(),$data,true);
     *
     *  渲染一个对话框
     *      $data['id']=$param['id'];
     *
     *      $input=[
     *          "accept_name"=>'收货人|请填写收货人|小马哥',
     *          "address"=>'收货地址|请填写收货地址|我家',
     *      ];
     *
     *      $operate=Tools::fastOperate('删除',action::getName(),$data,true);
     *
     *
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
     *
     * 此方法用于够着 OperateRender类的 setOperate 方法的 回调参数，可以与带参数跳转，可用于事件交互，
     *
     * 下面的 ajax confirm prompt 均用于构建 setOperate 方法的 回调参数
     *
     * 带参数跳转跟 redirect 功能差不多,区别在于redirect只能单个，operate可多个
     *
     * 关于$data
     *
     *      $data 参数是一个数组，按照既定的格式传递到前段页面完成交互事件，具体参考 ajax confirm  prompt 方法的解释
     *
     * 请注意，完成交互时间需要既定的格式
     *
     * @param string $operate 操作名称，如 编辑 删除 查看详情等
     * @param string $url 跳转链接  如果连接中出现Action则自动翻译成总后台的链接  为空则不进行跳转，进行
     * @param array $data 渲染到html标签中的json数据，渲染结果为 data-json=xxx  事件交互参数
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
     * 构建一个 ajax 请求
     *
     * 交互脚本：cnd.wxhand.com/ace-admin/commmon/admin-common.js
     *
     * js绑定事件的方法名称：initScript
     *
     * $data 渲染到前段页面在文本框中的属性是 data-json="{url:"",data:"","redirect"}"
     *
     * 会捕获a标签中包含 data-json 的dom元素进行事件交互
     *
     * 如果 ajax请求有 {type:"post/get",dataType:"html/json"}等需求，可在依次增加参数
     *
     * 目前默认使用的是 type：post dataType:json
     *
     * @param string $url ajax 请求的链接
     * @param array $data 请求携带的参数
     * @param string $redirect 请求成功之后跳转的链接
     * @return array  这里返回的值是用在 operate() 方法用的 $data 参数
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
     *
     * 构建一个确认框
     *
     * $config 参数值识别两个字段信息 title和icon  title 是对话框的标识  icon 是图标
     *
     * 前段data-json数据识别到有 confirm属性的时候就会识别人对话框 这也是区分的唯一标识，值就是对话框的组成元素，
     *
     * data-json="{confirm:{message:"",config:{title:"",icon:""}},ajax:{url:"",data:"",redirect:""}}";
     *
     * @param string $message 参数是确认框的提示文案
     * @param array $ajax ajax请求参数，为空则仅仅是提示
     * @param array $config 确认框的配置 ['title'=>'','icon'=>'']
     * @return array 返回值用在 operate() 方法中的$data参数
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
     *
     * 构建一个对话框
     *
     * $param是 如果是一个数组，形式如下
     *          [
     *              "title"=>"",        // 输入框的提示信息
     *              "name"=>"",         // 后端接收使用的值
     *              "value"=>"",        // 填充默认值
     *              "message"=>""       // 填写则表示必填，没有填写的时候 显示这个值
     *          ]
     * 这个参数可以是一维数组，也可以是二维数组，二维数组表示构建多个，可以使用 input() 方法构建
     *
     *
     * $config 参数值识别两个字段信息 title和icon  title 是对话框的标识  icon 是图标
     *
     * 渲染到前段的data-json参数如下
     *
     *  data-json="{prompt:{input:{name:"",message:"","value":"","title":""},config:{title:"",icon:""}},ajax:{url:"",data:"",redirect:""}}";
     *
     * @param array $input
     * @param array $ajax 异步请求参数
     * @param array $config
     * @return array
     */
    public static function prompt(array $input, $ajax = [], $config = [])
    {
        return [
            'prompt' => [
                'input' => $input,
                'config' => $config,
            ],
            'ajax' => $ajax
        ];
    }

    /**
     * 构建 prompt() 第一个参数的参数配置
     *
     * 使用这个避免手动输入一个文本框的配置的值，避免输出错误
     *
     * @param string $name 后端接收数组使用的值
     * @param string $title 文本框的描述
     * @param string $message 有值则表示必填，不填则显示此值
     * @param string $value 默认值
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