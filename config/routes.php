<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/test', 'App\Controller\IndexController@test');
Router::addRoute(['GET', 'POST', 'HEAD'], '/test1', 'App\Controller\TestController@index');

Router::get('/favicon.ico', function () {
    return '';
});

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\WeChat\OfficialController@serve');

Router::post('/wechat/official/oauth', 'App\Controller\WeChat\OfficialController@oauth');  //授权
Router::post('/wechat/pay', 'App\Controller\PaymentController@pay');  // 创建订单，发起支付
Router::post('/wechat/order', 'App\Controller\PaymentController@findOrderInfo');  // 订单查询

Router::addRoute(['GET', 'POST', 'HEAD'], '/payments/notify', 'App\Controller\PaymentController@notify');
Router::addRoute(['GET', 'POST', 'HEAD'], '/payments/pay', 'App\Controller\PaymentController@pay');
Router::addRoute(['GET', 'POST', 'HEAD'], '/payments/refund', 'App\Controller\PaymentController@refund');
Router::addRoute(['GET', 'POST', 'HEAD'], '/payments/refund_notify', 'App\Controller\PaymentController@refundNotify');
