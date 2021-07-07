<?php


namespace App\Controller;


use App\Constants\Enum\PaymentStatusEnum;
use App\Constants\ErrorCode;
use App\Model\Order;
use App\Model\OrderRefund;
use App\Service\PaymentService;
use App\Service\RefundMoneyService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

class PaymentController extends AbstractController
{
    /**
     * @Inject
     * @var PaymentService
     */
    protected $payment;

    /**
     * @Inject
     * @var RefundMoneyService
     */
    protected $refundService;

    /**
     * 支付回调的通知
     * User：caogang
     * DateTime：2021/6/29 17:12
     * @param RequestInterface $requset
     * @return mixed
     * @throws \Exception
     */
    public function notify(RequestInterface $requset)
    {
        return $this->payment->payNotify(PaymentStatusEnum::Platform['weixin'], PaymentStatusEnum::Channel['wechat'], $requset->getBody()->getContents());
    }

    public function refundNotify(RequestInterface $requset)
    {
        return $this->refundService->notify(PaymentStatusEnum::Platform['weixin'], PaymentStatusEnum::Channel['wechat'], $requset->getBody()->getContents());
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function pay()
    {
        $params = $this->request->all();
        $this->validatorParams($params, [
            'order_sn'  => 'required',
            'desc'      => 'required',
            'money'     => 'required',
            'openid'    => 'required',
            'create_ip' => 'required',
            'client'    => 'required',
            //            'platform'  => 'required',
            //            'channel'   => 'required',
        ], [
            'order_sn.required'  => '订单号不能为空',
            'desc.required'      => '订单内容不能为空',
            'money.required'     => '订单金额不能为空',
            'openid.required'    => '订单号不能为空',
            'create_ip.required' => 'ip不能为空',
            'client.required'    => '客户端类型不能为空',
            //            'platform.required'  => '支付平台不能为空',
            //            'channel.required'   => '支付渠道不能为空',
        ]);

        $orderSN = $params['order_sn'];
        //        $openid = 'ocdEF0yOIvwK9jeDX3-Zv5zKK9_U';
        $order = [
            'money'        => (int)$params['money'],
            'pay_order_sn' => $orderSN,
            'desc'         => "测试" . $params['desc'],
        ];

        $Platform = $params['platform'] ?? 'weixin';
        $Channel = $params['channel'] ?? 'wechat';

        $insertData = [
            'sn'             => $params['order_sn'],
            'body'           => $params['desc'],
            'total_fee'      => $params['money'],
            'client'         => $params['client'],
            'openid'         => $params['openid'],
            'create_ip'      => $params['create_ip'],
            'platform'       => $Platform,
            'channel'        => $Channel,
            'pay_notify_url' => $params['pay_notify_url']
        ];
        if (!Order::create($insertData)) {
            return $this->error(5000, '订单创建失败');
        }

        $Client = $params['client'];
        try {
            $response = $this->payment->pay($order, PaymentStatusEnum::Platform[$Platform], PaymentStatusEnum::Channel[$Channel], PaymentStatusEnum::Client[$Client], $params['openid']);
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
        return $this->success($response);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function findOrderInfo()
    {
        $params = $this->request->all();
        $this->validatorParams($params, [
            'order_sn' => 'required',
            //            'platform'  => 'required',
            //            'channel'   => 'required',
        ], [
            'order_sn.required' => '订单号不能为空',
            //            'platform.required'  => '支付平台不能为空',
            //            'channel.required'   => '支付渠道不能为空',
        ]);
        try {
            $info = $this->payment->findOrderInfo(0, 0, $params['order_sn']);
        } catch (\Exception $exception) {
            return $this->error(ErrorCode::PARAMS_INVALID, $exception->getMessage());
        }
        return $this->success($info);
    }

    public function refund()
    {
        $params = $this->request->all();
        $this->validatorParams($params, [
            'order_sn'          => 'required',
            'refund_amount'     => 'required',
            'refund_notify_url' => 'required'
        ], [
            'order_sn.required' => '订单号必须',
            'refund_amount'     => '退款金额必须',
            'refund_notify_url' => '回调地址必须',
        ]);
        $order = Order::query()->where('sn', $params['order_sn'])->first();
        $refundData = [
            'total_fee'  => $order->total_fee,
            'refund_fee' => $params['refund_amount'],
            'order_sn'   => $params['order_sn'],
            'notify_url' => $params['refund_notify_url'],
            'refund_sn'  => 'TK' . $params['order_sn']
        ];
        if (!OrderRefund::updateOrCreate($refundData, $refundData)) {
            return $this->error(5000, '退款订单创建失败');
        }
        $Platform = $params['platform'] ?? 'weixin';
        $Channel = $params['channel'] ?? 'wechat';
        $Client = $params['client'] ?? 0;

        try {
            $response = $this->refundService->refund($refundData, PaymentStatusEnum::Platform[$Platform], PaymentStatusEnum::Channel[$Channel], PaymentStatusEnum::Client[$Client]);
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }
        return $this->success($response);
    }
}