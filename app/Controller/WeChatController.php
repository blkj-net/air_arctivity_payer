<?php

declare(strict_types=1);

namespace App\Controller;



class WeChatController extends AbstractController
{
    /**
     * 处理微信的请求消息
     */
    public function serve()
    {

      var_dump(config('wechat.official_account.default'));
        return 'ok';
    }
}
