<?php
/*
 * @Description: 
 * @version: 2.0
 * @Author: 小橙子
 * @Date: 2019-05-16 14:31:30
 * @Website: https://www.kres.cn
 * @Email: 1697779290@qq.com
 */
namespace app\index\controller;

use think\Controller;
use app\commmon\SendEmail;
use think\Request;
use think\Db;
use think\Cookie;

class School extends Controller
{
    public function indexPage()
    {
        $request=Request::instance();
        $data=Db::name('website')->where('zid',$request->param('zid'))->find();
        if($data=="" || $data['status']==0){
            return view('msg');
        }
        Db::name('website')->where('zid',$request->param('zid'))->setInc('hot',1);
        $data['ismobile']=$request->isMobile();
        $data['system']['icp']=$this->getOptions('icp');
        $data['system']['cnzz']=$this->getOptions('cnzz');
        $data['system']['title']=$data['web_name'];
        $data['system']['keywords']=$this->getOptions('keywords');
        $data['system']['description']=$this->getOptions('description');
        $Contenes=Db::name('contents');
        $map['zid'] = $request->param('zid');
        if (Request::instance()->get('content')) {
            # code...
            $indexContent=$Contenes->where($map)->where('text','LIKE','%'.Request::instance()->param('content','','trim').'%')->order('date desc')->paginate(9);
            $page='';
        }else{
            $indexContent=$Contenes->where($map)->order('date desc')->paginate(9);
            $page=$indexContent->render();
        }
        return view('index',[
            'indexContent'=>$indexContent,
            'page'=>$page,
            'data'=>$data,
        ]);
    }
    public function getOptions($name=''){
        $res=Db::name('options')->where('name',$name)->find();
        return $res['value'];
    }
	public function getbarrager(){
        $request=Request::instance();
        $postId=$request->param('id');
        $map['post_id']=$postId;
        $res=array();
        $arr = Db::name('barrager')->where('post_id',$postId)->select();
        if ($arr) {
            # code...
            return json($arr[array_rand($arr)]);
        }else{
            return null;
        }
        //print_r($value);
    }
    public function sendbar(){
        /**
         * @name: 安全过滤
         * @test: 
         * @msg: 
         * @param {type} 
         * @return: 
         */
        $webScan = new \app\common\controller\Safe();
        //显示禁止页面
        if ($webScan->isAttack()) {
          if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || ($_SERVER['HTTP_AJAX'])) {
            header("content-type:application/json");
            $data = array(
              'code' => 0,
              'info' => '您的请求带有不合法参数，已被网站管理员设置拦截！' . $_SERVER['REQUEST_URI']
            );
            echo json_encode($data);
            exit();
          }
          $webScan->showStopPage();
        };
        Request::instance()->filter(['strip_tags','htmlspecialchars']);
        $request=Request::instance();
        $data=$request->post();
        $getqqinfo=file_get_contents('https://api.kres.cn/getqqnickname?qq='.$data['qq']);
        $getqqinfo=json_decode($getqqinfo,true);
        unset($data['qq']);
        $data['img']=$getqqinfo['image'];
        if (empty($request->post())) {
            # code...
            $back['code']=-1;
            $back['info']='发送失败';
            return json($back);
        }else{
            //插入数据
            $dataDb=Db::name('barrager')->insert($data);
            if ($dataDb) {
                # code...
                $back['code']=1;
                $back['info']='发送成功';
                return json($back);
            }
        }

    }
  	public function commPage(){
        //查询评论
        $post_id=Request::instance()->param('id');
        $num =  Db::name('comment')->where('post_id',$post_id)->count(); //获取评论总数
        $data['web']=Db::name('website')->where('zid',Request::instance()->param('zid'))->find();
        if($data['web']=="" || $data['web']['status']==0){
            return view('msg');
        }
        $data['ismobile']=Request::instance()->isMobile();
        $data['system']['icp']=$this->getOptions('icp');
        $data['system']['cnzz']=$this->getOptions('cnzz');
        $data['system']['keywords']=$this->getOptions('keywords');
        $data['system']['description']=$this->getOptions('description');
        $postdata=Db::name('contents')->where('id',$post_id)->find();
        $data['list']=array();
        $data['list']=$this->getCommlist($post_id);//获取评论
        return view('comment',[
            'commentlist'=>$data,
            'num'=>$num,
            'postdata'=>$postdata,
            'post_id'=>$post_id,
            'data'=>$data,
        ]);

    }
  
  	protected function getCommlist($post_id = 0,$parent_id = 0,&$result = array()){  
        $map['parent_id']=$parent_id;
        $map['post_id']=$post_id;
        $arr = Db::name('comment')->where($map)->order("create_time desc")->select();   
        if(empty($arr)){
            return array();
        }
        foreach ($arr as $cm) {  
            $thisArr=&$result[];
            $cm["children"] = $this->getCommlist($post_id,$cm["id"],$thisArr);    
            $thisArr = $cm;                                   
        }
        return $result;
    }
    public function addComment(){
        /**
         * @name: 
         * @test: 安全过滤
         * @msg: 
         * @param {type} 
         * @return: 
         */
        $webScan = new \app\common\controller\Safe();
        //显示禁止页面
        if ($webScan->isAttack()) {
          if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || ($_SERVER['HTTP_AJAX'])) {
            header("content-type:application/json");
            $data = array(
              'code' => 0,
              'info' => '您的请求带有不合法参数，已被网站管理员设置拦截！' . $_SERVER['REQUEST_URI']
            );
            echo json_encode($data);
            exit();
          }
          $webScan->showStopPage();
        };
        Request::instance()->filter(['strip_tags','htmlspecialchars']);
        $request=Request::instance();
        if($request->param('comment')!=''){
            $insert_data=$request->param();
            Db::name('contents')->where('id',$insert_data['post_id'])->setInc('comm',1);
            $cm['create_time']=date('Y-m-d H:i:s',time());
            $cm['post_id']=$insert_data['post_id'];
            $cm['content']=$insert_data['content'];
            if(Session::get('username')==""){
                $cm['nickname']="游客";
                $cm['head_pic']="/static/img/mous.jpg";
            }
            Db::name('comment')->insert($cm);
            $data['code']=1;
            $data['msg']="biu~已送达~";
            return json($data);
        }else{
            $data["error"] = "0";
        }
    }
    public function realPage()
    {
        $request=Request::instance();
        $Contenes=Db::name('contents');
        $map['zid']=$request->param('zid');
        $map['mous']=0;
        $data=Db::name('website')->where('zid',Request::instance()->param('zid'))->find();
        if($data=="" || $data['status']==0){
            return view('msg');
        }
        $data['ismobile']=Request::instance()->isMobile();
        $data['system']['icp']=$this->getOptions('icp');
        $data['system']['cnzz']=$this->getOptions('cnzz');
        $data['system']['title']=$data['web_name'];
        $data['system']['keywords']=$this->getOptions('keywords');
        $data['system']['description']=$this->getOptions('description');
        if (Request::instance()->get('content')) {
            # code...
            $indexContent=$Contenes->where($map)->where('text','LIKE','%'.Request::instance()->param('content','','trim').'%')->order('date desc')->paginate(9);
            $page='';
        }else{
            $indexContent=$Contenes->where($map)->order('date desc')->paginate(9);
            $page=$indexContent->render();
        }
        return view('real',[
            'indexContent'=>$indexContent,
            'page'=>$page,
            'data'=>$data,
        ]);
    }

    public function mousPage()
    {
        $request=Request::instance();
        $Contenes=Db::name('contents');
        $map['zid']=$request->param('zid');
        $map['mous']=1;
        $data=Db::name('website')->where('zid',Request::instance()->param('zid'))->find();
        if($data=="" || $data['status']==0){
            return view('msg');
        }
        $data['ismobile']=Request::instance()->isMobile();
        $data['system']['icp']=$this->getOptions('icp');
        $data['system']['cnzz']=$this->getOptions('cnzz');
        $data['system']['title']=$data['web_name'];
        $data['system']['keywords']=$this->getOptions('keywords');
        $data['system']['description']=$this->getOptions('description');
        $map['zid'] = $request->param('zid');
        if (Request::instance()->get('content')) {
            # code...
            $indexContent=$Contenes->where($map)->where('text','LIKE','%'.Request::instance()->param('content','','trim').'%')->order('date desc')->paginate(9);
            $page='';
        }else{
            $indexContent=$Contenes->where($map)->order('date desc')->paginate(9);
            $page=$indexContent->render();
        }
        return view('mous',[
            'indexContent'=>$indexContent,
            'page'=>$page,
            'data'=>$data,
        ]);
    }

    public function callpage()
    {
        $request=Request::instance();
        $data=Db::name('website')->where('zid',Request::instance()->param('zid'))->find();
        $data['ismobile']=Request::instance()->isMobile();
        $data['system']['icp']=$this->getOptions('icp');
        $data['system']['cnzz']=$this->getOptions('cnzz');
        $data['system']['title']=$data['web_name'];
        $data['system']['keywords']=$this->getOptions('keywords');
        $data['system']['description']=$this->getOptions('description');
        if($data=="" || $data['status']==0){
            return view('msg');
        }else {
            Cookie::set('zid',$request->param('zid'),30000);
            $indexData['ismobile']=$request->isMobile();
            return view('call',[
                'data'=>$data,
            ]);
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
