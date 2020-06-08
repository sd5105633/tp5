<?php
namespace app\index\repository\rabbitmq;

use app\index\repository\BaseRep;
use app\index\model\QueuePush as QueModel;

class QueuePush extends BaseRep
{
    /**
     * 写入队列表
     * @param $data
     * @return mixed
     */
    public function insert(array $data): int
    {
        return app(QueModel::class)->insert($data);
    }


    public function update(array $data,array $where): bool
    {
        return app(QueModel::class)->where($where)->update($data);
    }
}