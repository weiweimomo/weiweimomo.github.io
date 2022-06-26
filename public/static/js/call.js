var ajaxsuburl='//'+window.location.host+'/index/index/ajaxInsertData';
function submit_data(){
    var text=$('#text').val();
    var qq=$('#qq').val();
    var name=$('#name').val();
    var sex=$('input:radio[name="sex"]:checked').val();
    var mous=$('input:radio[name="mous"]:checked').val();
    var zid=$.cookie('zid');
    var reg = new RegExp("[\\u4E00-\\u9FFF]+","g");

if (text=='') {
        layer.msg('请填写内容~',{icon:2});
        $('#text').focus();
        return false;
    }else{
        if(reg.test(text)){ 
            // $.ajax({
            //     url:'https://api.kres.cn/index/filter'
            //     ,data:{content:text}
            //     ,dataType:'JSON'
            //     ,type:'GET'
            //     ,success:function(data){
            //         if (data.code==0) {
            //             layer.alert(data.data,{icon:2},function(index){
            //                 $('#text').val('');
            //                 $('#text').focus();
            //                 layer.msg('违规内容！\n系统已清空内容，请规范重写!');
            //                 layer.close(index);
            //             });
            //             return false;
            //         }else{
            //             submit(text,name,qq,sex,mous,zid);
            //         }
            //     }
            // });
            submit(text,name,qq,sex,mous,zid);
            }else{
                layer.msg('请包含中文~',{icon:2});
                return false;
            }
        
    }
    if (qq=='') {
        layer.msg('请填写Q Q~',{icon:2});
        $('#qq').focus();
        return false;
    }
    if (name=='') {
        layer.msg('请填写昵称~',{icon:2});
        $('#name').focus();
        return false;
    }
    layer.msg('发送中...', {
      icon: 16
      ,shade: 0.01
    });
function submit(text,name,qq,sex,mous,zid){
    $.post(ajaxsuburl,{
        text:text,
        name:name,
        qq:qq,
        sex:sex,
        mous:mous,
        zid:zid,
    },function(data){
        if (data.code=='1') {
            layer.alert(data.info,{icon:1},function(index){
                location.href="//"+window.location.host+"/index/school/indexpage/zid/"+$.cookie('zid');
            });
        }
        if (data.code=='0') {
            layer.msg(data.info,{icon:2});
        }
        if (data.code=='-1') {
            layer.msg(data.info,{icon:4});
        }
    },'json');
}
}
$('#qq').click(function(){
    layer.tips('不匿名时用于显示头像', '#h2qq', {
    tips: [1, '#F4A7B9'] ,
    });
});
$('#name').click(function(){
    layer.tips('不匿名时用于显示昵称', '#h2name', {
    tips: [1, '#F4A7B9'],
    });
});