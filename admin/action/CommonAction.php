<?php

namespace Magein\renderDataExt\admin\action;

use AadminCore\Admin\BaseAction;
use AadminCore\Core\RequestParam;
use AadminCore\Core\Response\Download;
use Magein\renderDataExt\library\RenderFactory;

use app\core\object\Page;
use think\Config;
use think\Model;

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
     * @param RequestParam $param
     * @param Page|null $pageLogic
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
            return 'public/form.twig';
        }

        return 'public/table.twig';
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
        // 页面js脚本文件版本,外部插件不使用，主要针对个人写的公共脚本文件,开发阶段使用一个随机值防止缓存
        $staticVersion = Config::get('APP_ENV') == 1 ? rand(100000, 999999) : 'v1.0.1';

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
     * 仅限于编辑页面使用
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
                $result = forward_static_call([$fieldTitleClass, 'getAllTitle']);
            } else {
                $result = forward_static_call([$fieldTitleClass, 'getFieldTitle'], $fieldGroup);
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
         * 如果是下载数据，则下载全部数据
         */
        if ($this->export = self::RECORDS_EXPORT) {
            $pageLogic = null;
        } else {
            $pageLogic = new Page();
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
        if (is_object($records)) {
            /**
             * @var Model $records
             */
            $records = $records->toArray();
        }

        $this->view($records);

        if ($this->export = self::RECORDS_EXPORT) {
            return $this->downloadCsv();
        }

        $data['view'] = $this->view;
        $data['select'] = $this->select;

        if ($pageLogic) {
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