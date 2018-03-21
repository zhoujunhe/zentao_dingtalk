<?php
$config->installed       = true;
$config->debug           = false;
$config->requestType     = 'PATH_INFO';
$config->db->host        = '127.0.0.1';
$config->db->port        = '3306';
$config->db->name        = 'zentao';
$config->db->user        = 'root';
$config->db->password    = '123456';
$config->db->prefix      = 'zt_';
$config->webRoot         = getWebRoot();
$config->default->lang   = 'zh-cn';

/* 钉钉登录配置 */
$config->ding->ddturnon = true;/* 是否开启钉钉登录 */
$config->ding->appid = '';/* 钉钉管理APPID */
$config->ding->appsecret = '';/* 钉钉密钥 */
$config->ding->redirect = 'http://xxxxxxx/www/dingtalk-login.html';/* 回调地址,与钉钉管理后台保持一致 */
