<?php


namespace App\Service\Abstracts;


abstract class PayAbstract
{
    //支付订单超时时间
    protected $payTimeExpire;
    //回调地址
    protected $payNotifyUrl = "";
    //配置对象
    protected $config;
    //金额
    protected $money;
    //支付客户端
    protected $client;
    //平台单号
    protected $orderSn;
    //描述
    protected $desc;
    //微信小程序支付时是code值，微信公众号支付时该值是openid   支付宝小程序支付时是buyer_id
    protected $extraData;

    /**
     * @param int $money 支付金额
     * @param string $client 支付客户端类型
     * @param string $orderSn 起飞线订单号
     * @param string $desc 支付商品描述
     * @param string|null $extraData 微信小程序支付时是code值，微信公众号支付时该值是openid  微信app支付时该值为空  支付宝小程序支付时是buyer_id
     * @return mixed
     */
    abstract public function pay(int $money, string $client, string $orderSn, string $desc, string $extraData = null);

    /**
     * @param $requestData
     * @return mixed
     */
    abstract public function payNotify($requestData);

    /**
     * @param string $orderSn
     * @return mixed
     */
    abstract public function findOrderInfo(string $orderSn);
}