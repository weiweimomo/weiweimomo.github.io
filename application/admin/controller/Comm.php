<?php
/*
 * @Description: 
 * @version: 2.0
 * @Author: 小橙子
 * @Website: https://www.kres.cn
 * @Email: 1697779290@qq.com
 * @Date: 2019-04-24 13:36:56
 */

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Cookie;
use PHPMailer\SendEmail;

class Comm extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }
    public function login_em(){
        switch (Request::instance()->post('func')) {
            case 'login':
                # code...
                $data=Request::instance()->post('data');
                if ($data) {
                    # code...
                    $data=json_decode($data,true);
                    if (Session::get('ec')==md5($data['code'])) {
                        # code...
                        $sqlData=Db::name('user')->where('email',$data['email'])->find();
                        Session::set('user_id',$sqlData['id']);
                        Session::set('user_name',$sqlData['username']);
                        Db::name('user')->where('username',$sqlData['username'])->setField('pass_err','0');
                        $res['code']=0;
                        $res['msg']="登录成功";
                        return json($res);
                    }else{
                        $back['code']=-1;
                        $back['msg']='验证码错误！';
                        return json($back);
                    }
                }
                break;
            case 'email_send':
                # code...
                
                if (Request::instance()->post('email')) {
                    # code...
                    $user=Db::name('user')->where('email',Request::instance()->post('email'))->find();
                    if ($user) {
                        # code...
                        $randCode=rand(100000000,999999999);
                        Session::set('ec',md5($randCode),1800);
                        $content='您正在使用一次性密码登录，密码30分钟有效，请勿转发，转发导致账号被盗！:'.$randCode;
                        $result = SendEmail::SendEmail('一次性密码登录',$content,Request::instance()->param('email','','trim'));
                        if ($result) {
                            # code...
                            $back['code']=1;
                            $back['msg']='发送成功！';
                            return json($back);
                        }
                    }else{
                        $back['code']=-1;
                        $back['msg']='找不到此用户!';
                        return json($back);
                    }
                } else {
                    # code...
                    $back['code']=-1;
                    $back['msg']='not.';
                    return json($back);
                }
                
                break;
            default:
                # code...
                return view('login_em');
                break;
        }
    }
    public function login(){
        if (Request::instance()->param('data')!='') {
            # code...
            $request=Request::instance();
            $data=$request->param('data');
            $datas=json_decode($data,true);
            $datas['password']=md5($datas['password']);
            $sqlData=Db::name('user')->where('username',$datas['username'])->find();
           if ($sqlData['pass_err']<99) {
               # code...
            if ($datas['username']==$sqlData['username']&&$datas['password']==$sqlData['password']) {
                # code...
                Session::set('user_id',$sqlData['id']);
                Session::set('user_name',$sqlData['username']);
                Db::name('user')->where('username',$datas['username'])->setField('pass_err','0');
                $res['code']=0;
                $res['msg']="登录成功";
                return json($res);
            }else{
                    $res['code']=1;
                    $res['msg']="登录失败";
                    $sqlDt=Db::name('user')->where('username',$datas['username'])->setInc('pass_err','1');
                    return json($res);
                }
           }else{
               $back['code']=-2;
               $back['msg']='错误次数过多，请通过邮箱登录！';
               return json($back);
           }
        }else{
            return view('login');
        }
    }
    public function logout(){
        Session::set('user_name',null);
        Session::set('user_id',null);
        $this->redirect('comm/login','Logout status ok!');
    }
    public function checkInfo(){
        $request=Request::instance();
        $data=$request->param('data');
        $datas=json_decode($data,true);
        $datas['password']=md5($datas['password']);
        $sqlData=Db::name('user')->where('username',$datas['username'])->find();
        if ($datas['username']==$sqlData['username']&&$datas['password']==$sqlData['password']) {
            # code...
            Session::set('user_id',$sqlData['id']);
            Session::set('user_name',$sqlData['username']);
            $res['code']=0;
            $res['msg']="登录成功";
            return json($res);
        }else{
            $res['code']=1;
            $res['msg']="登录失败";
            return json($res);
        }
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
