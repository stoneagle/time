$(function(){
    $("[name='delete_one']").on('click', function (e) {
        e.preventDefault();
        href = $(this).attr("href");
        var post_data = {
            'ids' : $(this).attr("model_id")
        };
        var text_info = "是否删除该配置";
        var hint = "该配置删除后，项目中使用相关配置的内容都会被标记删除";
        checkPost(text_info, hint, href, post_data);
    });
});
