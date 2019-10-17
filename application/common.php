<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 导出文件 CSV
 * @param $data 对应数据
 * @param array $header 标题
 * @param string $filename 文件名称
 * @return bool
 */
function exportcsv($data, $header = [], $filename = '')
{
    if ('' == $filename) {
        $filename = substr(md5(uniqid()),0,12);
    }
    header('Content-Type:text/csv');
    header('Content-Disposition:attachment;filename="'.$filename.'.csv"');
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0');
    header('Pragma:public');
    ob_clean();
    $fp = fopen('php://outpput','w');

    if ($header) {
        foreach ($header as $k => $v) {
            $header[$k] = mb_convert_encoding($v,'gbk','utf-8');
        }
        fputcsv($fp,$header);
    }

    foreach ($data as $key => $value) {
        $aRow = [];
        foreach ($value as $k => $va) {
            $aRow[] = mb_convert_encoding($va,'gbk','utf-8');
        }
        fputcsv($fp,$header);
    }
    fclose($fp);
    return true;
}