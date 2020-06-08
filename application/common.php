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


//phpoffice/phpexcel 文件导入1
function excelToArray1(){
    require_once dirname(__FILE__) . '/Lib/Classes/PHPExcel/IOFactory.php';

    //加载excel文件
    $filename = dirname(__FILE__).'/result.xlsx';
    $objPHPExcelReader = PHPExcel_IOFactory::load($filename);

    $sheet = $objPHPExcelReader->getSheet(0);        // 读取第一个工作表(编号从 0 开始)
    $highestRow = $sheet->getHighestRow();           // 取得总行数
    $highestColumn = $sheet->getHighestColumn();     // 取得总列数

    $arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M', 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    // 一次读取一列
    $res_arr = array();
    for ($row = 2; $row <= $highestRow; $row++) {
        $row_arr = array();
        for ($column = 0; $arr[$column] != 'F'; $column++) {
            $val = $sheet->getCellByColumnAndRow($column, $row)->getValue();
            $row_arr[] = $val;
        }

        $res_arr[] = $row_arr;
    }

    return $res_arr;
}


//phpoffice/phpexcel 文件导入2
function excelToArray2(){
    require_once dirname(__FILE__) . '/Lib/Classes/PHPExcel/IOFactory.php';

    //加载excel文件
    $filename = dirname(__FILE__).'/result.xlsx';
    $objPHPExcelReader = PHPExcel_IOFactory::load($filename);

    $reader = $objPHPExcelReader->getWorksheetIterator();
    //循环读取sheet
    foreach($reader as $sheet) {
        //读取表内容
        $content = $sheet->getRowIterator();
        //逐行处理
        $res_arr = array();
        foreach($content as $key => $items) {

            $rows = $items->getRowIndex();              //行
            $columns = $items->getCellIterator();       //列
            $row_arr = array();
            //确定从哪一行开始读取
            if($rows < 2){
                continue;
            }
            //逐列读取
            foreach($columns as $head => $cell) {
                //获取cell中数据
                $data = $cell->getValue();
                $row_arr[] = $data;
            }
            $res_arr[] = $row_arr;
        }

    }

    return $res_arr;
}


/**
 * 创建(导出)Excel数据表格
 * @param  array   $list        要导出的数组格式的数据
 * @param  string  $filename    导出的Excel表格数据表的文件名
 * @param  array   $indexKey    $list数组中与Excel表格表头$header中每个项目对应的字段的名字(key值)
 * @param  array   $startRow    第一条数据在Excel表格中起始行
 * @param  [bool]  $excel2007   是否生成Excel2007(.xlsx)以上兼容的数据表
 * 比如: $indexKey与$list数组对应关系如下:
 *     $indexKey = array('id','username','sex','age');
 *     $list = array(array('id'=>1,'username'=>'YQJ','sex'=>'男','age'=>24));
 */
function exportExcel($list,$filename,$indexKey,$startRow=1,$excel2007=false){
    //文件引入
    require_once APP_ROOT.'/Api/excel/PHPExcel.php';
    require_once APP_ROOT.'/Api/excel/PHPExcel/Writer/Excel2007.php';

    if(empty($filename)) $filename = time();
    if( !is_array($indexKey)) return false;

    $header_arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M', 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    //初始化PHPExcel()
    $objPHPExcel = new PHPExcel();

    //设置保存版本格式
    if($excel2007){
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $filename = $filename.'.xlsx';
    }else{
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $filename = $filename.'.xls';
    }

    //接下来就是写数据到表格里面去
    $objActSheet = $objPHPExcel->getActiveSheet();
    //$startRow = 1;
    foreach ($list as $row) {
        foreach ($indexKey as $key => $value){
            //这里是设置单元格的内容
            $objActSheet->setCellValue($header_arr[$key].$startRow,$row[$value]);
        }
        $startRow++;
    }

    // 下载这个表格，在浏览器输出
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");;
    header('Content-Disposition:attachment;filename='.$filename.'');
    header("Content-Transfer-Encoding:binary");
    $objWriter->save('php://output');
}


//文件导出
function exportExcel2($list,$filename,$indexKey=array()){
    require_once dirname(__FILE__) . '/Lib/Classes/PHPExcel/IOFactory.php';
    require_once dirname(__FILE__) . '/Lib/Classes/PHPExcel.php';
    require_once dirname(__FILE__) . '/Lib/Classes/PHPExcel/Writer/Excel2007.php';

    $header_arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M', 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

    //$objPHPExcel = new PHPExcel();                        //初始化PHPExcel(),不使用模板
    $template = dirname(__FILE__).'/template.xls';          //使用模板
    $objPHPExcel = PHPExcel_IOFactory::load($template);     //加载excel文件,设置模板

    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);  //设置保存版本格式

    //接下来就是写数据到表格里面去
    $objActSheet = $objPHPExcel->getActiveSheet();
    $objActSheet->setCellValue('A2',  "活动名称：江南极客");
    $objActSheet->setCellValue('C2',  "导出时间：".date('Y-m-d H:i:s'));
    $i = 4;
    foreach ($list as $row) {
        foreach ($indexKey as $key => $value){
            //这里是设置单元格的内容
            $objActSheet->setCellValue($header_arr[$key].$i,$row[$value]);
        }
        $i++;
    }

    // 1.保存至本地Excel表格
    //$objWriter->save($filename.'.xls');

    // 2.接下来当然是下载这个表格了，在浏览器输出就好了
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");;
    header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
    header("Content-Transfer-Encoding:binary");
    $objWriter->save('php://output');
}