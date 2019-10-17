<?php
namespace app\index\controller\rabbitmq;

use app\index\controller\BaseController;
use app\index\service\rabbitmq\Rabbit;

class Index extends BaseController
{
    //实时写入到rabbitmq
    public function mqPush()
    {

        $config=[
            'exchange'=>'exchange_test1',
            'queue'=>'queue_test1',
            'route_key'=>'route_test1',
        ];

        for ($i = 0; $i < 10; $i++) {
            $arr = [
                'name'=>'qicheng'.$i,
                'sex'=>'male',
                'round'=>date('YmdHis').rand(1000,9999),
            ];
            $arr = json_encode($arr);

            $res = Rabbit::pushMessage($arr,$config);

        }

        return '执行完毕';
    }

    //延迟写入到队列
    public function mqDelayPush()
    {

    }
}