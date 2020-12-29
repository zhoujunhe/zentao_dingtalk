<?php
$config->installed       = true;
$config->debug           = true;
$config->requestType     = 'PATH_INFO';
//$config->requestType     = 'GET';
$config->timezone        = 'Asia/Shanghai';
$config->db->host        = 'localhost';
$config->db->port        = '3306';
$config->db->name        = 'zentao';
$config->db->user        = 'root';
$config->db->encoding    = 'UTF8';
$config->db->password    = '123456';
$config->db->prefix      = 'zt_';
$config->webRoot         = getWebRoot();
$config->default->lang   = 'zh-cn';
$filter->oidc=new stdclass();
$filter->oidc->index=new stdclass();
$filter->oidc->index->paramValue['scope']='reg::any';


/* 钉钉登录配置 */
$config->ding->ddturnon = true;/* 是否开启钉钉登录 */
$config->ding->logintype = 1;/* 钉钉登录方式,0仅允许绑定登录,1允许自动注册登录(建议新平台使用此方法,方便人员自行添加) */
$config->ding->appid = '';/* 钉钉扫码登录appId */
$config->ding->appsecret = '';/* 钉钉扫码登录appSecret */
$config->ding->redirect = '';/* 回调地址域名,与钉钉管理后台保持一致 */
$config->ding->inter_appkey = ' ';/* 钉钉H5微应用appkey */
$config->ding->inter_appsecret = ' ';/* 钉钉H5微应用AppSecret */
