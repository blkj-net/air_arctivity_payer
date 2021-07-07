<?php


namespace App\Service;


use App\Service\Abstracts\RefundAbstract;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;

class RefundMoneyService
{
    /**
     * @Inject()
     * @var ConfigInterface
     */
    var $config;

    /**
     * @param array $refundOrderInfo
     * @param int $platform
     * @param int $channel
     * @param string $client
     * @param string $desc
     * @return mixed
     * @throws \Exception
     */
    public function refund(array $refundOrderInfo, int $platform, int $channel,string $client,string $desc="")
    {
        $paymentConfig = $this->config->get('refund');
        if (!isset($paymentConfig[$platform])) {
            throw new \Exception("不支持的退款平台");
        }
        if (!isset($paymentConfig[$platform][$channel])) {
            throw new \Exception("不支持的退款渠道");
        }
        $payPage = $paymentConfig[$platform][$channel];
        $payPageObject = new $payPage;
        if (!($payPageObject instanceof RefundAbstract)) {
            throw new \Exception("退款包必须基础于RefundAbstract");
        }
        return $payPageObject->refund($refundOrderInfo['refund_fee'], $client, $refundOrderInfo['order_sn'],$desc);
    }

    /**
     * @param int $platform
     * @param int $channel
     * @param $requestData
     * @return mixed
     * @throws \Exception
     */
    public function notify(int $platform, int $channel, $requestData)
    {
        $paymentConfig = $this->config->get('refund');
        if (!isset($paymentConfig[$platform])) {
            throw new \Exception("不支持的支付平台");
        }
        if (!isset($paymentConfig[$platform][$channel])) {
            throw new \Exception("不支持的支付渠道");
        }
        $payPage = $paymentConfig[$platform][$channel];
        $payPageObject = new $payPage;
        if (!($payPageObject instanceof RefundAbstract)) {
            throw new \Exception("支付包必须基础于RefundAbstract");
        }
        return $payPageObject->refundNotify($requestData);
    }
}