<?php
/**
 * Laravel-cms - cms based on laravel
 *
 * @category  Laravel-cms
 * @package   Laravel
 * @author    Qiangzi <35924784@qq.com>
 * @date      2018/06/06 09:08:00
 * @copyright Copyright 2018 CMS
 * @license   https://opensource.org/licenses/MIT
 * @github    https://github.com/35924784/laravel-cms
 ***
 * @version   Release 1.0
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Models\Wechat;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Transfer;
use App\Handlers\TextMessageHandler;
use App\Handlers\EventMessageHandler;

/**
 * 微信控制器
 *
 * Class WeChatController
 * @package App\Http\Controllers
 */
class WeChatController extends Controller
{

    /**
     * 处理微信的请求消息
     *
     * @param Wechat $safeWechat
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function serve(Wechat $safeWechat)
    {
        $app = $safeWechat->app();

        $app->server->push(function($message) use ($safeWechat){
            return app(EventMessageHandler::class)->handle($safeWechat, $message);
        }, Message::EVENT);

        $app->server->push(function($message) use ($safeWechat){
            return app(TextMessageHandler::class)->handle($safeWechat, $message);
        }, Message::TEXT);

        // 转发收到的消息给客服
        $app->server->push(function($message) {
            return new Transfer();
        });

        return $app->server->serve();
    }
}
