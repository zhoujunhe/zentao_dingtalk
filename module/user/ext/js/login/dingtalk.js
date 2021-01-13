//获取钉钉二维码
function dingtalk(){
  /*
  * 解释一下goto参数，参考以下例子：
  * var url = encodeURIComponent('http://localhost.me/index.php?test=1&aa=2');
  * var goto = encodeURIComponent('https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid=appid&response_type=code&scope=snsapi_login&state=STATE&redirect_uri='+url)
  */
  var goto = encodeURIComponent(goto_url)
  var obj = DDLogin({
      id:"login_scan",//这里需要你在自己的页面定义一个HTML标签并设置id，例如<div id="login_container"></div>或<span id="login_container"></span>
      goto: goto, //请参考注释里的方式
      style: "border:none;background-color:#F8F8F8;",
      width : "560",
      height: "300"
  });
}

//扫码后触发事件绑定
function bindMessage(){
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

$(function () {
    //生成标签
    var nodetable = document.createElement("table");
    nodetable.style="width: 560px;";
    nodetable.innerHTML = '<row>' +
                            '<td id="lable_pwd" class="select">账号密码登录</td>'+
                            '<td id="label_scan" class="unselect" style="border-left:1px solid #b3d4fc;">钉钉扫码登录</td>'+
                          '</row>';
    var login=document.getElementById("login");
    var loginPanel=document.getElementById("loginPanel");
    loginPanel.className="login";
    loginPanel.style="border-radius: 0px;";
    login.insertBefore(nodetable,loginPanel);
    //生成存放二维码容器
    var login_scan = document.createElement("div");
    login_scan.hidden=true;
    login_scan.id="login_scan";
    login_scan.className="login";
    login.insertBefore(login_scan,loginPanel);
    //绑定钉钉扫码登录标签
    document.getElementById("label_scan").onclick=function(){
      document.getElementById('login_scan').hidden=false;
      document.getElementById('loginPanel').hidden=true;
      document.getElementById('label_scan').className = "select";
      document.getElementById('lable_pwd').className = "unselect";
      dingtalk();
    }
    //绑定账号密码登录标签
    document.getElementById("lable_pwd").onclick=function(){
      document.getElementById('login_scan').hidden=true;
      document.getElementById('loginPanel').hidden=false;
      document.getElementById('label_scan').className = "unselect";
      document.getElementById('lable_pwd').className = "select";
    }
    bindMessage();
  }
)