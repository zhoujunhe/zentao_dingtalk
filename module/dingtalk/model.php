/*
 * created by wyne, QQ: 366659539
 */
<?php
class dingtalkModel extends model
{

    /**
     * Check Ali DingTalk Login Code
     * @access public
     * @return bool
     */
    public function updateSessionDing($del=false){
        if($del){$this->session->set('ding_rand',null);return null;}
        $random = rand(1000,9999);
        $this->session->set('ding_rand', $random);

        return $random;
    }

    /*
     * CURL请求封装
     * $url 请求地址字符串
     * $params POST请求参数
     * $method 请求方式,默认为post
     * $header 请求头参数
     * $ssl 请求协议是否https,默认是
     * @return 返回请求结果字符串
     */
    public function curl($url='',$params=[],$method='POST',$header=['Content-Type:application/json'],$ssl=true){

        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$method);
        if(strtoupper($method)=='POST'){
            if(!empty($params)){
                curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
            }
        }
        if(!empty($header)){
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }
        if($ssl){
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        }
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /*
     * 缓存数据文件方法
     * $name 数据文件名
     * $data 数据内容,若不传$data则为get获取,否则为set设置
     * $etime 缓存有效时间(秒)
     * @return 返回内容,若为设置则返回写入长度,若为获取则返回数据内容
     */
    public function caches($name,$data=false,$etime=false){
        $path = $_SERVER['DOCUMENT_ROOT'].DS.'tmp'.DS.'data'.DS;
        if(!file_exists($path) && !is_dir($path)) mkdir($path,0777,true);
        if($data===false){
            $file = file_get_contents($path.$name.'.php');
            if(!empty($file)){
                $time = substr($file,2,10);
                if($time!='0000000000' && $time<time()){ @unlink($path.$name.'.php'); return null; }
                return unserialize(substr($file,12));
            }
            return null;
        }else{
            if($data==null){@unlink($path.$name.'.php'); return true;}
            $str = $etime===false || intval($etime)<=0 ? '//0000000000' : '//'.(time()+intval($etime));
            if(file_put_contents($path.$name.'.php',$str.serialize($data))) return true;
            return false;
        }
    }

    /*
     * 获取钉钉授权码
     * @return array
     * */
    public function getDingtalkToken(){
        $token = $this->caches('ding_token');
        if(empty($token)){
            $result = $this->curl('https://oapi.dingtalk.com/sns/gettoken?appid='.$this->config->ding->appid.'&appsecret='.$this->config->ding->appsecret,[],'GET');
            $res = json_decode($result,true);
            if($res['errcode']!=0) return ['errcode'=>$res['errcode'],'errmsg'=>$res['errmsg']];
            $token = $res['access_token'];
            $this->caches('ding_token',$token,3600);
        }
        return $token;
    }

    /*
     * 获取钉钉用户唯一标识和持久码
     * @param $code 用户登录临时码
     * @return array
     * */
    public function getDingtalkUserToken($code){
        $token = $this->getDingtalkToken();
        if(empty($token)) ['errcode'=>500,'errmsg'=>'缺少授权码Token'];
        if(empty($code)) ['errcode'=>501,'errmsg'=>'缺少用户登录临时码code'];
        $result = $this->curl('https://oapi.dingtalk.com/sns/get_persistent_code?access_token='.$token,json_encode(['tmp_auth_code'=>$code]));
        $res = json_decode($result,true);
        if($res['errcode']!=0) return ['errcode'=>$res['errcode'],'errmsg'=>$res['errmsg']];
        return ['errcode'=>0,'errmsg'=>'ok','openid'=>$res['openid'],'persistent_code'=>$res['persistent_code'],'unionid'=>$res['unionid']];
    }

    /*
     * 获取用户授权的SNS_TOKEN码
     * @param $openid 用户应用唯一身份标识
     * @param $persistent_code 用户持久授权码
     * */
    public function getDingtalkUserSNS($openid,$persistent_code){
        $sns = $this->caches('ding_'.$openid);
        if(empty($sns)){
            $token = $this->getDingtalkToken();
            if(empty($token)) ['errcode'=>500,'errmsg'=>'缺少授权码Token'];
            if(empty($openid)) ['errcode'=>500,'errmsg'=>'缺少用户openid'];
            if(empty($persistent_code)) ['errcode'=>502,'errmsg'=>'缺少用户授权持久码persistent_code'];
            $result = $this->curl('https://oapi.dingtalk.com/sns/get_sns_token?access_token='.$token,json_encode(['openid'=>$openid,'persistent_code'=>$persistent_code]));
            $res = json_decode($result,true);
            if($res['errcode']!=0) return ['errcode'=>$res['errcode'],'errmsg'=>$res['errmsg']];
            $sns = $res['sns_token'];
            $this->caches('ding_'.$openid,$sns,3600);
        }
        return ['errcode'=>0,'errmsg'=>'ok','sns_token'=>$sns];

    }

    /*
     * 根据sns授权码获取钉钉用户信息
     * @param $sns 用户授权的SNS_TOKEN
     * */
    public function getDingtalkUserInfo($sns){
        if(empty($sns)) ['errcode'=>500,'errmsg'=>'缺少用户授权码sns'];
        $result = $this->curl('https://oapi.dingtalk.com/sns/getuserinfo?sns_token='.$sns,[],'GET');
        $res = json_decode($result,true);
        if($res['errcode']!=0) return ['errcode'=>$res['errcode'],'errmsg'=>$res['errmsg']];
        return ['errcode'=>0,'errmsg'=>'ok','user_info'=>$res['user_info']];

    }

    /*
     * 根据unionid获取钉钉企业用户userid
     * @param $unionid 用户的唯一ID
     * */
    public function getDingtalkCompanyUserid($unionid){
        $token = $this->getDingtalkToken();
        if(empty($sns)) ['errcode'=>500,'errmsg'=>'缺少用户授权码sns'];
        $result = $this->curl('https://oapi.dingtalk.com/user/getUseridByUnionid?access_token='.$token.'&unionid='.$unionid,[],'GET');
        $res = json_decode($result,true);
        if($res['errcode']!=0) return ['errcode'=>$res['errcode'],'errmsg'=>$res['errmsg']];
        return $res;

    }

    /*
     * 根据钉钉企业用户userid获取用户详情
     * @param $unionid 用户的唯一ID
     * */
    public function getDingtalkCompanyUserInfo($userid){
        $token = $this->getDingtalkToken();
        if(empty($sns)) ['errcode'=>500,'errmsg'=>'缺少用户授权码sns'];
        $result = $this->curl('https://oapi.dingtalk.com/user/get?access_token='.$token.'&userid='.$userid,[],'GET');
        $res = json_decode($result,true);
        if($res['errcode']!=0) return ['errcode'=>$res['errcode'],'errmsg'=>$res['errmsg']];
        return $res;
    }

    /*
     * 错误跳转页
     * @param $url 跳转地址
     * @param $msg 跳转提示内容
     * */
    public function errjump($url,$msg){
        echo "<html><head><meta charset='utf-8'><script>setTimeout(function(){location.href='".$url."'},300000);</script></head>";
        echo "<body><table align='center' style='width:700px; margin-top:100px; border:1px solid gray; font-size:14px;'><tr><td style='padding:8px'>";
        die("<div style='margin-bottom:8px;'>登录失败! <strong style='color:#ed980f'>".$msg."</strong> <br/>300 秒后页面自动跳转</div>");
    }

    /*
     * 插入钉钉用户数据和用户组关系
     * */
    public function regDingtalkUser($data){
        $duser = new stdclass();
        $duser->dt_openid = $data['dt_openid'];
        $duser->dt_persistent_code = $data['dt_persistent_code'];
        $duser->dt_unionid = $data['dt_unionid'];
        $duser->dt_dingid = $data['dt_dingId'];
        $duser->dt_nick = $data['dt_nick'];
        $duser->realname = $data['dt_nick'];
        $duser->nickname = $data['dt_nick'];
        $duser->account  = 'dt_'.substr($data['dt_openid'],0,12);
        $duser->role = 'others';
        $duser->dept = 0;
        $duser->password = md5('123456');
        $duser->gender   = 'm';
        $dusergroup = new stdclass();
        $dusergroup->account = $duser->account;
        $dusergroup->group = 10;
        $this->dao->insert(TABLE_USERGROUP)->data($dusergroup)->exec();
        return $this->dao->insert(TABLE_USER)->data($duser)->exec();
    }

    /*
     * 检查钉钉用户是否注册过,若注册过则直接查询返回
     * */
    public function checkDingtalkUser($dt_openid,$dt_persistent_code){
        $user = $this->dao->select('*')->from(TABLE_USER)
            ->where('dt_openid')->eq($dt_openid)
            ->andWhere('dt_persistent_code')->eq($dt_persistent_code)
            ->limit('0,1')
            ->fetch();
        return $user;
    }
}