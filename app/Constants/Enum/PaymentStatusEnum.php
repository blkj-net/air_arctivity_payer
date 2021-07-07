<?php


namespace App\Constants\Enum;


class PaymentStatusEnum
{
    // 支付平台
    const Platform = [
        'weixin' => 0
    ];

    // 支付渠道
    const Channel = [
        'wechat' => 0
    ];

    const Client = [
        0 => "officialAccount",
    ];
}