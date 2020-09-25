<?php
class xhFun
{
    public $ua = 'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1';
    public function xhGet($url,$header=''){
        if ($header) {
            $head = str_replace("|","\r\n",$header."|");
            $options = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => $head,
                    'timeout' => 6*10
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url,false,$context);
        } else {
            $result = file_get_contents($url);
        }
        return $result;
    }
    public function xhPost($url,$data,$header=''){
        $postdata = $data;
        if (is_array($data) or is_object($data)) {
            $postdata = http_build_query($data);
        }
        if ($header) {
            $head = str_replace("|","\r\n",$header."|");
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => $head,
                    'content' => $postdata,
                    'timeout' => 6*10
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url,false,$context);
        } else {
            $result = file_get_contents($url);
        }
        return $result;
    }
    // 获取客户端ip
    public function ip(){if(getenv('HTTP_CLIENT_IP')){$a=getenv('HTTP_CLIENT_IP');}elseif(getenv('HTTP_X_FORWARDED_FOR')){$a=getenv('HTTP_X_FORWARDED_FOR');}elseif(getenv('HTTP_X_FORWARDED')){$a=getenv('HTTP_X_FORWARDED');}elseif(getenv('HTTP_FORWARDED_FOR')){$a=getenv('HTTP_FORWARDED_FOR');}elseif(getenv('HTTP_FORWARDED')){$a=getenv('HTTP_FORWARDED');}else{$a=$_SERVER['REMOTE_ADDR'];}return $a;}
    public function ip_(){
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }
    // 获取随机ip
    public function randip(){
        $ip2id = round(rand(600000, 2550000) / 10000);
        $ip3id = round(rand(600000, 2550000) / 10000);
        $ip4id = round(rand(600000, 2550000) / 10000);
        $arr_1 = array("218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211");
        $randarr= mt_rand(0,count($arr_1)-1);
        $ip1id = $arr_1[$randarr];
        return $ip1id.".".$ip2id.".".$ip3id.".".$ip4id;
    }
    // 取毫秒时间戳+随机2位数字
    public function microtime_rand(){ 
        $microtime = str_replace(' ','',microtime());
        $rand = mt_rand(0,2);
        return substr($microtime,0,strlen($microtime)-$rand);
    }
    // 截取中间文本
    public function getSubStr($str,$leftStr,$rightStr){
        $left = strpos($str, $leftStr);
        $right = strpos($str, $rightStr,$left);
        if($left < 0 or $right < $left) return '';
        return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
    }
    public function getSubStr_($content,$start,$end){
        $r = explode($start, $content);
        if (isset($r[1])) {
            $r = explode($end, $r[1]);
            return $r[0];
        }
        return '';
    }
    // 取重定向地址
    public function getRedirect($url,$ref=''){
        $headers = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: zh-CN,zh;q=0.9',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Pragma: no-cache',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: '.$this->ua
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        if ($ref) {
            curl_setopt($curl, CURLOPT_REFERER, $ref);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($curl);
        $url=curl_getinfo($curl);
        curl_close($curl);
        return $url["redirect_url"];
    }
    // 取ip所在地
    public function getAddress(){
        $api = 'https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query='.self::ip().'&co=&resource_id=6006&t=1595918972828&ie=utf8&oe=utf8&cb=op_aladdin_callback&format=json&tn=baidu&cb=jQuery110208782869533668733_1595918932052&_=1595918932053';
        $result = file_get_contents($api);
        return $this->getSubStr($result,'location":"','",');
    }
    // 去除全部空 文本换行
    public function trimAll($context){
        $str = array(" ","　","\t","\n","\r");
        return str_replace($str,'',$context);
    }
    // 过滤文本提取Url链接
    public function toUrl($str){
        if(empty($str))return '';
        $reg = "/(http|https):\/\/([\w.]+\/?)\S*/";
        preg_match($reg,$str,$link);
        return $link[0];
    }
}