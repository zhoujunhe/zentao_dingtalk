## 扩展说明：为禅道源码版9.8.1 添加钉钉三方登录方式。
### 使用方式：导入数据库文件，然后将文件直接复制覆盖到禅道源码版里并即可。
### 最新改动：钉钉注册及登录方式设置,在my.php里的logintype参数可以设置为【0仅允许绑定过钉钉的账号扫码登录, 1允许钉钉扫码注册并登录(原版模式,推荐新平台搭建注册人员使用方便)】


### 更改说明：

#### 一、数据库更改：
* 数据库执行zentao_dt_mysql.sql文件，为数据库用户表zt_user添加钉钉用户数据字段


#### 二、模块增加：
* 将/module/dingtalk/ 下的文件复制到 禅道的/module/dingtalk/目录下,如果目录不存在则新建


#### 三、配置增加：
* /config/filter.php 最后两行增加了dingtalk模块的参数过滤
* /config/my.php 最后增加了钉钉参数配置, 请将配置里的钉钉参数修改为你的钉钉参数


#### 四、文件更改：
* /module/user/view/login.html.php 增加了判断开启钉钉登录按钮的1行代码
* /module/my/view/profile.html.php 增加了绑定钉钉按钮的1行代码
* /module/my/view/changepassword.html.php 增加了提示初始密码为123456的1行代码
* /module/user/lang/ 下的语言包文件添加了钉钉按钮文字显示内容 $lang->user->dingBtn 和 $lang->user->dingid
* /module/common/model.php 里的isOpenMethod方法添加钉钉登录开放