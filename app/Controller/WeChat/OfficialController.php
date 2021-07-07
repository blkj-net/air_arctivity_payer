<?php


namespace App\Controller\WeChat;


use App\Controller\AbstractController;
use App\Model\User;
use Carbon\Carbon;
use EasySwoole\WeChat\Factory;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use function Swoole\Coroutine\Http\request;

class OfficialController extends AbstractController
{
    /**
     * @Inject
     * @var User
     */
    protected $userModel;

    /**
     * 处理微信的请求消息
     * @param RequestInterface $request
     * @return string
     */
    public function serve(RequestInterface $request, ResponseInterface $response)
    {
        $officialAccount = Factory::officialAccount(config('wechat.official_account.default'));
        $server = $officialAccount->server;

        $replyResponse = $server->forceValidate()->serve($request);
        var_dump($replyResponse);
        $response->withStatus($replyResponse->getStatusCode());
        /**
         * PSR-7 的 Header 并不是单纯的 k => v 结构
         */
        foreach ($replyResponse->getHeaders() as $name => $values) {
            $response->withHeader($name, implode(", ", $values));
        }

        // 将响应输出到客户端
        $return = $response->raw($replyResponse->getBody()->__toString());
        var_dump($return);
        return $return;
    }

    public function getUser()
    {
        $officialAccount = Factory::officialAccount(config('wechat.official_account.default'));
        $users = $officialAccount->user->list();
        $usersInfo = $officialAccount->user->select($users['data']['openid']);
        var_dump($users);
        return $usersInfo;
    }

    public function oauth()
    {
        $params = $this->request->all();
        var_dump($params);
        $this->validatorParams($params, [
            'code' => 'required'
        ], [
            'code.required' => 'code授权码不能为空',
        ]);
        $config = config('wechat.official_account.default');

        $officialAccount = \EasySwoole\WeChat\Factory::officialAccount($config);

        $oauth = $officialAccount->oauth;

        // 获取 OAuth 授权结果用户信息
        $code = $this->request->input('code');

        try {
            $user = $oauth->userFromCode($code);
            var_dump($user);
        } catch (\Exception $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        }

        $findUser = $this->userModel->where('openid', $user->getId())->first();
        $insertData = $user->getRaw() + $user->getTokenResponse();
        if (!$findUser) {
            User::create(User::fillableFromArray($insertData));
        }
        return $this->success($insertData);
    }

    public function oauthCallback()
    {
        $config = config('wechat.official_account.default');

        $officialAccount = \EasySwoole\WeChat\Factory::officialAccount($config);

        $oauth = $officialAccount->oauth;

        // 获取 OAuth 授权结果用户信息
        $code = $this->request->input('code');

        $user = $oauth->userFromCode($code);
        $findUser = $this->userModel->where('openid', $user->getId())->first();
        if (!$findUser) {
            $insertData = $user->getRaw() + $user->getTokenResponse();
            User::create(User::fillableFromArray($insertData));
        }
        //        var_dump($user->getRaw());
        return [
            'user'  => $user->getRaw(),
            'token' => $user->getTokenResponse()
        ];

    }


}