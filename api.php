<?php
header('Content-Type: application/json; charset=utf-8');
define('PATH',dirname(__FILE__).'/');
require PATH.'_Core.php';
$lz = isset($_GET['lz'])?$_GET['lz']:'';
$pw = isset($_GET['pw'])?$_GET['pw']:'';
$tp = isset($_GET['type'])?$_GET['type']:'';
if (empty($lz) or strpos($lz,'lanzous.com')==FALSE) {
    die(
        json_encode(
            ['code'=>0,'msg'=>'请检查输入是否有误']
        ,320)
    );
}
preg_match('/lanzous.com\/(.*)/',$lz,$lz_id);
$lz = 'https://www.lanzous.com/tp/'.$lz_id[1];
$Fun = new XhFun();
$api = 'https://www.lanzous.com/ajaxm.php';
$header = "Content-Type: application/x-www-form-urlencoded\r\nReferer: https://www.lanzous.com\r\nUser-Agent: {$Fun->ua}";
// $context = $Fun->trimAll( $Fun->xhGet($lz,$header) );
$context = $Fun->xhGet($lz,$header);
// echo $context;
if (strpos($context,'文件取消分享了')!==false) {
    die(
        json_encode(
            ['code'=>-1,'msg'=>'文件取消分享了']
        ,320)
    );
}
if (strpos($context,'function pwd()')!==false) {
    $filesize = $Fun->getSubstr($context,'class="mtt">(',' )');
    $update = $Fun->getSubstr($context,'时间:</span>',' <');
    $author = $Fun->getSubstr($context,'发布者:</span>',' <');
    $desc = $Fun->getSubstr($context,'class="mdo">',' <span');
    if (!empty($pw)) {
        $pw_len = strlen($pw);
        if (preg_match('/^[a-zA-Z0-9]+$/u',$pw) && $pw_len>=2&&$pw_len<=6) {
            $sign = $Fun->getSubstr($context,"downprocess','sign':'","','p'");
            // echo $sign;
            if ($sign !== '' or strlen($sign)>10) {
                $data = [
                    'action' => 'downprocess',
                    'sign' => $sign,
                    'p' => $pw
                ];
                $row = json_decode($Fun->xhPost($api,$data,$header));
                // print_r($row);
                if ($row->zt == 1) {
                    $downUrl = $Fun->getRedirect($row->dom.'/file/'.$row->url);
                    if ($tp == 'down') {
                        header('Location:'.$downUrl);die;
                    }
                    $ary = array(
                        'code' => 1,
                        'fileDown' => $downUrl,
                        'info' => [
                            'name' => $row->inf,
                            'size' => $filesize,
                            'author' => $author,
                            'update' => $update,
                            'desc' => $desc
                        ],
                        'address' => $Fun->getAddress()
                    );
                } else {
                    $ary = array(
                        'code' => 400,
                        'msg' => '获取失败，原因：'.$row->inf
                    );
                }
            } else {
                $ary = array(
                    'code' => -1,
                    'msg' => '接口已失效，请联系开发者修复'
                );
            }
        } else {
            $ary = array(
                'code' => 400,
                'msg' => '密码不符合规范，不能包含中文或小数点且需大于2小于6'
            );
        }
    } else {
        $ary = array(
            'code' => 400,
            'msg' => '此文件需要密码才能访问'
        );
    }
} else {
    // $context = $Fun->trimAll( $context );
    $filename = $Fun->getSubstr($context,'class="md">',' <');
    $filesize = $Fun->getSubstr($context,'mtt">( ',' )');
    $update = $Fun->getSubstr($context,'时间:</span>',' <span');
    $author = $Fun->getSubstr($context,'发布者:</span>',' <span');
    $desc = $Fun->getSubstr($context,'class="mdo">','\n');
    // echo $context;
    // $cdn = $Fun->getSubstr($context,"var cdomain='","';");
    $cdn = 'https://vip.d0.baidupan.com/file/';
    $fix = $Fun->getSubstr($context,"var urlload = '","';");
    // echo $cdn.$fix;
    if ($tp == 'down') {
        header('Location:'.$cdn.$fix);die;
    }
    if ($cdn!=='' or $fix!=='' or strlen($fix)>10) {
        $downUrl = $Fun->getRedirect($cdn.$fix);
        $ary = array(
            'code' => 1,
            'fileDown' => $downUrl,
            'info' => [
                'name' => $filename,
                'size' => $filesize,
                'author' => $author,
                'update' => $update,
                'desc' => $desc
            ],
            'address' => $Fun->getAddress()
        );
    } else {
        $ary = array(
            'code' => -1,
            'msg' => '接口已失效，请联系开发者修复'
        );
    }
}
echo json_encode($ary,320);