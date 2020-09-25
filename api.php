<?php
/**
 * 蓝奏云直链提取
 * @param url|链接 pwd|密码 type=down|直接下载
 * @param 修复请求参数逻辑，修复链接获取失败问题，删了部分代码
 * @param 状态码：0|获取失败 1|获取成功 404|文件已取消 400|提取直链失败
 * @date 2020-09-25
 * @copyright https://github.com/xhgzs/LanzouApi All rights reserved.
 */
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('PATH',dirname(__FILE__).'/');
require_once PATH.'_Core.php';
$url = isset($_GET['lz']) ? $_GET['lz'] : '';
$pwd = isset($_GET['pwd']) ? $_GET['pwd'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
if (empty($url) or (strpos($url,'lanzous.com')==false && strpos($url,'lanzoux.com')==false && strpos($url,'lanzoui.com')==false)) {
    die(
        json_encode(
            ['code'=>0,'msg'=>'链接有误或不能为空','fileDown'=>[]]
        ,320)
    );
}
$Fun = new xhFun();
preg_match('/lanzou[a-zA-Z].com\/(.*)/',$url,$id);
// 下面$mtp或$api接口可能会因地域不同而导致无法访问
// 我这边是广州的服务器是可以的，服务器能访问就行了，你们自己看着改
$mtp = 'https://www.lanzous.com/tp/'.$id[1];
$api = 'https://www.lanzous.com/ajaxm.php';
$header = "{$Fun->ua}|Referer: {$mtp}";
// $context = $Fun->trimAll( $Fun->xhGet($lz,$header) );
$context = $Fun->xhGet($mtp,$header);
if (strpos($context,'文件取消分享了')!==false) {
    die(
        json_encode(
            ['code'=>404,'msg'=>'文件取消分享了','fileDown'=>[]]
        ,320)
    );
}
if (strpos($context,'function pwd()')!==false) {
    $size = $Fun->getSubstr($context,'class="mtt">( ',' )');
    if (empty($pwd)) {
        die(
            json_encode(
                ['code'=>400,'msg'=>'此文件需要密码才能请求','fileDown'=>[]]
            ,320)
        );
    }
    $pwLen = strlen($pwd);
    if (preg_match('/^[a-zA-Z0-9]+$/u',$pwd) && $pwLen>=2&&$pwLen<=6) {
        $sign = $Fun->getSubStr($context,"downprocess','sign':'","','p'");
        $data = [
            'action' => 'downprocess',
            'sign' => $sign,
            'p' => $pwd
        ];
        // echo $sign;
        $row = json_decode($Fun->xhPost($api,$data,$header),true);
        if ($row['zt'] !== 1) {
            die(
                json_encode(
                    ['code'=>400,'msg'=>'请求失败，原因：'.$row['inf'],'fileDown'=>[]]
                ,320)
            );
        }
        // print_r($row);
        // $downUrl = $row['dom'].'/file/'.$row['url'];
        $downUrl = $Fun->getRedirect($row['dom'].'/file/'.$row['url']);
        if ($type == 'down') {
            header('Location:'.$downUrl);die;
        }
        $arr = [
            'code' => 1,
            'name' => $row['inf'],
            'size' => $size,
            'fileDown' => $downUrl
        ];
    } else {
        $arr = [
            'code' => 400,
            'msg' => '密码不符合规范，不能包含中文或小数点且需大于2小于6',
            'fileDown' => []
        ];
    }
} else {
    $name = $Fun->getSubstr($context,'class="md">',' <');
    $size = $Fun->getSubstr($context,'class="mtt">( ',' )');
    $cdn = 'https://vip.d0.baidupan.com/file/';
    $fix = $Fun->getSubstr_($context,"domianload + '","'");
    // echo $cdn.$fix;
    if ($fix=='' or strlen($fix)<10) {
        die(
            json_encode(
                ['code'=>400,'msg'=>'接口已失效，请联系开发者修复','fileDown'=>[]]
            ,320)
        );
    }
    // $downUrl = $cdn.$fix;
    $downUrl = $Fun->getRedirect($cdn.$fix);
    if ($tp == 'down') {
        header('Location:'.$cdn.$fix);die;
    }
    $arr = [
        'code' => 1,
        'name' => $name,
        'size' => $size,
        'fileDown' => $downUrl
    ];
}
echo json_encode($arr,320);