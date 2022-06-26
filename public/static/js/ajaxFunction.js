var ajaxUrl=new Array();
ajaxUrl['login_logout_option']="//"+document.domain+"/admin/Option/logout";
ajaxUrl['login_login_page']="//"+document.domain+"/admin/Option/loginActivity";
ajaxUrl['login_login_option']="//"+document.domain+"/admin/Option/checklogin";
ajaxUrl['admin_index_page']="//"+document.domain+"/admin/index";
ajaxUrl['admin_setting_option']="//"+document.domain+"/admin/index/ajaxSetSave";
ajaxUrl['admin_index_getlogintime']="//"+document.domain+"/admin/Option/getLogintime";
ajaxUrl['admin_edit_page']="//"+document.domain+"/admin/index/eidtPage";
function adduser(){
    layer.prompt(
      function(val,vals, index){
      layer.msg('得到了'+val);
      layer.close(index);
    },
  );
}
function submitData(){
  var name=$("#username").val();
  var pass=$("#password").val();
  if(name==''){
    layer.msg('请输入账号', {icon: 5});
    $("#username").focus();
    return false;
  }
  if(pass==''){
    layer.msg('请输入账号', {icon: 5});
    $("#username").focus();
    return false;
  }
  $.post(ajaxUrl['login_login_option'],{
    username:name,
    password:pass,
  },function(data){
    if(data!='1'){
      layer.msg('账号获密码错误', {icon: 5});
    }else{
      layer.msg('登陆成功', {
        icon: 6,
        time: 2000,
      },function(index){
        location.href="//"+document.domain+"/admin/index";
        layer.close(index);
      });
    }
  });
}
function logout(){
  $.get(ajaxUrl['login_logout_option'],function(data){
        layer.msg('注销成功', {
          icon: 6,
          time: 2000,
      },function(index){
        location.href=ajaxUrl['login_login_page'];
        layer.close();
      });
      async:false
  });
}
function getCookie(cname){
var name = cname + "=";
var ca = document.cookie.split(';');
for(var i=0; i<ca.length; i++) {
  var c = ca[i].trim();
  if (c.indexOf(name)==0) { return c.substring(name.length,c.length); }
}
return "";
}
if(getCookie('windowShow')!='1'){
  $.ajax({ 
    url: ajaxUrl['admin_index_getlogintime'], 
    type:"GET",
    dataType:"json",
    success: function(data){
      layer.open({
      type: 1,
      title:'温馨提示！'
      ,offset: 'rb' //具体配置参考：http://www.layui.com/doc/modules/layer.html#offset
      ,id: 'layerDemo'+'rb' //防止重复弹出
      ,content: '<div style="padding: 20px 30px;">'+ '上次登陆时间:' + data['login_time']+'<br/>'+'上次登陆IP：'+data['login_ip']+'<br/>'+'本次登陆：'+returnCitySN['cip']+'&'+returnCitySN['cname']+'</div>'
      ,btn: '修改密码'
      ,btnAlign: 'c' //按钮居中
      ,shade: 0 //不显示遮罩
      ,yes: function(){
        layer.closeAll();
        layer.prompt({
          title:"输入新密码",
          formType:1,
          maxlength:50,
        },function(val, index){
          $.ajax({
            url: "{:url('Option/changePassword')}", 
            type:"POST",
            data:"newpassword="+val,
            success:function(data){
              layer.msg(data)
            }
          })
          layer.close(index);
      });
      }
    });
      document.cookie="windowShow=1"
  }});
}
$('.postedit').on('click',function(){
  var Oa=$(this);
  var id=Oa.attr('id');//获取id属性
  window.location.href=ajaxUrl['admin_edit_page']+"?id="+id;
});
function toHome(){
  location.href=ajaxUrl['admin_index_page'];
}

function ajaxSetSave(){
  var icp=$("#icp").val();
  var ccid=$("#ccid").val();
  var title=$("#title").val();
  var keywords=$("#keywords").val();
  var limit_time=$("#limit_time").val();
  var limit_mous=$('input:radio:checked').val();
  var description=$("#description").val();
  $.post(ajaxUrl['admin_setting_option'],{
    title:title,
    keywords:keywords,
    limit_mous:limit_mous,
    limit_time:limit_time,
    description:description,
    icp:icp,
    ccid:ccid
  },function(data){
      if(data.status==1){
        layer.msg('修改成功',{icon:6});
      }else{
        layer.msg(data.info,{icon:5});
      }
  },"json")
}
document.body.innerHTML='';