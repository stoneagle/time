$(function(){
    $("[name='delete_one']").on('click', function (e) {
        e.preventDefault();
        href = $(this).attr("href");
        var post_data = {
            'ids' : $(this).attr("model_id")
        };
        var text_info = "是否删除该技能";
        var hint = "该技能删除后，相关技能内容会全被删除";
        checkPost(text_info, hint, href, post_data);
    });
});
