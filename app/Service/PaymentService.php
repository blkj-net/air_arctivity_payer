<?php


namespace App\Service;


use App\Service\Abstracts\PayAbstract;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;

class PaymentService
{
    /**
     * @Inject
     * @var ConfigInterface
     */
    var $config;

    /**
     * @param array $orderInfo
     * @param int $platform
     * @param int $channel
     * @param string $client
     * @param string|null $extraData
     * @return mixed
     * @throws \Exception
     */
    public function pay(array $orderInfo,int $platform,int $channel,string $client,?string $extraData=null)
    {
        $paymentConfig = $this->config->get('payment');
        if(!isset($paymentConfig[$platform])){
            throw new \Exception("不支持的支付平台");
        }
        if(!isset($paymentConfig[$platform][$channel])){
            throw new \Exception("不支持的支付渠道");
        }
        $payPage = $paymentConfig[$platform][$channel];
        $payPageObject = new $payPage;
        if(!($payPageObject instanceof PayAbstract)){
            throw new \Exception($payPage."支付包必须基础于PayAbstract");
        }
        if(!method_exists($payPage,$client)){
            throw new \Exception('不支持的支付客户端类型'.$client);
        }

        return $payPageObject->pay($orderInfo['money'],$client,$orderInfo['pay_order_sn'],$orderInfo['desc'],$extraData);
    }

    /**
     * @param int $platform
     * @param int $channel
     * @param $requestData
     * @return mixed
     * @throws \Exception
     */
    public function payNotify(int $platform,int $channel, $requestData){
        $paymentConfig = $this->config->get('payment');
        if(!isset($paymentConfig[$platform])){
            throw new \Exception("不支持的支付平台");
        }
        if(!isset($paymentConfig[$platform][$channel])){
            throw new \Exception("不支持的支付渠道");
        }
        $payPage = $paymentConfig[$platform][$channel];
        $payPageObject = new $payPage;
        if(!($payPageObject instanceof PayAbstract)){
            throw new \Exception("支付包必须基础于PayInterface");
        }
        return $payPageObject->payNotify($requestData);
    }

    public function findOrderInfo(int $platform,int $channel, $param){
        $paymentConfig = $this->config->get('payment');
        if(!isset($paymentConfig[$platform])){
            throw new \Exception("不支持的支付平台");
        }
        if(!isset($paymentConfig[$platform][$channel])){
            throw new \Exception("不支持的支付渠道");
        }
        $payPage = $paymentConfig[$platform][$channel];
        $payPageObject = new $payPage;
        if(!($payPageObject instanceof PayAbstract)){
            throw new \Exception("支付包必须基础于PayInterface");
        }
        return $payPageObject->findOrderInfo($param);
    }
}