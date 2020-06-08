<?php
namespace app\index\controller\rabbitmq;

use app\index\controller\BaseController;
use app\index\service\rabbitmq\Rabbit;

class Consume extends BaseController
{
    /**
     * 消费rabbimq里面数据，待检验
     */
    public function start()
    {
        $config=[
            'exchange'=>'exchange_test1',
            'queue'=>'queue_test1',
            'route_key'=>'route_test1',
            'consumer_tag'=>'consumer',
        ];
        app(Rabbit::class)->start($config);
    }
}