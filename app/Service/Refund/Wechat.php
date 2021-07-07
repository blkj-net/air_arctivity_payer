<?php


namespace App\Service\Refund;


use App\Model\Order;
use App\Model\OrderRefund;
use App\Service\Abstracts\RefundAbstract;
use EasySwoole\Pay\WeChat\Config;

class Wechat extends RefundAbstract
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

    public function refund(int $money, string $client, string $orderSn, string $desc)
    {
//        $this->refundTimeExpire = date("YmdHis", strtotime("+3 minute", time()));
        $this->refundNotifyUrl = \config('wechat.payment.default.refund_notify_url', 'wechat');
//        $this->config->setNotifyUrl($this->refundNotifyUrl);
        $this->money = $money;
        $this->orderSn = $orderSn;
        $this->refundDesc = $desc;
        return call_user_func_array([
            $this,
            $client
        ], []);
    }

    public function refundNotify($requestData)
    {
        $pay = new \EasySwoole\Pay\Pay();
        $data = $pay->weChat($this->config)->verify($requestData, true);
        $data = json_decode(json_encode($data), true);
        if ($data['return_code'] == "SUCCESS") {
            // 通知客服端
            $pay_notify_url = OrderRefund::query()->where('order_sn', $data['out_trade_no'])->value('notify_url');
            $client = new \GuzzleHttp\Client();
            $ret = $client->post($pay_notify_url, ['form_params' => $data]);
            $result = $ret->getBody()->getContents();
            if (!$result) {
                return \EasySwoole\Pay\WeChat\WeChat::fail();//失败响应
            }
            if ($this->refundSuccess($data)) {
                return \EasySwoole\Pay\WeChat\WeChat::success();//成功响应
            } else {
                return \EasySwoole\Pay\WeChat\WeChat::fail();//失败响应
            }
            return \EasySwoole\Pay\WeChat\WeChat::success();//成功响应
        }
        return \EasySwoole\Pay\WeChat\WeChat::fail();//失败响应
    }

    public function refundSuccess($data)
    {
        try {
            Order::query()->where('sn', $data['out_trade_no'])->update(['status' => 4]);
            OrderRefund::query()->where('refund_sn', $data['out_trade_no'])->update(['status' => 1]);
            return true;
        } catch (\Exception $exception) {
            (new Logger('log'))->error($exception->getMessage());
            return false;
        }
    }

    public function officialAccount()
    {
        $total_fee = Order::where('sn', $this->orderSn)->value('total_fee');
        $this->config->setAppId(\config('wechat.payment.default.app_id', 'wechat'));
        $refund = new \EasySwoole\Pay\WeChat\RequestBean\Refund();
        $refund->setOutTradeNo($this->orderSn);
        $refund->setOutRefundNo('TK' . $this->orderSn);
        $refund->setTotalFee($total_fee);
        $refund->setRefundFee($this->money); // 退款金额
        $refund->setNotifyUrl($this->refundNotifyUrl);
        $refund->setRefundDesc($this->refundDesc);
        $pay = new \EasySwoole\Pay\Pay();
        return $pay->weChat($this->config)->refund($refund);
    }

}