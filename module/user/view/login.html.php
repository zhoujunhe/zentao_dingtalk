<?php
/**
 * The html template file of login method of user module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPMS
 * @version     $Id: login.html.php 5084 2013-07-10 01:31:38Z wyd621@gmail.com $
 */
include '../../common/view/header.lite.html.php';
if(empty($config->notMd5Pwd))js::import($jsRoot . 'md5.js');
?>
<script src="https://g.alicdn.com/dingding/dinglogin/0.0.5/ddLogin.js"></script>
<script>
window.onload = function(){
<?php
echo "var appid='".$config->ding->appid."';";
echo "var state='".$this->loadModel('dingtalk')->updateSessionDing()."';";
echo "var redirect_uri='".$config->ding->redirect.$this->createLink('dingtalk','login')."';";
?>
  /*
  * 解释一下goto参数，参考以下例子：
  * var url = encodeURIComponent('http://localhost.me/index.php?test=1&aa=2');
  * var goto = encodeURIComponent('https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid=appid&response_type=code&scope=snsapi_login&state=STATE&redirect_uri='+url)
  */
  var url = encodeURIComponent(redirect_uri);
  var goto_url = 'https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid='+appid+'&response_type=code&scope=snsapi_login&state='+state+'&redirect_uri='+url;
  var goto = encodeURIComponent(goto_url)
  var obj = DDLogin({
      id:"login_scan",//这里需要你在自己的页面定义一个HTML标签并设置id，例如<div id="login_container"></div>或<span id="login_container"></span>
      goto: goto, //请参考注释里的方式
      style: "border:none;background-color:#FFFFFF;",
      width : "560",
      height: "300"
  });

  var handleMessage = function (event) {
  var origin = event.origin;
  console.log("origin", event.origin);
  if( origin == "https://login.dingtalk.com" ) { //判断是否来自ddLogin扫码事件。
    var loginTmpCode = event.data; 
    //获取到loginTmpCode后就可以在这里构造跳转链接进行跳转了
    window.location.replace(goto_url+'&loginTmpCode='+loginTmpCode);
    console.log("url", goto_url+'&loginTmpCode='+loginTmpCode);
    }
  };
  if (typeof window.addEventListener != 'undefined') {
      window.addEventListener('message', handleMessage, false);
  } else if (typeof window.attachEvent != 'undefined') {
      window.attachEvent('onmessage', handleMessage);
  }
}
function scan_onclick(val){
  login_scan.hidden=!val;
  loginPanel.hidden=val;
  document.getElementById('label_scan').className = val?"select":"unselect";
  document.getElementById('lable_pwd').className = val?"unselect":"select";
}
</script>
<style>
  .unselect{
            cursor: pointer;
            text-align: center;
            background: #FFFFFF;
            border-bottom:3px solid #FFFFFF;
            height:40px;
  }
  .select{
            cursor: pointer;
            text-align: center;
            background: #FFFFFF;
            border-bottom:3px solid #ff6a00;
            height:40px;
  }
</style>
<main id="main" class="fade no-padding">
  <div class="container" id="login">
    <table width="560px">
      <row>
        <td id="lable_pwd" onclick="scan_onclick(false);" class="select">账号密码登录</td>
        <td id="label_scan" onclick="scan_onclick(true);" class="unselect" style="border-left:1px solid #b3d4fc;">钉钉扫码登录</td>
      </row>
    </table>
    <div id="login_scan" hidden="true" style="border:none;background-color:#FFFFFF;height:305px;width:560px;"></div>
    <div id="loginPanel" style="border:none;background-color:#FFFFFF;height:305px;width:560px;border-radius: 0px;">
      <header>
        <h2><?php printf($lang->welcome, $app->company->name);?></h2>
        <div class="actions dropdown dropdown-hover" id='langs'>
          <button type='button' class='btn' title='Change Language/更换语言/更換語言'><?php echo $config->langs[$this->app->getClientLang()]; ?> <span class="caret"></span></button>
          <ul class="dropdown-menu pull-right">
            <?php foreach($config->langs as $key => $value):?>
            <li><a class="switch-lang" data-value="<?php echo $key; ?>"><?php echo $value; ?></a></li>
            <?php endforeach;?>
          </ul>
        </div>
      </header>
      <div class="table-row">
        <div class="col-4 text-center" id='logo-box'>
          <img src="<?php echo $config->webRoot . 'theme/default/images/main/' . $this->lang->logoImg;?>" />
        </div>
        <div class="col-8">
          <form method='post' target='hiddenwin'>
            <table class='table table-form'>
              <tbody>
                <tr>
                  <th><?php echo $lang->user->account;?></th>
                  <td><input class='form-control' type='text' name='account' id='account' autofocus /></td>
                </tr>
                <tr>
                  <th><?php echo $lang->user->password;?></th>
                  <td><input class='form-control' type='password' name='password' /></td>
                </tr>
                <tr>
                  <th></th>
                  <td id="keeplogin"><?php echo html::checkBox('keepLogin', $lang->user->keepLogin, $keepLogin);?></td>
                </tr>
                <tr>
                  <td></td>
                  <td class="form-actions">
                  <?php
                  echo html::submitButton($lang->login, '', 'btn btn-primary');
                  if($app->company->guest) echo html::linkButton($lang->user->asGuest, $this->createLink($config->default->module));
                  echo html::hidden('referer', $referer);
                  echo html::hidden('verifyRand', $rand);
                  echo html::a(inlink('reset'), $lang->user->resetPassword);
                  ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
        </div>
      </div>
      <?php if(isset($demoUsers)):?>
      <footer>
        <span><?php echo $lang->user->loginWithDemoUser;?></span>
        <?php
        $password = md5('123456');
        $link     = inlink('login');
        $link    .= strpos($link, '?') !== false ? '&' : '?';
        foreach($demoUsers as $demoAccount => $demoUser)
        {
            if($demoUser->password != $password) continue;
            echo html::a($link . "account={$demoAccount}&password=" . md5($password . $this->session->rand), $demoUser->realname, 'hiddenwin');
        }
        ?>
      </footer>
      <?php endif;?>
    </div>
    <div id="info" class="table-row">
      <div class="table-col text-middle text-center">
        <div id="poweredby">
          <?php if($config->checkVersion):?>
          <iframe id='updater' class='hidden' frameborder='0' width='100%' height='45' scrolling='no' allowtransparency='true' src="<?php echo $this->createLink('misc', 'checkUpdate', "sn=$s");?>"></iframe>
          <?php endif;?>
        </div>
      </div>
    </div>
  </div>
</main>
<?php include '../../common/view/footer.lite.html.php';?>
