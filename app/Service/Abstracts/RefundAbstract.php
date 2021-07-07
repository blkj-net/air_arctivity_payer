<?php


namespace App\Service\Abstracts;


abstract class RefundAbstract
{

    //配置对象
    protected $config;

    //退款金额
    protected $money;

    //平台单号
    protected $orderSn;

    //描述
    protected $desc;

    protected $refundNotifyUrl;
    protected $refundDesc;

    abstract public function refund(int $money, string $client, string $orderSn, string $desc);

    abstract public function refundNotify($requestData);


}