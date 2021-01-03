<script src="https://g.alicdn.com/dingding/dinglogin/0.0.5/ddLogin.js"></script>
<script>
<?php
echo "var appid='".$config->ding->appid."';";
echo "var state='".$this->loadModel('dingtalk')->updateSessionDing()."';\n";
echo "var redirect_uri='".$config->ding->redirect.$this->createLink('dingtalk','login')."';\n";
?>
var url = encodeURIComponent(redirect_uri);
var goto_url = 'https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid='+appid+'&response_type=code&scope=snsapi_login&state='+state+'&redirect_uri='+url;
</script>

