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
namespace App\Controller;

use AndPHP\HyperfSign\Annotation\Sign;

/**
 * @Sign("verify")
 */
class IndexController extends AbstractController
{


    /**
     * @Sign("gen")
     */
    public function index()
    {

//        $info = $this->payment->findOrderInfo(0,0,'2021061711264743');

//        return $this->success($info);

        return ['username'=>'zhangshan'];
    }

    public function test(){
        var_dump($this->index());
    }
}
