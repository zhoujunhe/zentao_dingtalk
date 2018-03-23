<?php

class dingtalk extends control
{

    /* 钉钉登录 */
    public function login(){

        if(isset($_GET['code']) and isset($_GET['state'])){
            if($this->get->state)   $state  = $this->get->state;
            if($this->get->code)   $code  = $this->get->code;

            if(empty($code)) $this->dingtalk->errjump('/www/user-login.html','用户临时码code不能为空');
            if(empty($state)) $this->dingtalk->errjump('/www/user-login.html','状态参数state不能为空');

            $statesess = $this->session->ding_rand;
            if($statesess != $state) $this->dingtalk->errjump('/www/user-login.html','状态参数state过期');

            $usercode = $this->dingtalk->getDingtalkUserToken($code); /* 获取用户的持久授权码 */
            if($usercode['errcode']!=0) $this->dingtalk->errjump('/www/user-login.html',$usercode['errmsg']);
            $this->dingtalk->updateSessionDing(true); /* 销毁session的钉钉ding_state */

            /* 用户是否已登录,如果已登录,则直接进行绑定 */
            if($this->session->user !== false){

                /* 获取钉钉用户信息 */
                $usersns = $this->dingtalk->getDingtalkUserSNS($usercode['openid'],$usercode['persistent_code']); /* 获取用户的SNS授权码 */
                if($usersns['errcode']!=0) $this->dingtalk->errjump('/www/user-login.html',$usersns['errmsg']);

                $userinfo = $this->dingtalk->getDingtalkUserInfo($usersns['sns_token']); /* 获取用户的基本信息 */
                if($userinfo['errcode']!=0) $this->dingtalk->errjump('/www/user-login.html',$userinfo['errmsg']);

                /* 构建钉钉信息数据 */
                $data = [
                    'dt_openid'=>$usercode['openid'],
                    'dt_unionid'=>$usercode['unionid'],
                    'dt_persistent_code'=>$usercode['persistent_code'],
                    'dt_dingId'=>$userinfo['user_info']['dingId'],
                    'dt_nick'=>$userinfo['user_info']['nick']
                ];

                /* 绑定钉钉用户 */
                if($this->dingtalk->bindDingtalkUser($this->session->user->account,$data)===false){
                    $this->dingtalk->errjump('/www/user-login.html','绑定失败');
                }else{
                    die(js::locate($this->createLink($this->config->default->module), 'parent'));
                }
            }

            /* 检查钉钉用户登录 */
            $user = $this->dingtalk->checkDingtalkUser($usercode['openid'],$usercode['persistent_code']);

            /* 若允许钉钉自动注册账号且钉钉用户不存在,则获取钉钉用户信息并注册 */
            if($user===false && $this->config->ding->logintype){
                $usersns = $this->dingtalk->getDingtalkUserSNS($usercode['openid'],$usercode['persistent_code']); /* 获取用户的SNS授权码 */
                if($usersns['errcode']!=0) $this->dingtalk->errjump('/www/user-login.html',$usersns['errmsg']);

                $userinfo = $this->dingtalk->getDingtalkUserInfo($usersns['sns_token']); /* 获取用户的基本信息 */
                if($userinfo['errcode']!=0) $this->dingtalk->errjump('/www/user-login.html',$userinfo['errmsg']);

                /* 构建钉钉信息数据 */
                $data = [
                    'dt_openid'=>$usercode['openid'],
                    'dt_unionid'=>$usercode['unionid'],
                    'dt_persistent_code'=>$usercode['persistent_code'],
                    'dt_dingId'=>$userinfo['user_info']['dingId'],
                    'dt_nick'=>$userinfo['user_info']['nick']
                ];

                /* 用户注册 */
                $this->dingtalk->regDingtalkUser($data);

                /* 重新获取钉钉用户信息 */
                $user = $this->dingtalk->checkDingtalkUser($usercode['openid'],$usercode['persistent_code']);

            }

            if($user){
                $account = $user->account;
                $this->user = $this->loadModel('user');

                $this->user->cleanLocked($account);
                $user->rights = $this->user->authorize($account);
                $user->groups = $this->user->getGroups($account);
                $this->session->set('user', $user);
                $this->app->user = $this->session->user;
                $this->loadModel('action')->create('user', $user->id, 'login');
                $this->loadModel('score')->create('user', 'login');
                /* Keep login. */
                if($this->post->keepLogin) $this->user->keepLogin($user);

                die(js::locate($this->createLink($this->config->default->module), 'parent'));

            }else{
                $this->dingtalk->errjump('/www/user-login.html','登录失败,用户不存在');
            }

        }else{
            $this->dingtalk->errjump('/www/user-login.html','缺少钉钉参数');
        }

    }

}