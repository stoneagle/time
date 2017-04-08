// 批量选择
function batchCheck(batchId, batchListName)
{
    $("#" + batchId).click(function(){
        var isChecked = $(this).prop("checked");
        $("input[name="+batchListName+"]").prop("checked", isChecked);
    });
}

// 验证提交
function checkPost(text_info, hint, href, post_data)
{
    var finish_flag = arguments[4] ? false : true;
    swal({
        title: text_info,                   //弹出框的title
        text: hint,                         //弹出框里面的提示文本
        type: "warning",                    //弹出框类型
        showCancelButton: true,             //是否显示取消按钮
        confirmButtonColor: "#DD6B55",      //确定按钮颜色
        cancelButtonText: "取消",           //取消按钮文本
        confirmButtonText: "是的，确定！",  //确定按钮上面的文档
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(
            function(){     
                $.ajax({
                    url: href,
                    data: post_data,
                    dataType: 'text',
                    type: 'POST',
                    success: function(result) {
                        var data = eval('(' + result + ')');  
                        if (data.error === 0) {
                            if (finish_flag) {
                                swal({
                                    title: "操作成功!",   
                                    text: data.message,  
                                    type: "success",    
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "确定",
                                },function(){
                                    location.reload();
                                });
                            } else {
                                location.reload();
                            }
                        } else {
                            swal("操作失败!", data.message, "error");
                        }
                    },
                    error: function(data) {
                        swal("操作失败!", data.responseText, "error");
                    }
                })
            }, 
            500 
        );
    });
}

function directPost(href, post_data) 
{
    var finish_flag = arguments[2] ? false : true;
    var reload_flag = arguments[3] ? false : true;
    var finish_func_flag = arguments[4] ? true : false;
    var finish_func = arguments[4];
    $.ajax({
        url: href,
        data: post_data,
        dataType: 'text',
        type: 'POST',
        success: function(result) {
            var data = eval('(' + result + ')');  
            if (data.error === 0) {
                if (finish_flag) {
                    swal({
                        title: "操作成功!",   
                        text: data.message,  
                        type: "success",    
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确定",
                    },function(){
                        if (finish_func_flag) finish_func(data);
                        if (reload_flag) location.reload();
                    });
                } else {
                    if (finish_func_flag) finish_func(data);
                    if (reload_flag) location.reload();
                }
            } else {
                swal("操作失败!", data.message, "error");
            }
        },
        error: function(data) {
            swal("操作失败!", data.message, "error");
        }
    })
}
