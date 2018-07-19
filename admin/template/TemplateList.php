<?php

namespace Magein\renderDataExt\admin\template;

use AadminCore\Core\RequestParam;
use AadminCore\Core\Response\Json;

class TemplateList extends \AadminCore\AdminServer\Actions\TemplateList
{
    /**
     * @var string
     */
    private $templatePath;

    /**
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    private function getFiles($path, $basePath = '')
    {
        if (!is_dir($path)) {
            return [];
        }

        $files = [];

        $handle = dir($path);
        while (true) {
            $filename = $handle->read();
            if (!$filename) {
                break;
            } elseif ($filename == '.' || $filename == '..') {
                continue;
            }

            $fullFilename = $path . '/' . $filename;
            if (is_dir($fullFilename)) {
                $files = array_merge($files, $this->getFiles($fullFilename, $basePath . $filename . '/'));
            } else {
                $files[$basePath . $filename] = $fullFilename;
            }
        }

        return $files;
    }

    protected function getPublicTemplateFiles()
    {

        $filePath = dirname(__FILE__) . DS . 'public';

        $publicTemplateFiles = $this->getFiles($filePath);

        return $publicTemplateFiles;
    }

    private function getTemplateList()
    {

        $templateFiles = $this->getFiles($this->templatePath);
        /**
         * 加载公共模板文件
         */
        $templateFiles = array_merge($templateFiles, $this->getPublicTemplateFiles());

        $templateList = [];
        foreach ($templateFiles as $templateName => $templateFileName) {
            $templateList[$templateName] = [
                'content' => file_get_contents($templateFileName),
            ];
        }

        return $templateList;
    }

    public function doAction(RequestParam $param)
    {
        $response = new Json();

        $response->setData($this->getTemplateList());

        return $response;
    }
}
