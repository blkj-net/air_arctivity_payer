<?php


namespace App\Service\Pay;


use App\Exception\BusinessException;
use App\Model\Order;
use App\Model\PayNotify;
use App\Service\Abstracts\PayAbstract;
use Carbon\Carbon;
use EasySwoole\Pay\WeChat\Config;
use EasySwoole\Pay\WeChat\RequestBean\OfficialAccount;
use Monolog\Logger;

class Wechat extends PayAbstract
{

    public function __construct()
    {
        $wechatConfig = new Config();
        $wechatConfig->setMchId(\config('wechat.payment.default.mch_id', 'wechat'));
        $wechatConfig->setKey(\config('wechat.payment.default.key', 'wechat'));
        $wechatConfig->setApiClientCert(\config('wechat.payment.default.cert_path', 'wechat'));//客户端证书
        $wechatConfig->setApiClientKey(\config('wechat.payment.default.key_path', 'wechat')); //客户端证书秘钥
        $this->config = $wechatConfig;
    }

    /**
     * 支付
     * User：caogang
     * DateTime：2021/6/29 15:52
     * @param int $money
     * @param string $client
     * @param string $orderSn
     * @param string $desc
     * @param string|null $extraData
     * @return false|mixed
     */
    public function pay(int $money, string $client, string $orderSn, string $desc, ?string $extraData = null)
    {
        $this->payTimeExpire = date("YmdHis", strtotime("+3 minute", time()));
        $this->payNotifyUrl = \config('wechat.payment.default.notify_url', 'wechat');
        $this->config->setNotifyUrl($this->payNotifyUrl);
        $this->money = $money;
        $this->orderSn = $orderSn;
        $this->desc = $desc;
        $this->extraData = $extraData;
        return call_user_func_array([
            $this,
            $client
        ], []);
    }

    /**
     * 支付回调
     * User：caogang
     * DateTime：2021/6/29 15:52
     * @param $requestData
     * @return mixed|string
     * @throws \EasySwoole\Pay\Exceptions\InvalidArgumentException
     * @throws \EasySwoole\Pay\Exceptions\InvalidSignException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function payNotify($requestData)
    {
        $pay = new \EasySwoole\Pay\Pay();
        $data = $pay->weChat($this->config)->verify($requestData);
        $data = json_decode(json_encode($data), true);
        if ($data['return_code'] == "SUCCESS" && $data['result_code'] == "SUCCESS") {
            //  return \EasySwoole\Pay\WeChat\WeChat::success();//成功响应
            // 通知客服端
            $pay_notify_url = Order::query()->where('sn', $data['out_trade_no'])->value('pay_notify_url');
            $client = new \GuzzleHttp\Client();
            $ret = $client->post($pay_notify_url, ['form_params' => $data]);
            $result = $ret->getBody()->getContents();
            if (!$result) {
                return \EasySwoole\Pay\WeChat\WeChat::fail();//失败响应
            }
            if ($this->paySuccess((array)$data)) {
                return \EasySwoole\Pay\WeChat\WeChat::success();//成功响应
            } else {
                return \EasySwoole\Pay\WeChat\WeChat::fail();//失败响应
            }
        } else {
            return \EasySwoole\Pay\WeChat\WeChat::fail();//失败响应
        }
    }

    public function officialAccount()
    {
        $this->config->setAppId(\config('wechat.payment.default.app_id', 'wechat'));
        $officialAccount = new OfficialAccount();
        $officialAccount->setOpenid($this->extraData);
        $officialAccount->setOutTradeNo($this->orderSn);
        $officialAccount->setBody($this->desc);
        $officialAccount->setTotalFee($this->money);
        $officialAccount->setTimeExpire($this->payTimeExpire);
        $pay = new \EasySwoole\Pay\Pay();
        return $pay->weChat($this->config)->officialAccount($officialAccount)->toArray();
    }

    public function paySuccess(array $data)
    {
        try {
            PayNotify::create(PayNotify::fillableFromArray($data));
            Order::where('sn', $data['out_trade_no'])->update([
                'status'   => 1,
                'pay_time' => Carbon::parse($data['time_end'])->format('Y-m-d H:i:s')
            ]);
            return true;
        } catch (\Exception $exception) {
            (new Logger('log'))->error($exception->getMessage());
            return false;
        }

    }

    /**
     * @param string $orderSn
     * @return array|\EasySwoole\Spl\SplArray|mixed
     * @throws \EasySwoole\Pay\Exceptions\GatewayException
     * @throws \EasySwoole\Pay\Exceptions\InvalidArgumentException
     * @throws \EasySwoole\Pay\Exceptions\InvalidSignException
     */
    public function findOrderInfo(string $orderSn)
    {
        $this->config->setAppId(\config('wechat.payment.default.app_id', 'wechat'));
        $config = $this->config;
        $orderOne = PayNotify::where('out_trade_no', $orderSn)->first();
        if (!$orderOne) {
            $orderFind = new \EasySwoole\Pay\WeChat\RequestBean\OrderFind();
            $orderFind->setOutTradeNo($orderSn);
            $pay = new \EasySwoole\Pay\Pay();
            $info = $pay->weChat($config)->orderFind($orderFind);
            $this->paySuccess((array)$info);
        } else {
            $info = $orderOne->toArray();
        }
        return $info;
    }

}