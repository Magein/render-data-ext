<?php


namespace Magein\renderDataExt\admin\action;

use AadminCore\Core\RequestParam;
use AadminCore\Core\Response\Image;

class WebQrcodeAction extends CommonAction
{
    /**
     * @return string
     */
    public static function getIntro()
    {
        return '链接转二维码';
    }

    /**
     * @param RequestParam $param
     * @return Image
     */
    public function doAction(RequestParam $param)
    {
        $url = $param->getDataByName('url', 'http://www.zsdx.com');

        $url = urldecode($url);

        $qrCode = null;
        $qrCodeClass = 'Endroid\QrCode\QrCode';
        if (class_exists($qrCodeClass)) {
            $qrCode = new $qrCodeClass($url);
        }

        $instance = new Image();

        if ($qrCode && method_exists($qrCode, 'writeString')) {
            $instance->setImageContent(call_user_func([$qrCode, 'writeString']));
        }

        return $instance;
    }
}