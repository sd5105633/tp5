<?php
namespace app\index\service\rabbitmq;

use app\index\service\BaseService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Exception\AMQPBasicCancelException;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Message\AMQPMessage;
use app\index\repository\rabbitmq\QueuePush as RepQuePush;
use think\Exception;

class Rabbit extends BaseService
{
    protected $connection;
    protected $channel;

    /**
     * 队列连接初始化
     */
    public function initialize()
    {
        register_shutdown_function([$this,'shutdown']);

    }

    /**
     * 推送消息到队列
     * @param $data
     * @param $config
     * @return string
     */
    public function pushMessage($data,$config)
    {

        try {
            $params = [];
            $params['content']= $data;
            $params['create_at']= time();
            $params['create_datetime']= date('Y-m-d H:i:s');
            $params['status']= 1;
            $id = app(RepQuePush::class)->insert($params);
            //连接配置
            $host_config = config('rabbitmq.rabbitmq');
            $this->connection = new AMQPStreamConnection(
                $host_config['Host'],
                $host_config['Port'],
                $host_config['User'],
                $host_config['Pass'],
                $host_config['Vhost']
            );
            $this->channel = $this->connection->channel();

            $this->channel->queue_declare($config['queue'],false,true,false,false);
            $this->channel->exchange_declare($config['exchange'],'direct',false,true,false);
            $this->channel->queue_bind($config['queue'],$config['exchange'],$config['route_key']);

            $message = new AMQPMessage(
                $data,
                [
                    'content_type'=>'application/json',
                    'content_encoding'=>'utf8',
                    'delivery_mode'=>AMQPMessage::DELIVERY_MODE_PERSISTENT
                ]
            );
            $this->channel->basic_publish($message,$config['exchange'],$config['route_key']);

            $where['id'] = $id;
            $update['update_at'] = time();
            $update['update_datetime'] = date('Y-m-d H:i:s');
            $res = app(RepQuePush::class)->insert($update,$where);

            return true;
        } catch (AMQPProtocolException $e){
            echo 'THINK:'.$e->getMessage();
            exit;
        }catch (AMQPChannelClosedException $e) {
            echo 'AMQPCONNECTION:'.$e->getMessage();
            exit;
        }  catch (AMQPConnectionClosedException $e){
            echo 'AMQPCONNECTION:'.$e->getMessage();
            exit;
        } catch (\Exception $e){
            header("content-type:text/html;charset=gbk");
            echo 'Exception111:'.$e->getMessage();
            exit;
        }catch (AMQPIOException $e){
            header("content-type:text/html;charset=gbk");
            echo 'AMQPCONNECTION333:'.$e->getMessage();
            exit;
        }

    }


    /**
     * 消费队列 开始
     * @param $config
     */
    public function start($config)
    {

        $this->channel->queue_declare($config['queue'],false,true,false,false);
        $this->channel->exchange_declare($config['exchange'],'direct',false,true,false);
        $this->channel->queue_bind($config['queue'],$config['exchange'],$config['route_key']);

        $this->channel->basic_consume(
            $config['queue'],
            $config['consumerTag'],
            false,
            false,
            false,
            false,
            [$this,'process_message']
        );

        register_shutdown_function([$this,'shutdown']);
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    /**
     * 实际消费队列的函数
     * @param $message
     */
    protected function process_message($message)
    {
        if ($message->body !== 'quit') {
            //检测是否返回相关数据
            $obj = json_decode($message->body,1);
            if (!isset($obj->name)) {
                echo 'error data\n';
            }else{

            }
        }
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        if ($message->body === 'quit') {
            $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
        }
    }

    /**
     * 关闭连接，关闭通道
     * @param $channel
     * @param $connection
     */
    public function shutdown(){

        echo "------------------<br>";
        echo '123123';
        if(is_object($this->channel)){
            $this->channel->close();
        }
        if(is_object($this->connection)){
            $this->connection->close();
        }

    }
}