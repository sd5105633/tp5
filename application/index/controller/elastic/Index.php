<?php

namespace app\index\controller\elastic;

use app\index\controller\BaseController;
use Elasticsearch\ClientBuilder;
use think\Db;
use think\Exception;

class Index extends BaseController
{
    protected $client;

    public function initialize()
    {
        $host = [
            '192.168.1.159:9200'
        ];
        $this->client = ClientBuilder::create()->setHosts($host)->build();
    }

    public function index()
    {
        try {
            $res = Db::table("study_product")
                // ->limit(0,100)
                ->select();
            foreach ($res as $key => $val) {
                $params = [
                    'body' => [
                        'id' => $val['id'],
                        'letter' => $val['letter'],
                        'cid' => $val['cid'],
                        'name' => $val['name'],
                        'item' => $val['item'],
                        'mmaker' => $val['mmaker'],
                        'maker' => $val['maker'],
                        'homepage' => $val['homepage'],
                    ],
                    'id' => 'product_' . $val['id'],
                    'index' => 'product_index',
                    'type' => 'product_type',
                ];
                $ss = $this->client->index($params);
            }

            return json("成功");

        } catch (Exception $e) {
            return json($e->getTrace(), 104);
        }

    }

    public function search()
    {

        $params = [
            'index' => 'product_index',
            'type' => 'product_type',
            "from" => 0,
            "size" => 50,
        ];

        $params['body'] = [
            'query' => [
                'bool' => [
                    "must" => [
                        [
                            'match' => [
                                'name' => '*lm385*',
                            ],
                        ],
                    ]
                ],
            ]
        ];

        $res = $this->client->search($params);
        return json($res);
    }
}