function submit_data(){
    var formData = $("#datainfo").serialize(); 
    $.ajax({  
            type:"post",  
            url:"{:url('Index/ajaxInsertData')}",  
            data:formData,
            success:function(data){  
                if (data=='0') {
                    alert('表白成功！自动跳转首页');
                    window.location.href='/';
                }
                if (data=='1') {
                    alert('请填写内容');
                    document.getElementById('text').focus();
                    return false;
                }
                if(data=='2' || data=='3'){
                    alert('请检查QQ');
                    document.getElementById('qq').focus();
                    return false;
                }
                if(data='4'){
                    alert('请填写昵称');
                    document.getElementById('name').focus();
                    return false;
                }
            }
        });  

}