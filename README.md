# apidoc
api文档管理系统

基于thinkphp5.0.9

# 安装方法
## 1.git clone
https://github.com/sundyandy/apidoc
或
http://git.oschina.net/sundyandy/apidoc （定期同步）

## 2.安装composer，并修改镜像源
https://pkg.phpcomposer.com

## 3.在根目录执行composer install

## 4.修改数据库等配置文件
- \application\database.php 修改数据库配置
- \application\index\config.php 修改imageUrl节点

## 5.运行migtate进行数据库初始化
php think migrate:run

## 6.如有需要，可指定域名(nginx/apache)