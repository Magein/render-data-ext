<?php

namespace Magein\renderDataExt\admin\action;

use AadminCore\Core\RequestParam;
use AadminCore\Admin\BaseAction;
use AadminCore\Core\Response\Download;
use app\core\object\Page;
use Magein\renderDataExt\library\RenderFactory;

class CommonAction extends BaseAction
{

    /**
     * 渲染成表格
     */
    const RENDER_STYLE_TABLE = 'table';

    /**
     * 渲染成表单
     */
    const RENDER_STYLE_FORM = 'form';

    /**
     * 渲染成筛选
     */
    const RENDER_STYLE_FIELD = 'field';

    /**
     * 处理成导出的格式
     */
    const RENDER_STYLE_EXPORT = 'export';

    /**
     *  单选框的可选项-不显示
     */
    const OPTIONS_HIDE = 0;

    /**
     *  单选框的可选项-显示
     */
    const OPTIONS_SHOW = 1;

    /**
     * 渲染数据到页面中
     */
    const RECORDS_SHOW = 1;

    /**
     * 下载数据
     */
    const RECORDS_EXPORT = 2;

    /**
     * 是否下载数据
     * @var int
     */
    protected $export;

    /**
     * @var string 要渲染的数据，是拼接好的 html 字符串，通过页面赋值（变量为 view ）直接显示到html中
     */
    protected $view;

    /**
     * @var string 拼接的筛选的字段 html 字符串 效果同上（变量为 select ）
     */
    protected $select;

    /**
     * 页面赋值
     * @var array
     */
    protected $assign = [];

    /**
     * 上传成功后 这里显示的是 上传的路径  上传失败后这里显示的错误信息
     * 数组表示批量上传的结果
     * @var string|array
     */
    protected $uploadRes = '';

    /**
     * @return string
     */
    protected function getRenderStyle()
    {
        if (preg_match('/Edit/', static::class)) {
            return self::RENDER_STYLE_FORM;
        }

        if (isset($this->assign['result']) && $this->assign['result']) {
            return self::RENDER_STYLE_EXPORT;
        }

        return self::RENDER_STYLE_TABLE;
    }

    /**
     * @param  RequestParam|null $param
     * @param  Page|null $pageLogic
     * @return array
     */
    protected function getData(RequestParam $param, Page $pageLogic = null)
    {
        return [];
    }

    /**
     * @param array $data
     * @return array|string
     */
    protected function view(array $data = [])
    {
        return $data;
    }

    /**
     * @return string
     */
    protected function template()
    {
        if (preg_match('/Edit/', static::class)) {
            return 'form.twig';
        }

        return 'table.twig';
    }

    /**
     * 页面数据赋值 在页面中可直接使用 {{ xxx }} 获取，请注意花括号中的空格
     *
     * 如果是指定action需要使用 {{ request_url(null,xxx)|raw }} 进行转化  raw需要携带
     *
     *  raw 的作用把url的字符实体的实体名称转为成字符 &amp; 转化成 &
     *
     *  使用 raw http://admin-dev-loc.wxhand.com/admin/ApiClientServer/server.html?__aadmin_cate_name=market&__aadmin_action_name=app%5Cadmin%5Caction%5Cplugins%5CWebUploaderAction
     *  不使用raw http://admin-dev-loc.wxhand.com/admin/ApiClientServer/server.html?__aadmin_cate_name=market&amp;__aadmin_action_name=app%5Cadmin%5Caction%5Cplugins%5CWebUploaderAction
     *
     * @return array
     */
    protected function getAssign()
    {
        $config = 'think\Config';
        if (class_exists($config)) {
            // 页面js脚本文件版本,外部插件不使用，主要针对个人写的公共脚本文件,开发阶段使用一个随机值防止缓存
            $staticVersion = call_user_func($config . '::get', 'APP_ENV') == 1 ? rand(100000, 999999) : 'v1.0.1';
        } else {
            $staticVersion = 'v' . rand(100000, 999999);
        }

        return [
            'public_title' => static::getIntro(),
            'js_version' => $staticVersion,
            'css_version' => $staticVersion,
        ];
    }

    /**
     * 加载 Ueditor 富文本编辑器
     * 参数 ueditor_server_action 是服务请求的路径 在当前框架下需设置成action才能正确相应
     * @return array
     */
    protected function loadUeditor()
    {
        $this->assign['ueditor_server_action'] = UeditorAction::getName();

        return $this->assign;
    }

    /**
     * 加载 webUploader 图片上传插件
     *
     * web_uploader_server_action是服务请求的路径 在当前框架下需设置成action才能正确相应
     * web_uploader_accept_name 是后端接受图片的名称  可根据开发需求自行设置
     */
    protected function loadWebUploader()
    {
        $this->assign['web_uploader_server_action'] = WebUploaderAction::getName();
        $this->assign['web_uploader_accept_name'] = 'pics';

        return $this->assign;
    }

    /**
     * 加载 添加 按钮，仅限于用于数据列表展示页面中 新增数据使用
     * @param null|string $actionName 跳转到指定的action 不指定则跳转到默认（文件名称符合规则的）的编辑页面
     * @return array
     */
    protected function loadAddButton($actionName = null)
    {
        if ($actionName === null) {
            $actionName = preg_replace('/List/', 'Edit', static::class);
        }
        $this->assign['add_action_name'] = $actionName;
        return $this->assign;
    }

    /**
     * 加载 搜索 按钮 仅限于用于数据列表展示页面中 搜索数据使用
     * @param null|string $actionName 指定搜索跳转的action 不指定则默认跳转到当前页
     * @return array
     */
    protected function loadSearchButton($actionName = null)
    {
        if ($actionName === null) {
            $actionName = static::getName();
        }

        $this->assign['search_action_name'] = $actionName;

        return $this->assign;
    }

    /**
     * 仅限于编辑页面使用，
     * 这里保存使用单独的一个控制器是用于更细致的权限控制
     * @param null|string $actionName 指定数据保存请求的跳转的action 不指定则使用默认的action  (XXXSaveAction)
     * @return array
     */
    protected function setSaveDataAction($actionName = null)
    {
        if ($actionName === null) {
            $actionName = preg_replace('/Edit/', 'Save', static::class);
        }

        $this->assign['save_data_action_name'] = $actionName;

        return $this->assign;
    }

    /**
     * @param $data
     * @param string $style
     * @param null $fieldGroup
     * @return RenderFactory
     */
    protected function renderFactory($data, $style = 'table', $fieldGroup = null)
    {
        $renderFactory = new RenderFactory($data, $style);

        $fieldTitleClass = 'app\admin\logic\FieldTitleLogic';


        if (class_exists($fieldTitleClass)) {
            if (null === $fieldGroup) {
                $result = call_user_func($fieldTitleClass . '::getAllTitle');
            } else {
                $result = call_user_func($fieldTitleClass . '::getFieldTitle', $fieldGroup);
            }
            $renderFactory->setFieldTitle($result);
        }

        return $renderFactory;
    }

    /**
     * 显示、不显示的选项，只是用于快速设置，避免重复写，并且尽量保证字段含义一样
     * @return array
     */
    protected function statusOptions()
    {
        return [
            self::OPTIONS_HIDE => '不显示',
            self::OPTIONS_SHOW => '显示'
        ];
    }

    /**
     * 数据是展示、还是下载
     * @return array
     */
    protected function exportOptions()
    {
        return [
            self::RECORDS_SHOW => '展示',
            self::RECORDS_EXPORT => '导出'
        ];
    }

    /**
     *
     * 这里暂时不支持批量上传
     *
     * $param->getFileDataByName('file');
     * 获取的数组形式：
     *
     *  array (size=5)
     *       'name' => string 'QQ截图20161116191522.png' (length=26)
     *       'type' => string 'image/png' (length=9)
     *       'size' => int 300656
     *       'tmp_name' => string 'E:/wamp64/tmp\aadmin_upload_file_7c272b7c6352a51abe44de57c836da485053' (length=69)
     *       'error' => int 0
     *
     *
     *  $param->getPost();
     *  获取的数据形式如下：
     *  array (size=6)
     *   'uid' => string '123' (length=3)
     *   'id' => string 'WU_FILE_0' (length=9)
     *   'name' => string 'QQ截图20161116191522.png' (length=26)
     *   'type' => string 'image/png' (length=9)
     *   'lastModifiedDate' => string 'Wed Nov 16 2016 19:15:24 GMT+0800 (中国标准时间)' (length=54)
     *   'size' => string '300656' (length=6)
     *
     *  $param->getFile();
     *  获取的数据形式如下:
     *  array (size=1)
     *   'file' =>
     *   array (size=5)
     *   'name' => string 'QQ截图20161116191522.png' (length=26)
     *   'type' => string 'image/png' (length=9)
     *   'size' => int 300656
     *   'tmp_name' => string 'E:/wamp64/tmp\aadmin_upload_file_7c272b7c6352a51abe44de57c836da485053' (length=69)
     *   'error' => int 0
     *
     * @param array $file 使用$param->getFileDataByName('name')得到的结果
     * @return bool
     */
    public function upload($file)
    {
        if (empty($file)) {
            $this->uploadRes = '文件为空';
            return false;
        }

        $allowExt = ['jpg', 'png', 'gif', 'jpeg'];
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!in_array($fileExt, $allowExt)) {
            $this->uploadRes = '上传文件类型不允许';
            return false;
        }
        $fileBuilder = 'app\common\object\upload\FileBuilder';
        if (class_exists($fileBuilder)) {
            $file = call_user_func_array($fileBuilder . '::buildFileByPhpFile', [$file]);
            if ($file) {
                $uploadLogic = 'app\common\logic\upload\UploadLogic';
                if (class_exists($uploadLogic)) {
                    $uploadLogic = new $uploadLogic();
                    $result = call_user_func_array([$uploadLogic, 'upload'], [$file]);
                    if (false === call_user_func([$uploadLogic, 'checkError'])) {
                        $this->uploadRes = call_user_func([$result, 'getUrl']);
                        return true;
                    } else {
                        $this->uploadRes = 'reason:' . call_user_func([$uploadLogic, 'getErrorInfo']);
                        return false;
                    }
                }
            }
        }

        $this->uploadRes = '缺少上传文件处理类';
        return false;
    }

    /**
     * @param null $data
     * @param null $fileName
     * @return Download
     */
    public function downloadCsv($data = null, $fileName = null)
    {
        if (null === $data) {
            $data = $this->view;
        }

        $data = mb_convert_encoding($data, 'GBK');
        $fileName = $fileName ? $fileName : date('Y-m-d-H-i-s') . rand(1000, 9999);
        $download = new Download();
        $download->setFilename($fileName . '.csv');
        $download->setFileContent($data);
        return $download;
    }

    /**
     * @param string $cateName
     * @param string $url
     * @return string
     */
    public function qrcodeUrl($cateName, $url)
    {
        $data['__aadmin_cate_name'] = $cateName;
        $data['__aadmin_action_name'] = WebQrcodeAction::getName();
        $data['url'] = $url;

        return '?' . http_build_query($data);
    }

    /**
     * @param RequestParam $param
     * @return Download|\AadminCore\Core\Response\View
     */
    public function doAction(RequestParam $param)
    {
        /**
         * 获取赋值到页面的参数
         */
        $data = $this->getAssign();

        /**
         * 如果分页类存在，则进行分页数据处理
         */
        $pageLogic = null;
        $pageClass = 'app\core\object\Page';
        if (class_exists($pageClass)) {
            $pageLogic = new $pageClass();
            $pageLogic->page_size = 10;
            $pageLogic->now_page = $param->getDataByName('page_id', 1);
        }

        /**
         * 获取数据
         */
        $records = $this->getData($param, $pageLogic);

        /**
         * 渲染数据，需要一个数据，tp5 中查询的结果是一个对象，如果是一个对象则处理成数据
         */
        if (is_object($records) && method_exists($records, 'toArray')) {
            $records = $records->toArray();
        }

        $this->view($records);

        if ($this->export == self::RECORDS_EXPORT) {
            return $this->downloadCsv();
        }

        $data['view'] = $this->view;
        $data['select'] = $this->select;

        if ($pageLogic && property_exists($pageLogic, 'pages')) {
            $pages = $pageLogic->pages;
            $pageParam = $param->getGet();
            $pageParamPost = $param->getPost();
            $pageParam = array_merge($pageParam, $pageParamPost);
            foreach ($pageParam as $key => $item) {
                if ($item === '') {
                    unset($pageParam[$key]);
                }
            }
            unset($pageParam['page_id']);
            unset($pageParam['__user_id']);
            $data['page'] = $this->buildViewPage($pages['now_page'], $pages['total_count'], $pages['page_size'], $pageParam);
        }

        return $this->buildViewResponse($this->template(), $data);
    }
}