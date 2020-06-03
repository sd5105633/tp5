<?php
namespace app\index\repository\rabbimq;

use app\index\repository\BaseRep;
use app\index\model\QueuePush as QueModel;

class QueuePush extends BaseRep
{
    /**
     * 写入队列表
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        return app(QueModel::class)->insert($data);
    }
}