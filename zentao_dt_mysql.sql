ALTER TABLE `zt_user`
ADD COLUMN `dt_openid`  varchar(80) NOT NULL DEFAULT '' COMMENT '钉钉用户应用唯一标识' AFTER `ranzhi`,
ADD COLUMN `dt_unionid`  varchar(80) NOT NULL DEFAULT '' COMMENT '钉钉用户唯一ID' AFTER `dt_openid`,
ADD COLUMN `dt_persistent_code`  varchar(200) NOT NULL DEFAULT '' COMMENT '钉钉用户持久授权码' AFTER `dt_unionid`,
ADD COLUMN `dt_dingid`  varchar(80) NOT NULL DEFAULT '' COMMENT '钉钉ID' AFTER `dt_persistent_code`,
ADD COLUMN `dt_nick`  varchar(50) NOT NULL DEFAULT '' COMMENT '钉钉用户昵称' AFTER `dt_dingid`,
ADD INDEX `dt_openid` (`dt_openid`) USING BTREE ,
ADD INDEX `dt_unionid` (`dt_unionid`) USING BTREE ,
ADD INDEX `dt_persistent_code` (`dt_persistent_code`) USING BTREE ,
ADD INDEX `dt_dingid` (`dt_dingid`) USING BTREE ;