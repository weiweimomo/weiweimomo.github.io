<?php
/*
 * @Description: 
 * @version: 2.0
 * @Author: 小橙子
 * @Website: https://www.kres.cn
 * @Email: 1697779290@qq.com
 * @Date: 2019-04-23 21:57:05
 */
namespace app\index\controller;
use think\Request;
use think\Db;
use think\Controller;
use think\Session;
use think\Cookie;

class Index extends Controller
{
    public function index(){
        $request=Request::instance();
        $pageContenes=Db::name('website');
        $data['system']['icp']=$this->getOptions('icp');
        $data['system']['cnzz']=$this->getOptions('cnzz');
        $data['system']['name']=$this->getOptions('name');
        $data['system']['title']='关于'.$this->getOptions('title');
        $data['system']['keywords']=$this->getOptions('keywords');
        $data['system']['description']=$this->getOptions('description');
        $data['system']['title']=$this->getOptions('title');
        $data['ismobile']=$request->isMobile();
        $result=$pageContenes->paginate(9);
        $pages=$result->render();
        return	view('index',[
            'data'=>$data,
            'indexContent'=>$result,
            'page'=>$pages,
        ]);
    }
  
  public function http_request($url, $data = null)  
  {  
      $curl = curl_init();  
      curl_setopt($curl, CURLOPT_URL, $url);  
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);  
      if (! empty($data)) {  
          curl_setopt($curl, CURLOPT_POST, 1);  
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
      }  
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);  
      $output = curl_exec($curl);  
      curl_close($curl);  
      return $output;  
  } 
	public function upload(){
    $file = request()->file('file');    
    // 移动到框架应用根目录/public/uploads/ 目录下
    if($file){
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            // echo $info->getExtension();
            // // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            // echo $info->getSaveName();
            // // 输出 42a79759f284b767dfcb2a0197904287.jpg
            // echo $info->getFilename(); 
            $data['code']=0;
            $data['data']['src']='//'.$_SERVER['SERVER_NAME'].'/uploads/'.$info->getSaveName();
            return json($data);
        }else{
            // 上传失败获取错误信息
            $data['code']=-1;
            $data['msg']=$file->getError();
            return json($data);
        }
    }
	}
    public function openSchool(){
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
        //Request::instance()->filter(['strip_tags','htmlspecialchars']);
      	if(Request::instance()->post('data')){
            $data=Request::instance()->post('data');
            $data=json_decode($data,true);
            unset($data['file']);
            $data['ip']=Request::instance()->ip();
            $data['addtime']=date('Y-m-d H:i:s');
            $res=Db::name('open_school')->insert($data);
          	if($res==''){
              	$back['code']=-1;
              	$back['info']='失败';
            	return json($back);
            }else{
              	$back['code']=1;
              	$back['info']='成功';
            	return json($back);
            }
        }else{
            $data['system']['icp']=$this->getOptions('icp');
            $data['system']['cnzz']=$this->getOptions('cnzz');
            $data['system']['name']=$this->getOptions('name');
            $data['system']['title']='关于'.$this->getOptions('title');
            $data['system']['keywords']=$this->getOptions('keywords');
            $data['system']['description']=$this->getOptions('description');
            $data['system']['title']=$this->getOptions('title');
            $data['ismobile']=Request::instance()->isMobile();
            return view('open',[
                'data'=>$data,
            ]);
        }
        
    }
    public function serachInfo(){
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
        if (Request::instance()->get()) {
            # code...
            $data=Request::instance()->get('data');
            $data=json_decode($data,true);
            $dbContents=Db::name('contents')->where($data['type'],'like','%'.$data['content'].'%');
            $back['data']='';
            foreach ($dbContents->select() as $res) {
                # code...
                $back['data']="<a style='display:block;overflow:hidden;word-break:keep-all;white-space:nowrap;text-overflow:ellipsis;' href='/article?id=".$res['id']."'>".$res['text']."</a><hr><br>".$back['data'];
            }
            $back['code']=1;
            return json($back);
            // for ($i=0; $i < $dbContents->count(); $i++) { 
            //     # code...
            //     $back['data']
            // }
        }
        $request=Request::instance();
        $data['system']['icp']=$this->getOptions('icp');
        $data['system']['cnzz']=$this->getOptions('cnzz');
        $data['system']['name']=$this->getOptions('name');
        $data['system']['title']='搜索-'.$this->getOptions('title');
        $data['system']['keywords']=$this->getOptions('keywords');
        $data['system']['description']=$this->getOptions('description');
        $data['ismobile']=$request->isMobile();
        return view('serachInfo',[
            'data'=>$data,
        ]);
    }
    public function about(){
        $request=Request::instance();
        $data['system']['icp']=$this->getOptions('icp');
        $data['system']['cnzz']=$this->getOptions('cnzz');
        $data['system']['name']=$this->getOptions('name');
        $data['system']['title']='关于-'.$this->getOptions('title');
        $data['system']['keywords']=$this->getOptions('keywords');
        $data['system']['description']=$this->getOptions('description');
        $data['about']=$this->getOptions('page_about');
        $data['ismobile']=$request->isMobile();
        return view('about',[
            'data'=>$data,
        ]);
    }
    public function getOptions($name=''){
        $res=Db::name('options')->where('name',$name)->find();
        return $res['value'];
    }
    public function ajaxInsertData(){
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
        $data[]=array();
        $data=Request::instance()->param();
        $content=Db::name('contents');
        $time['today']['begin']=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $time['today']['end']=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $limit['time']['conf']=Db::name('options')->where('name','limit_time')->find();
        $limit['ip']=Db::name('options')->where('name','limit_ip')->find();
        $limit['time']['res']=Db::name('contents')->order('date','desc')->where('ip',Request::instance()->ip())->find();
        $limit['time']['count']=Db::name('contents')->whereTime('date','between',[$time['today']['begin'],$time['today']['end']])->where('ip',Request::instance()->ip())->count();
        if($limit['ip']['value']<=$limit['time']['count']){
            $back['code']=-1;
            $back['info']='您今日发帖次数已上限';
            return json($back);
            exit();
        }else{
            //计算时间差距
            $limit['time']['value_a']=time()-$limit['time']['res']['date'];
            if($limit['time']['value_a']<=$limit['time']['conf']['value']){
                $back['code']=-1;
                $back['info']='亲~发帖速度不能太快哦！';
                return json($back);
                exit();
            }
        }
        //Cookie::init(['prefix'=>'call_','expire'=>$limitTime['value'],'path'=>'/','domain'=>$_SERVER['HTTP_HOST']]);
            if($data['text']!='' && $data['qq']!='' && $data['name']!='' && preg_match("/^[1-9]\d{4,10}$/",$data['qq'])){
                $limit_mous=Db::name('options')->where('name','limit_mous')->find();
                if($data['mous']==0){
                    $data['ip']=Request::instance()->ip();
                    $data['date']=time();
                    $content->insert($data);
                    Cookie::set('val_a',md5($data['sex']));
                    Cookie::set('val_b',md5($data['name']));
                    Cookie::set('qq',$data['qq']);
                    Cookie::set('submitClick',$data['qq']); 
                    Db::name('website')->where('zid',$data['zid'])->setInc('count',1);
                    $res['code']='1';
                    $res['info']='提交成功';
                    return json($res);
                }
                else if($data['mous']==1){
                    if($limit_mous['value']=='1'){
                        $data['ip']=Request::instance()->ip();
                        $data['date']=time();
                        $content->insert($data);
                        Cookie::set('val_a',md5($data['sex']));
                        Cookie::set('val_b',md5($data['name']));
                        Cookie::set('qq',$data['qq']);
                        Cookie::set('submitClick',$data['qq']); 
                        Db::name('website')->where('zid',$data['zid'])->setInc('count',1);
                        $res['code']='1';
                        $res['info']='提交成功';
                        return json($res);
                    }else{
                        $res['code']='0';
                        $res['info']='管理员已设置“仅实名发帖”！';
                        return json($res);
                    }
                }
            }else{
                $res['status']='0';
                $res['msg']='参数不完整！';
                return json($res);
            }  		
    }

    public function getSystemInfo($valueName){
        $map['name']=$valueName;
        $options=Db::name('options')->where($map)->select();
    }
    public function getNotice(){
        $request=Request::instance();
        $option=$request->param('func');
        if($option=='get'){
            $noticeText=Db::name('notice')->order('time','desc')->find();
            $back['code']=1;
            $back['tid']=$noticeText['tid'];
            $back['info']=$noticeText['content'].'<br/>'.date('Y-m-d H:i:s',$noticeText['time']);
            $back['time']=$noticeText['time'];
            return json($back);
        }
    }
    public function likeSubmit(){
        $post_id=Request::instance()->param('id');
        $post_id_md5=Request::instance()->param('id','','md5');
        $key=43278787;
        $cookie=Cookie::get($post_id);
        if(!isset($cookie) && isset($post_id)){
            Cookie::set($post_id,$post_id_md5.$key,time()+60);
            Db::name('contents')->where('id',$post_id)->setInc('like',1);
            $data['info']="点赞成功！感谢支持~";
            $data['status']='1';
            $res=Db::name('contents')->where('id',$post_id)->find();
            $data['count']=$res['like'];
            return json($data);
        }
        else{
            $cookies=Cookie::get($post_id);
            if($cookies!=$post_id_md5.$key){
                $data['info']="fail";
                $data['status']='2';
                return json($data);
            }else{
                $data['info']="您已经点赞过了哦~";
                $data['status']='0';
                return json($data);
            }
        }
    }
    public function articlePage(){
        
        $post_id=Request::instance()->param('id');
        $num =  Db::name('comment')->where('post_id',$post_id)->count(); //获取评论总数
        $data['ismobile']=Request::instance()->isMobile();
        $data['system']['icp']=$this->getOptions('icp');
        $data['system']['cnzz']=$this->getOptions('cnzz');
        $data['system']['keywords']=$this->getOptions('keywords');
        $data['system']['name']=$this->getOptions('name');
        $data['system']['description']=$this->getOptions('description');
        $postdata=Db::name('contents')->where('id',$post_id)->find();
        $data['list']=array();
        $data['list']=$this->getCommlist($post_id);//获取评论
        return view('comment',[
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
        //echo json_encode($data);
    }
    public function likeComm(){
        $comm_id=Request::instance()->param('id');
        $comm_id_md5=Request::instance()->param('id','','md5');
        $key=43278787;
        $cookie=Cookie::get($comm_id);
        if(!isset($cookie)){
            Cookie::set($comm_id.$key,$comm_id_md5.$key,time()+60);
            $contents=Db::name('comment')->where('id',$comm_id)->setInc('like',1);
            $data['info']="ok";
            $data['status']=1;
            return json($data);
        }
        else{
            $data['info']="error";
            $data['status']=0;
            return json($data);
            }
        }
}
