<?php
/*
 * @Description: 
 * @version: 2.0
 * @Author: 小橙子
 * @Website: https://www.kres.cn
 * @Email: 1697779290@qq.com
 * @Date: 2019-04-24 13:26:47
 */

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class Base extends Controller
{
    public function _initialize(){
        if (!Session::has('user_id')&&!Session::has('user_name')) {
            $this->error('请登录','comm/login');
        }else{
            $config=Db::name('options')->where('name','admin_account')->find();
            if ($config['value']!=Session::get('user_id')) {
                # code...
                $this->error('无权访问','comm/login');
            }
        }
    }
}
