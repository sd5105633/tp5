<?php
namespace app\index\service\rabbitmq;

use app\index\service\BaseService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Message\AMQPMessage;
use think\Exception;
use think\exception\ErrorException;

class Rabbit extends BaseService
{
    //推送消息到队列
    public static function pushMessage($data,$config)
    {
        try {
            //连接配置
            $host_config = config('rabbitmq.rabbitmq');
            $connection = new AMQPStreamConnection(
                $host_config['Host'],
                $host_config['Port'],
                $host_config['User'],
                $host_config['Pass'],
                $host_config['Vhost']
            );
            $channel = $connection->channel();

            $channel->queue_declare($config['queue'],false,true,false,false);
            $channel->exchange_declare($config['exchange'],'direct',false,true,false);
            $channel->queue_bind($config['queue'],$config['exchange'],$config['route_key']);

            $message = new AMQPMessage(
                $data,
                [
                    'content_type'=>'application/json',
                    'content_encoding'=>'utf8',
                    'delivery_mode'=>AMQPMessage::DELIVERY_MODE_PERSISTENT
                ]
            );

            $channel->basic_publish($message,$config['exchange'],$config['route_key']);
            $channel->close();
            $connection->close();
            return 'ok';
        } catch (AMQPChannelClosedException $e) {
            echo 'AMQPCONNECTION:'.$e->getMessage();
        } catch (\ErrorException $e){
            echo 'THINK:'.$e->getMessage();
            exit;
        }



    }
}