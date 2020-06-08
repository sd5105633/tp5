<?php
namespace app\index\controller\rabbitmq;

use app\index\controller\BaseController;
use app\index\service\rabbitmq\Rabbit;

/**
 * 写入队列
 * Class Index
 * @package app\index\controller\rabbitmq
 */
class Index extends BaseController
{
    /**
     * 实时写入到rabbitmq
     * @return string
     */
    public function mqPush()
    {
        $config=[
            'exchange'=>'exchange_test1',
            'queue'=>'queue_test1',
            'route_key'=>'route_test1',
        ];

        for ($i = 10; $i < 20; $i++) {
            $arr = [
                'name'=>'qicheng'.$i,
                'sex'=>'male',
                'round'=>date('YmdHis').rand(1000,9999),
            ];
            $arr = json_encode($arr);

            $res = app(Rabbit::class)->pushMessage($arr,$config);
            usleep(100);
        }
        app(Rabbit::class )->shutdown();

        return '执行完毕';
    }

    //延迟写入到队列
    public function mqDelayPush()
    {

    }
}