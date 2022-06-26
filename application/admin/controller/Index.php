<?php
/*
 * @Description: 
 * @version: 2.0
 * @Author: 小橙子
 * @Website: https://www.kres.cn
 * @Email: 1697779290@qq.com
 * @Date: 2019-04-24 13:26:23
 */

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use PHPMailer\SendEmail;

class index extends Base
{
    public function index()
    {
        return view('index');
    }
        public function sys_set_basic(){
        if (Request::instance()->post('data')) {
            # code...
            $data=Request::instance()->post('data');
            $data=json_decode($data,true);
            $this->setConfig('name',$data['name']);
            $this->setConfig('title',$data['title']);
            $this->setConfig('keywords',$data['keywords']);
            $this->setConfig('description',$data['description']);
            $this->setConfig('page_about',$data['page_about']);
            $this->setConfig('qq',$data['qq']);
            $this->setConfig('cnzz',$data['cnzz']);
            $this->setConfig('icp',$data['icp']);
            $this->setConfig('admin_account',$data['admin_account']);
            $back['code']=1;
            $back['info']='修改成功！';
            return json($back);
        } else {
            # code...
            $data['name']=$this->getConfig('name')['value'];
            $data['title']=$this->getConfig('title')['value'];
            $data['keywords']=$this->getConfig('keywords')['value'];
            $data['description']=$this->getConfig('description')['value'];
            $data['page_about']=$this->getConfig('page_about')['value'];
            $data['cnzz']=$this->getConfig('cnzz')['value'];
            $data['icp']=$this->getConfig('icp')['value'];
            $data['qq']=$this->getConfig('qq')['value'];
            $data['admin_account']=$this->getConfig('admin_account')['value'];
            $data['user']['list']=Db::name('user')->select();
            return view('sys_set_basic',[
                'data'=>$data,
            ]);
        }
    }
    public function schoolList(){
        $data['list']=Db::name('website')->paginate(15);
        $data['page']=$data['list']->render();
        $request=Request::instance();
        if($request->param('zid')!=''){
            $res=Db::name('website')->where('zid',$request->param('zid'))->setField($request->param('ziduan'),$request->param('ziduan_value'));
            if ($res!=0) {
                # code...
                $ajaxReturn['code']=0;
                $ajaxReturn['msg']='操作成功';
                return json($ajaxReturn);
            }else{
                $ajaxReturn['code']=1;
                $ajaxReturn['msg']='操作失败';
                return json($ajaxReturn);
            }
        }else{
            return view('schoollist',[
                'data'=>$data,
            ]);

        }
    }
    public function schoolAdd($name,$qq,$description,$logo){
        $chars = md5(uniqid(mt_rand(), true));
        $uuid  = substr($chars,0,8) . '-';
        $uuid .= substr($chars,8,4) . '-';
        $uuid .= substr($chars,12,4) . '-';
        $uuid .= substr($chars,16,4) . '-';
        $uuid .= substr($chars,20,12);

        $data['zid']=$uuid;
        $data['web_name']=$name;
        $data['web_qq']=$qq;
        $data['web_description']=$description;
        $data['web_logo']=$logo;
        $insertRes=Db::name('website')->insert($data);
        if ($insertRes) {
            # code...
            return $data['zid'];
        }
        
    }
    public function schoolExamine(){
        $data['list']=Db::name('open_school')->paginate(15);
        $data['page']=$data['list']->render();
        return view('school_examine',[
            'data'=>$data,
        ]);
    }
    public function examineResult(){
        switch (Request::instance()->post('func')) {
            case 'Reject':
                # code...
                $data=Request::instance()->post('data');
                $data=json_decode($data,true);
                //取出数据库内容
                $sqlData=Db::name('open_school')->where('id',$data['id'])->find();
                Db::name('open_school')->where('id',$data['id'])->setField('status',2);
                $result = SendEmail::SendEmail('很抱歉！表白墙审核未通过',$data['content'].'<br>请更正或检查后再次提交，感谢您的使用！',$sqlData['email']);
                if ($result) {
                    # code...
                    $back['code']=1;
                    $back['info']='已驳回';
                    return json($back);
                }
                break;
            case 'Adopt':
                # code...
                $data=Request::instance()->post('data');
                $data=json_decode($data,true);
                //取出数据库内容
                $sqlData=Db::name('open_school')->where('id',$data['id'])->find();
                $addSchool=$this->schoolAdd($sqlData['web_name'],$sqlData['email'],$sqlData['description'],$sqlData['logo_img']);
                if ($addSchool!='') {
                    # code...
                    //更新状态
                    Db::name('open_school')->where('id',$data['id'])->setField('status',1);
                    $result = SendEmail::SendEmail('恭喜！表白墙审核通过',$data['content'].'<br>站点ID:'.$addSchool,$sqlData['email']);
                    if ($result) {
                        # code...
                        $back['code']=1;
                        $back['info']='已分配ID:'.$addSchool;
                        return json($back);
                    }
                }else{
                    $back['code']=-1;
                    $back['info']='操作失败';
                    return json($back);
                }
                break;
            default:
                # code...
                $res=Db::name('open_school')->where('id',Request::instance()->post('id'))->delete();
                if ($res) {
                    # code...
                    $back['code']=1;
                    $back['info']='已删除';
                    return json($back);
                }else{
                    $back['code']-1;
                    $back['info']='删除失败！';
                    return json($back);
                }
                break;
        }
    }
    public function examineInfo(){
        $data=Db::name('open_school')->where('id',Request::instance()->get('id'))->find();
        return view('examine_info',['data'=>$data,]);
    }
    public function schoolOption(){
        $func=Request::instance()->post('func');
        switch ($func) {
            case 'save':
                # code...
                $data=Request::instance()->post('data');
                $data=json_decode($data,true);
                unset($data['file']);
                $res=Db::name('website')->where('zid',$data['zid'])->update($data);
                if ($res) {
                    # code...
                    $back['code']=1;
                    $back['info']="更新成功！";
                    return json($back);
                } else {
                    # code...
                    $back['code']=1;
                    $back['info']="更新成功！";
                    return json($back);
                }
                
                break;
            case 'del':
                # code...
                $data=Request::instance()->post('id');
                $res=Db::name('website')->where('id',$data)->delete();
                if ($res) {
                    # code...
                    $back['code']=1;
                    $back['info']="删除成功！";
                    return json($back);
                } else {
                    # code...
                    $back['code']=1;
                    $back['info']="删除成功！";
                    return json($back);
                }
                
                break;
            case 'add':
                # code...
                $data=Request::instance()->post('data');
                $data=json_decode($data,true);
                unset($data['file']);
                $statusReturn=$this->schoolAdd($data['web_name'],$data['web_qq'],$data['web_description'],$data['web_logo']);
                if ($statusReturn!='') {
                    # code...
                    $back['code']=1;
                    $back['info']="开通成功：".$statusReturn;
                    return json($back);
                } else {
                    # code...
                    $back['code']=-1;
                    $back['info']="操作失败";
                    return json($back);
                }
                
                break;
            default:
                # code...

                break;
        }
    }
    public function schoolEdit(){
        $data=Db::name('website')->where('zid',Request::instance()->param('zid'))->find();
        return view('school_edit',[
            'data'=>$data,
        ]);
    }
    public function userOption(){
        $func=Request::instance()->post('func');
        switch ($func) {
            case 'add':
                $data=Request::instance()->post('data');
                $data=json_decode($data,true);
                $sql=Db::name('user')->where('username',$data['username'])->find();
                if($sql){
                    $back['code']=-1;
                    $back['info']="用户名重复";
                    return json($back);
                    exit();
                }
                $data['password']=md5($data['password']);
                $res=Db::name('user')->insert($data);
                # code...
                if ($res) {
                    # code...
                    $back['code']=1;
                    $back['info']="新增成功！";
                    return json($back);

                }else{
                    $back['code']=-1;
                    $back['info']="新增失败！";
                    return json($back);
                }
                
                break;
            
            default:
                # code...

                $res=Db::name('user')->where('id',Request::instance()->post('id',Session::get('user_id'),'trim'))->delete();
                if ($res) {
                    # code...
                    $back['code']=1;
                    $back['info']="删除成功！";
                    return json($back);

                }else{
                    $back['code']=-1;
                    $back['info']="删除失败！";
                    return json($back);
                }
                break;
        }
    }
    public function userAdd(){
        return view('user_add');
    }
    public function schoolAddPage(){
        return view('school_add');
    }
    public function userList(){
        if (Request::instance()->get('username')!='') {
            # code...
            $data['user']['list']=Db::name('user')->where('username','LIKE','%'.Request::instance()->get('username').'%')->select();
            $data['page']='';
        }else{
            $data['user']['list']=Db::name('user')->paginate(15);
            $data['page']=$data['user']['list']->render();
        }
        return view('user_list',[
            'data'=>$data,
        ]);
    }
    public function noticeAjaxAdd(){
        $data['tid']=uniqid(microtime());
        $data['time']=time();
        $data['content']=Request::instance()->post('content');
        $db=Db::name('notice')->insert($data);
        if ($db) {
            # code...
            $back['code']=1;
            $back['info']='发布成功！';
            return json($back);
        } else {
            # code...
            $back['code']=-1;
            $back['info']='发布失败！';
            return json($back);
        }
    }

    public function noticeShow(){
        $Db=Db::name('notice')->where('tid',Request::instance()->get('tid'))->find();
        return $Db['content'];
    }
    public function noticeList(){
        if (Request::instance()->post('func')=='del') {
            # code...
            $res=Db::name('notice')->where('id',Request::instance()->post('id'))->delete();
            if ($res) {
                # code...
                $back['code']=1;
                $back['info']="删除成功！";
                return json($back);
            }else{
                $back['code']=-1;
                $back['info']="删除失败！";
                return json($back);
            }
        }else if(Request::instance()->param('func')=='add'){
            return view('noticeAdd');
        }else{
            if (Request::instance()->get('start')&&Request::instance()->get('end')) {
                # code...
                $data['list']=Db::name('notice')->whereTime('time','between',[strtotime(Request::instance()->get('start')),strtotime(Request::instance()->get('end'))])->order('time','desc')->paginate('10');
            }else{
                $data['list']=Db::name('notice')->order('time','desc')->paginate('10');
            }
            $data['page']=$data['list']->render();
            return view('noticeList',[
                'data'=>$data,
            ]);
            
        }
    }
    public function schoolSwitch(){
        if(Request::instance()->post()){
            if (Request::instance()->post('type')!=''&&Request::instance()->post('value')!='') {
                # code...
                $res=Db::name('web_switch')->where('zid',Request::instance()->post('zid'))->setField(Request::instance()->param('type'),Request::instance()->param('value'));
                if($res){
                    $returnInfo['code']=1;
                    $returnInfo['data']="修改成功";
                    return json($returnInfo);
                }else {
                    $returnInfo['code']=0;
                    $returnInfo['data']="修改失败";
                    return json($returnInfo);
                }
            }

        }else{
            $dataInfo=Db::name('web_switch')->where('zid',Request::instance()->param('zid'))->find();
            return view('school_switch',[
                'data'=>$dataInfo,
            ]);
        }
    }
    public function welcome(){
        //检查更新
        //当前版本
        $appVersion=20190525;
        //获取远程文件
        // $appServer=file_get_contents('https://pro.kres.cn/update/get/appVersion?appid=10002');
        // $appServer=json_decode($appServer,true);
        $data['newVersion']['status']=0;
        $data['newVersion']['info']='';
        // if ($appServer['version']!=$appVersion) {
        //     # code...
        //     $data['newVersion']['info']=$appServer['info'].'<br>'.$appServer['version'];
        //     $data['newVersion']['status']=1;
        // }
        $data['commNum']=$this->countTableNum('comment');
        $data['postNum']=$this->countTableNum('contents');
        $data['userNum']=$this->countTableNum('user');
        $data['noticeNum']=$this->countTableNum('notice');
        $data['schoolNum']=$this->countTableNum('website');
        $data['barragerNum']=$this->countTableNum('barrager');
        $data['dateTime']=date('Y-m-d H:i:s');
        $data['user_name']=Session::get('user_name');
        return view('welcome',[
            'data'=>$data,
        ]);
    }
    public function contentAdd(){
        $data['school']['list']=Db::name('website')->select();
        if(Request::instance()->post('data')){
            $data['form']=Request::instance()->post('data');
            $data['form']=json_decode($data['form'],true);
            $data['form']['ip']=Request::instance()->ip();
            $data['form']['date']=time();
            $data['sql']['sqlres']=Db::name('contents')->insert($data['form']);
            Db::name('website')->where('zid',$data['form']['zid'])->setInc('count',1);
            if ($data['sql']['sqlres']) {
                # code...
                $back['code']=1;
                $back['info']='添加成功！';
                return json($back);
            }else{
                $back['code']=-1;
                $back['info']='添加失败！';
                return json($back);
            }
        }else{
            return view('content_add',[
                'data'=>$data,
            ]);
        }
    }
    public function websetting(){
        if (Request::instance()->post('data')) {
            # code...
            $data=Request::instance()->post('data');
            $data=json_decode($data,true);
            $this->setConfig('name',$data['name']);
            $this->setConfig('title',$data['title']);
            $this->setConfig('keywords',$data['keywords']);
            $this->setConfig('description',$data['description']);
            $this->setConfig('limit_mous',isset($data['limit_mous'])?'1':'0');
            $this->setConfig('limit_time',$data['limit_time']);
            $this->setConfig('limit_ip',$data['limit_ip']);
            $this->setConfig('page_about',$data['page_about']);
            $this->setConfig('qq',$data['qq']);
            $this->setConfig('cnzz',$data['cnzz']);
            $this->setConfig('icp',$data['icp']);
            $this->setConfig('admin_account',$data['admin_account']);

            $back['code']=1;
            $back['info']='修改成功！';
            return json($back);
        } else {
            # code...
            $data['name']=$this->getConfig('name')['value'];
            $data['title']=$this->getConfig('title')['value'];
            $data['keywords']=$this->getConfig('keywords')['value'];
            $data['description']=$this->getConfig('description')['value'];
            $data['mous']=$this->getConfig('limit_mous')['value'];
            $data['limitime']=$this->getConfig('limit_time')['value'];
            $data['limitip']=$this->getConfig('limit_ip')['value'];
            $data['page_about']=$this->getConfig('page_about')['value'];
            $data['cnzz']=$this->getConfig('cnzz')['value'];
            $data['icp']=$this->getConfig('icp')['value'];
            $data['qq']=$this->getConfig('qq')['value'];
            $data['admin_account']=$this->getConfig('admin_account')['value'];
            $data['user']['list']=Db::name('user')->select();
            return view('system_setting',[
                'data'=>$data,
            ]);
        }
        
    }
    public function userView(){
        if (Request::instance()->get('id')) {
            # code...
            $data=Db::name('user')->where('id',Request::instance()->get('id'))->find();
            return view('user_view',[
                'data'=>$data,
            ]);
        } else {
            # code...
            return $this->error('无法显示文章内容');
        }
    }

    public function contentView(){

        if (Request::instance()->get('id')) {
            # code...
            $data=Db::name('contents')->where('id',Request::instance()->get('id'))->find();
            return view('content_view',[
                'data'=>$data,
            ]);
        } else {
            # code...
            return $this->error('无法显示文章内容');
        }
        
    }

    public function upPassword(){
        $id=Request::instance()->post('id',Session::get('user_id','trim'));
        $password=Request::instance()->post('password','','md5');
        $res=Db::name('user')->where('id',$id)->setField('password',$password);
        if($res){
            $back['code']=1;
            $back['info']="修改成功！";
            return json($back);
        }else{
            $back['code']=-1;
            $back['info']="修改失败！";
            return json($back);
        }
    }

    public function contentList(){
        if (Request::instance()->get('content')!='') {
            # code...
            $func='content';
        }elseif (Request::instance()->get('content')!=''&&Request::instance()->get('start')!=''&&Request::instance()->get('end')!='') {
            # code...
            $func='cttime';
        }else if(Request::instance()->get('start')!='') {
            
            $func='time';
        }
        else{
            $func='';
        }
        switch ($func) {
            case 'content':
                # code...
                $data['content']=Db::name('contents')->order('date','desc')->where('text','LIKE','%'.Request::instance()->get('content').'%')->paginate(10);
                break;
            case 'time':
                # code...
                $data['content']=Db::name('contents')->order('date','desc')->whereTime('date','between',[strtotime(Request::instance()->get('start')),strtotime(Request::instance()->get('end'))])->paginate(10);
                break;
            case 'cttime':
                $data['content']=Db::name('contents')->order('date','desc')->where('text','LIKE','%'.Request::instance()->get('content'))->whereTime('date','between',[strtotime(Request::instance()->get('start')),strtotime(Request::instance()->get('end'))])->paginate(10);

                # code...
                break;
            default:
                # code...
                $data['content']=Db::name('contents')->order('date','desc')->paginate(10);
                break;
        }
        $data['page']=$data['content']->render();
        return view('content_list',[
            'data'=>$data,
        ]);
    }
    public function countTableNum($tableName){
        return Db::name($tableName)->count();
    }
    public function deleteContent(){
        $res=Db::name('contents')->where('id',Request::instance()->post('id'))->delete(true);
        if ($res) {
            # code...
            $back['code']=1;
            $back['info']="删除成功！";
            return json($back);
        }else{
            $back['code']=-1;
            $back['info']="删除失败！";
            return json($back);
        }
    }
    public function setConfig($name,$value){
        $res=Db::name('options')->where('name',$name)->setField('value',$value);
        if ($res) {
            # code...
            return true;
        } else {
            # code...
            return false;
        }
        
    }
    public function getConfig($name){
        return Db::name('options')->where('name',$name)->find();
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
